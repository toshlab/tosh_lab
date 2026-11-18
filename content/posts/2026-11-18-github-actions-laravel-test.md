---
title: GitHub Actions で Laravel のテストを自動化する、最初の一歩
slug: github-actions-laravel-test
description: GitHub Actions で Laravel のテストを自動で動かす方法を、最小の設定ファイルから紹介します。push のたびにテストが走るだけで、レビューもリリースもぐっと安心になります。
category: 作業の記録
tags: [GitHub Actions, Laravel, CI, Git]
published_at: 2026-11-18 21:59
status: published
---

GitHub Actions で Laravel のテストを自動で動かすようにすると、「あ、これ壊してた」に早く気づけるようになるんです。

テストは書いているけど、実行するのは気が向いたときだけ……ってこと、ありませんか？僕も、テストを手元で流すのをつい忘れてしまうことがあります。

そこで便利なのが GitHub Actions（GitHub 上で決まった処理を自動で動かしてくれるしくみ）です。今回は、Laravel のテストを push のたびに自動で動かすための、最小の設定を紹介します。

## CI があると何がうれしいのか

CI（コードを変更するたびに、テストなどを自動で実行するしくみ）があると、こんなメリットがあります。

- テストの実行忘れがなくなる
- プルリクエストの画面で、テストが通ったかひと目で分かる
- 「自分の環境では動いたのに」が減る

サッカーでいうと、試合前に毎回同じウォーミングアップをするようなものだなと思います。決まった準備を毎回やるから、本番で余計なミスが減るんですよね。

## 最小の設定ファイル

リポジトリに `.github/workflows/tests.yml` というファイルを作って、次のように書きます。

    name: tests

    on:
      push:
        branches: [main]
      pull_request:

    jobs:
      test:
        runs-on: ubuntu-latest
        steps:
          - uses: actions/checkout@v4

          - uses: shivammathur/setup-php@v2
            with:
              php-version: '8.3'

          - name: Install dependencies
            run: composer install --no-interaction --prefer-dist

          - name: Prepare .env
            run: |
              cp .env.example .env
              php artisan key:generate

          - name: Run tests
            run: php artisan test

ひとつずつ見ていきますね。

### on：いつ動かすか

`push` で main ブランチに push されたとき、`pull_request` でプルリクエストが作られたり更新されたりしたときに動きます。

### steps：何をするか

1. `actions/checkout` でリポジトリのコードを取ってくる
2. `shivammathur/setup-php` で PHP を用意する（バージョンは本番環境に合わせるのがおすすめです）
3. `composer install` でライブラリを入れる
4. `.env` を作ってアプリケーションキーを生成する
5. `php artisan test` でテストを実行する

これを push するだけで、GitHub の「Actions」タブにテストの結果が出るようになります。

## つまずきやすいポイント

### データベースをどうするか

テストでデータベースを使う場合、いちばん手軽なのは SQLite（ファイルやメモリ上で動く軽いデータベース）を使う方法です。`phpunit.xml` でテスト用のデータベースを SQLite に向けておけば、CI 上でも追加の準備なしで動きます。

本番と同じ MySQL で確かめたい場合は、GitHub Actions の `services` という機能で、テスト中だけ MySQL のコンテナを立ち上げることもできます。最初は SQLite で始めて、必要になったら切り替えるのでも十分だと思います。

### 秘密の値はファイルに書かない

外部APIのキーなどが必要なテストがある場合、設定ファイルに直接書くのはNGです。GitHub の「Secrets」に登録して、`${{ secrets.名前 }}` の形で読み込むようにしましょう。

### 時間がかかるとき

`composer install` は毎回ゼロから実行すると時間がかかります。慣れてきたら、`actions/cache` で Composer のキャッシュを使うと、実行時間を短くできます。

## 次の一歩

テストが自動で動くようになったら、次のようなことにも広げられます。

- Laravel Pint（コードの書き方をそろえるツール）でのチェックを追加する
- プルリクエストで、テストが通らないとマージできないようにする（ブランチ保護ルール）
- テストが通ったら、自動でデプロイする

いきなり全部やろうとせず、まずは「push したらテストが動く」から始めるのが続けやすいなと思います。

## 今日のポイント

GitHub Actions の設定ファイルを1つ置くだけで、Laravel のテストを毎回自動で動かせます。まずは最小の形で始めて、少しずつ育てていきましょう。

ホームページや作業の効率化で困っていることがあれば、気軽に相談してくださいね。
