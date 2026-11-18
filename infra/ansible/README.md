# toshlab のサーバー構築（Ansible）

ConoHa VPS（Ubuntu 24.04、メモリ2GB）に、toshlab を本番公開するための手順。
何度実行しても同じ状態になるように書いている。

## 構成

| 役割 | 内容 |
|---|---|
| common | パッケージの更新、タイムゾーン |
| security | SSH（鍵のみ・root禁止）、ufw（22/80/443のみ）、fail2ban、自動のセキュリティ更新 |
| swap | スワップ2GB |
| php | PHP 8.4（ondrej/php）、PHP-FPM（ondemand）、Composer |
| mysql | MySQL、アプリ用のDBとユーザー（メモリ少なめの設定） |
| nginx | nginx の共通設定 |
| app | GitHub のデプロイキー、nginx のサイト設定、アプリの配置（`deploy.yml` と共通） |
| tls | Let's Encrypt（HTTPS への転送つき） |

サーバーには Node.js を入れず、フロント（`public/build`）は Mac でビルドして送る。

## 公開しないファイル

`inventory.ini`（IPアドレス）と `group_vars/all/vault.yml`（パスワード類）は `.gitignore` 済み。`*.example.*` をコピーして使う。

## 手順

```shell
cd infra/ansible
cp inventory.example.ini inventory.ini                         # IPアドレスを書く
cp group_vars/all/vault.example.yml group_vars/all/vault.yml   # 値を書く
ansible-vault encrypt group_vars/all/vault.yml

ansible-playbook bootstrap.yml -e ansible_user=root            # 1回目だけ
ansible-playbook site.yml --ask-vault-pass                     # サーバー全体
```

更新するとき：

```shell
vendor/bin/sail npm run build                                  # プロジェクトのルートで
cd infra/ansible && ansible-playbook deploy.yml --ask-vault-pass
```

管理画面のユーザーは、サーバーで作る：

```shell
ssh deploy@<IPアドレス> -i ~/.ssh/conoha_toshlab
cd /var/www/toshlab && php artisan make:filament-user
```

## バックアップ

`roles/backup` が、毎日 3:30 ごろにサーバーの中へバックアップを取る（14日分を残す）。

| 内容 | 場所（サーバー） |
|---|---|
| データベース（mysqldump） | `/var/backups/toshlab/db/toshlab-YYYYMMDD-HHMM.sql.gz` |
| アップロードしたファイル（`storage/app/public`） | `/var/backups/toshlab/files/storage-YYYYMMDD-HHMM.tar.gz` |

サーバー自体が壊れたときに備えて、ときどき Mac に取ってくる（`~/Backups/toshlab/` に保存。お問い合わせの個人情報を含むので共有しない）：

```shell
ansible-playbook fetch-backups.yml --ask-vault-pass
```

すぐに取りたいとき・状態を見たいとき（サーバーで）：

```shell
sudo systemctl start toshlab-backup.service     # 今すぐ実行
journalctl -u toshlab-backup.service -n 20      # 結果（「backup ok: …」が出ていれば成功）
systemctl list-timers toshlab-backup.timer      # 次に実行される時刻
```

### 戻すとき（サーバーで）

```shell
# データベース（今の内容は上書きされる）
gunzip -c /var/backups/toshlab/db/toshlab-YYYYMMDD-HHMM.sql.gz | sudo mysql toshlab

# アップロードしたファイル
sudo tar -xzf /var/backups/toshlab/files/storage-YYYYMMDD-HHMM.tar.gz -C /var/www/toshlab/storage/app
sudo chown -R deploy:www-data /var/www/toshlab/storage/app/public
```

## ブログ記事の取り込み（roles/posts_sync）

`content/posts/` の Markdown を、GitHub Actions からの合図でブログの記事として取り込む（計画は `docs/blog-import-plan.md`）。

| もの | 内容 |
|---|---|
| ユーザー `publisher` | 取り込み専用。sudo なし。`.env` を読むために `www-data` のグループだけに入る |
| `/var/www/toshlab-content` | 記事専用のクローン（`content/posts` だけの sparse checkout）。アプリのコードは取り込まない |
| `/usr/local/bin/toshlab-import-posts` | クローンを main にそろえて `posts:import` を実行する（root の持ち物で、publisher は書き換えられない） |
| `~publisher/.ssh/authorized_keys` | GitHub Actions の鍵1つだけ。`restrict,command="/usr/local/bin/toshlab-import-posts"` で、このスクリプトしか実行できない |
| `~publisher/.ssh/github_content` | GitHub から記事を取得する鍵（リポジトリの読み取り専用のデプロイキーとして登録する） |

初回だけ：

```shell
# Mac で、GitHub Actions が使う鍵を作る（秘密鍵は GitHub Secrets にだけ置く）
ssh-keygen -t ed25519 -C "toshlab-posts-actions" -N "" -f ~/.ssh/toshlab_posts_actions

# サーバーに反映（途中で、GitHub に登録するデプロイキーの公開鍵が表示されて止まる）
ansible-playbook site.yml --ask-vault-pass --tags posts_sync
```

手で取り込むとき：GitHub Actions と同じ鍵で接続すると、送ったコマンドに関係なく取り込みだけが実行される。

```shell
ssh -i ~/.ssh/toshlab_posts_actions publisher@<IPアドレス>
```

`deploy.yml` を実行したときも、最後に記事を取り込み直す（スキップしたファイルがあってもデプロイは止めない）。
