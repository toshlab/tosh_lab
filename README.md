# toshLab

Laravelエンジニア「tosh」のポートフォリオサイトです。
業務システムの開発・既存システムの改修・サーバー運用・ホームページ制作のご相談を受け付けています。

- サイト：https://toshlab.jp （公開準備中）
- 相談窓口：Threads [@yuru_saka_se](https://www.threads.net/@yuru_saka_se)

## 主な機能

| 機能 | 内容 |
|---|---|
| トップページ | 実績・サービスと料金・プロフィール・スキル・FAQ・お問い合わせの1ページ構成 |
| 実績 | 「サンプル」「提案デモ」「制作実績」をラベルで区別して表示。詳細ページはMarkdownで書く |
| ブログ | Markdownで執筆。下書き・予約投稿に対応 |
| お問い合わせ | DBに保存して管理者へ通知メール。おとり欄と送信回数の制限でスパム対策 |
| 管理画面 | Filamentで実績・ブログ記事・お問い合わせを管理（`/admin`） |

## 技術スタック

- PHP 8.4 / Laravel 12
- MySQL 8.4
- Blade + Tailwind CSS 4（Vite）
- Filament 5（管理画面）
- PHPUnit（Feature テスト）、Laravel Pint（整形）
- ローカル開発：Docker（Laravel Sail）
- 本番：VPS（nginx + php-fpm）

## 設計で意識したこと

- **処理の置き場所を決めておく**：バリデーションは FormRequest、保存やメール送信などの副作用は `app/UseCases` に分け、Controller を薄く保つ（例：`app/UseCases/Inquiry/StoreAction.php`）
- **失敗しても取りこぼさない**：通知メールの送信に失敗しても、お問い合わせは保存して管理画面から確認できるようにした
- **出力は安全側に**：ブログ本文の Markdown に書かれた生の HTML や `javascript:` リンクは出力しない
- **メッセージは言語ファイルで一元管理**：画面とテストで同じキー（`lang/ja/`）を参照し、文言の二重管理を避ける
- **規約を文書化**：[docs/coding-standards.md](docs/coding-standards.md)

## ローカルでの起動

```shell
cp .env.example .env
docker run --rm -u "$(id -u):$(id -g)" -v "$(pwd):/var/www/html" -w /var/www/html \
    laravelsail/php84-composer:latest composer install
vendor/bin/sail up -d
vendor/bin/sail artisan key:generate
vendor/bin/sail artisan migrate --seed
vendor/bin/sail artisan storage:link
vendor/bin/sail artisan make:filament-user   # 管理画面のユーザー
vendor/bin/sail npm install && vendor/bin/sail npm run build
```

- サイト：http://localhost
- 管理画面：http://localhost/admin

## テスト

```shell
vendor/bin/sail artisan test
vendor/bin/sail pint
```
