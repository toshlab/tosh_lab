# ブログ記事を GitHub の Markdown から自動で公開する（計画）

ステータス：**確定（2026-11-18）**。#1 から順に、1段階ずつ実装して報告する。

## 目的

1. `content/posts/` の Markdown（1記事1ファイル）を、ブログの記事として DB に取り込む
2. main に記事のファイルが追加・変更されたら、GitHub Actions でサーバーに反映する
3. 将来、Claude（予約タスク）が記事のファイルを追加するプルリクエストを作り、チェックの成功を確認してからマージ → 公開、の流れにする

## 記事ファイルの形式

- ファイル名：`content/posts/2026-11-20-raid5-raid6-difference.md`（公開日-スラッグ.md）
- 先頭に YAML の front matter、本文は Markdown

```markdown
---
title: タイトル
slug: raid5-raid6-difference
description: meta description（120文字以内）
category: 基本情報の用語解説   # 基本情報の用語解説 / 作業の記録 / ITニュース解説 のどれか
tags: [RAID, 基本情報]
published_at: 2026-11-20 08:00  # 日本時間
status: published               # draft なら取り込むが公開しない
---

本文（Markdown）
```

## 調査結果（2026-11-18 時点）

| 部分 | 状態 |
|---|---|
| テーブル `posts` | title・slug（ユニーク）・category（50）・excerpt（300）・body（Markdown）・published_at（null＝下書き、未来＝予約）。タグの列は無い |
| モデル `Post` | `published()` スコープ、`bodyHtml()`（生の HTML と危険なリンクを無効化）、`excerptText()` |
| 管理画面 | Filament の作成・編集・一覧。カテゴリは自由入力 |
| 表示 | `/blog`、`/blog/{slug}`、トップに最新3件 |
| SEO | title・meta description・og:site_name/title/type/url・canonical。**sitemap.xml・JSON-LD・og:description・og:image は無い** |
| パッケージ | league/commonmark は本番にあり。symfony/yaml は開発用のみ |
| タイムゾーン | アプリが UTC のまま |

## 決定事項

| # | 項目 | 決定 |
|---|---|---|
| 1 | SEO の不足分 | sitemap.xml・Article の JSON-LD・og:description・og:image・robots.txt の sitemap 行を、取り込みより先に作る（#2） |
| 2 | タイムゾーン | `Asia/Tokyo` にする。front matter の日時も日本時間として扱う |
| 3 | サーバーでの実行ユーザー | 取り込み専用の `publisher`（sudo なし）を作る。`authorized_keys` は `restrict,command="…"` 付き |
| 4 | ファイルを消したとき | その記事を下書きに戻す（削除しない） |
| 5 | プルリクエストのチェック | `--dry-run` で取り込みを試すワークフローを作る。非公開リポジトリ・無料プランでブランチの保護は使えないので、**マージする側（Claude の予約タスク）がチェックの成功を確認してからマージする**運用にする |
| 6 | パッケージ | `symfony/yaml` を本番の依存に追加する |

### 取り込みの決めごと

| 項目 | 内容 |
|---|---|
| キー | slug。新規作成または更新（何度実行しても同じ結果） |
| 変更が無いとき | ファイルの中身のハッシュ（`source_hash`）が同じなら何もしない |
| ファイル名と front matter | 日付・slug が食い違ったらスキップ |
| `description` | `excerpt` 列に入れる（120文字以内） |
| `tags` | `posts.tags`（JSON）を追加し、記事のページに表示する |
| 管理画面で作った記事と slug が重なったとき | 上書きしないでスキップ |
| 不足・不正のあるファイル | スキップして理由をログに出す。ほかの記事は続ける。最後に一覧を出し、1件でもあれば終了コード 1 |
| `category` | 3つの候補を `config/blog.php` にまとめ、管理画面も同じ候補から選ぶ |
| 管理画面 | ファイル由来の記事は読み取り専用・削除ボタンなし。「GitHub のファイルで編集してください（パス）」と表示 |
| サーバーでの取得 | `/var/www/toshlab-content` に `content/posts` だけの sparse checkout を作り、そこから取り込む（アプリのコードは取り込まない） |
| キャッシュ | 記事は毎回 DB から読むので、今は消すキャッシュが無い（sitemap をキャッシュするようにしたら、取り込み後に消す） |

