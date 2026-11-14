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
