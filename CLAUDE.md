# CLAUDE.md

このファイルは、Claude Code (claude.ai/code) がこのリポジトリのコードを扱う際のガイダンスを提供します。

## プロジェクトの位置づけ

**toshLab** は、副業で案件を獲得するためのポートフォリオサイト（`shuttttttto.com`）。Threads（@yuru_saka_se）から流入した人が、実績・サービス・料金を見て相談できるようにすることが目的。

- 打ち出し方は**業務システム寄り**。キャッチコピーは「Excelや手作業の業務を、使いやすいシステムに。」
- サイト自体とこのリポジトリのコードが、「Laravelで業務システムを作れる」ことの実績を兼ねる。**読まれる前提のコード**として、規約・テスト・コメントの質を保つ
- 対象のお客さんは4種類：業務を効率化したい会社／制作会社・エンジニア／中小企業・個人事業主／スポーツスクール・クラブ

### 最重要：公開リポジトリであること

このリポジトリはGitHubで公開する。**実名・勤務先・本業のクライアント名・個人のメールアドレスを、コード・コメント・ドキュメントに書かない**。表示名は活動名「tosh（トッシュ）」、ロゴは「toshLab」で、値は `config/site.php` に集約している。本業の経験を載せる場合も、社名・クライアント名は伏せる。

### コーディング規約

@docs/coding-standards.md

## 機能の構成

| 機能 | 内容 | 主なファイル |
|---|---|---|
| トップページ | 1ページ構成（FOR YOU／実績／サービス・料金／プロフィール／実務での経験／スキル／ブログ／FAQ／お問い合わせ） | `HomeController`, `resources/views/home.blade.php` |
| 実績 | 一覧はトップに表示、本文がある実績だけ詳細ページを持つ。ラベルは `Work::LABELS`（サンプル／提案デモ／制作実績） | `Work`, `WorkController`, `works/show.blade.php` |
| ブログ | 本文はMarkdown。`published_at` が null なら下書き、未来なら予約投稿 | `Post`, `PostController`, `posts/*.blade.php` |
| お問い合わせ | DBに保存し、`SITE_ADMIN_EMAIL` に通知メール。おとり欄（`website`）と回数制限でスパム対策 | `Inquiry`, `StoreInquiryRequest`, `app/UseCases/Inquiry/` |
| 管理画面 | Filament（`/admin`）。実績・ブログ・お問い合わせを管理する | `app/Filament/` |

### デザイン

- デザインは確定済み（Aのレイアウト×Bの配色）。色とフォントは `resources/css/app.css` の `@theme` に定義したトークン（`paper`, `band`, `ink`, `body`, `muted`, `line`, `accent` など）を使い、色コードを直接書かない
- フォントは Noto Sans JP。最大幅は1120px、セクション間は `pt-24`
- 実案件とサンプルは、ラベルで必ず区別して表示する（実案件でないものを実案件に見せない）

## 開発コマンド

Docker（Sail）前提。PHPは **8.4**、DBは **MySQL 8.4**（本番のConoHa VPSに合わせる）。

```shell
vendor/bin/sail up -d                       # コンテナ起動
vendor/bin/sail artisan migrate --seed      # マイグレーション＋実績の初期データ
vendor/bin/sail artisan make:filament-user  # 管理画面のユーザーを作成（seedでは作らない）
vendor/bin/sail npm install                 # フロント依存
vendor/bin/sail composer dev                # 開発サーバ一括起動
```

- `composer dev` は `concurrently` で server／queue／logs（pail）／vite を同時起動する
- 画面：`http://localhost`、管理画面：`http://localhost/admin`
- 別プロジェクト（wordpress-practice）のコンテナが8081番ポートを使っていることがある

### テスト・整形

```shell
vendor/bin/sail artisan test                          # 全テスト（PHPUnit）
vendor/bin/sail artisan test --filter=InquiryTest     # 単一テストクラス/メソッド
vendor/bin/sail pint                                  # コード整形（Laravel Pint）
```

- テストは PHPUnit（`phpunit.xml`）。`Unit` と `Feature` の2 suite

## 本番環境

- ConoHa VPS（メモリ2GB／CPU 3コア）。nginx + php-fpm + MySQL。構築はAnsibleで自動化する予定
- 同じVPSに、デモアプリ（在庫管理システムなど）をサブドメインで同居させる予定。メモリに余裕がないので、重い常駐プロセスを増やさない

## 環境・ロケール

- ロケールは日本語既定（`.env`：`APP_LOCALE=ja`、`APP_FAKER_LOCALE=ja_JP`）。翻訳は `lang/ja/`
- フロントは Blade + Tailwind CSS 4（Vite ビルド）。管理画面は Filament

## CLI の共通ルール

- コミット・push・タグ付けはしない（git 操作は人が行う）
- 不明点は推測せず質問する
- 実装したらテストを書き、`vendor/bin/sail artisan test` で実行して結果を確認する。失敗したまま完了扱いにしない。テスト結果を推測で断定しない
- `migrate:fresh`・`migrate:rollback` など破壊的な操作は、実行前に確認する