## 実装の順番

| # | 実装 | 手で行う作業（オーナー） |
|---|---|---|
| 1 | タイムゾーンを Asia/Tokyo に、symfony/yaml を追加 | テストの実行 |
| 2 | SEO の不足分（og:description・og:image・Article の JSON-LD・sitemap.xml・robots.txt） | テストの実行・表示の確認 |
| 3 | マイグレーション（tags・source・source_path・source_hash）、`config/blog.php`、モデル | マイグレーションの実行（手元） |
| 4 | `posts:import`（`--path`・`--dry-run`）とテスト | テストの実行 |
| 5 | 管理画面の調整 | 画面の確認 |
| 6 | Ansible の役割 `posts_sync`（publisher ユーザー・sparse checkout・取り込みのスクリプト・`authorized_keys`）、`deploy.yml` にも取り込みを追加 | 鍵のペアの作成、`site.yml --tags posts_sync`・`deploy.yml` の実行 |
| 7 | ワークフロー2つ（プルリクエストの dry-run チェック／main へのマージで反映） | GitHub Secrets の登録 |
| 8 | サンプルの記事（draft）で全体の流れを確認 | プルリクエストの作成・マージ、反映の確認 |

## 実装で決めたこと（#6）

- `publisher` は GitHub から取得するための鍵を自分で持つ（deploy の鍵は読めないため）。リポジトリには読み取り専用のデプロイキーをもう1つ登録する
- 本番は設定をキャッシュしている（`artisan optimize`）ため、取り込みのスクリプトでは `APP_CONFIG_CACHE` に存在しないパスを指定し、`LOG_CHANNEL=stderr` を効かせる（publisher は Laravel のログファイルに書き込めないため。ログは GitHub Actions の画面に出る）
- `deploy.yml` の最後にも取り込みを実行する（スキップがあってもデプロイは止めない）

## GitHub Actions（#7）

| ワークフロー | いつ動くか | 何をするか | Secrets |
|---|---|---|---|
| `.github/workflows/posts-check.yml`（ジョブ名 `dry-run`） | `content/posts/**` を変えたプルリクエスト | このジョブの中だけの SQLite で `posts:import --dry-run`。問題のある記事があれば失敗 | 使わない |
| `.github/workflows/posts-publish.yml`（ジョブ名 `import`） | `content/posts/**` が main に入ったとき（手動でも実行できる） | publisher の鍵でサーバーに接続 → サーバー側で取り込みだけが実行される | 下の3つ |

| Secret の名前 | 入れるもの |
|---|---|
| `TOSHLAB_POSTS_SSH_KEY` | 取り込み専用の秘密鍵（Mac の `~/.ssh/toshlab_posts_actions` の中身すべて） |
| `TOSHLAB_POSTS_SSH_HOST` | サーバーの IP アドレス |
| `TOSHLAB_POSTS_SSH_KNOWN_HOSTS` | サーバーのホスト鍵（known_hosts の形の1行）。なりすましのサーバーに接続しないため |

### マージする側（Claude の予約タスクなど）の決まり

ブランチの保護を使えないので、マージする前に必ず次を確かめる。

1. プルリクエストの `posts-check / dry-run` が **success** であること（pending・failure・skipped のときはマージしない）
2. 変更が `content/posts/` の中だけであること（ほかのファイルを含むプルリクエストは、人が確認する）
3. マージしたあと、`posts-publish / import` が success になったことを確かめる。failure なら、Summary の理由を直すプルリクエストを作る

`posts-check` は空の DB で試すので、本番の「管理画面で作った記事との slug の重なり」までは分からない。その場合は本番の取り込みでスキップされ、`posts-publish` が失敗として通知する（管理画面の記事は上書きされない）。

## 守ること

- 方針の提案 → オーナーの確認 → 実装 の順。1段階ずつ止めて報告する
- テスト・マイグレーション・サーバーへの反映は、コマンドを提示するだけで、実行はオーナーが行う
- 秘密鍵・パスワード・トークンをファイルやコミットに含めない。GitHub Secrets に入れるのは、記事の取り込み専用の秘密鍵と接続先の情報だけ
- 記事以外のコードとフロントのビルドは、今までどおり `deploy.yml` で手動
