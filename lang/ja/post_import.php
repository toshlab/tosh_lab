<?php

/*
| ブログ記事の取り込み（php artisan posts:import）のメッセージ
*/

return [

    'attributes' => [
        'title' => 'title',
        'slug' => 'slug',
        'description' => 'description',
        'category' => 'category',
        'tags' => 'tags',
        'tags.*' => 'tags の値',
        'published_at' => 'published_at',
        'status' => 'status',
    ],

    'reasons' => [
        'filename' => 'ファイル名が「公開日-スラッグ.md」（例：2026-11-20-raid5-raid6-difference.md）の形ではありません。',
        'unreadable' => 'ファイルを読めませんでした。',
        'no_front_matter' => '先頭に front matter（--- で囲んだ YAML）がありません。',
        'invalid_yaml' => 'front matter の YAML を読めませんでした（:message）。',
        'not_mapping' => 'front matter は「項目名: 値」の形で書いてください。',
        'published_at_format' => 'published_at は「YYYY-MM-DD HH:MM」の形で書いてください（例：2026-11-20 08:00）。',
        'empty_body' => '本文がありません。',
        'slug_mismatch' => 'ファイル名のスラッグ（:file）と、front matter の slug（:slug）が違います。',
        'date_mismatch' => 'ファイル名の日付（:file）と、published_at の日付（:date）が違います。',
        'slug_taken_by_admin' => '管理画面で作った記事と slug（:slug）が重なっています。管理画面の記事は上書きしません。',
        'slug_duplicated' => '同じ slug（:slug）の記事のファイルが、ほかにもあります（:other）。',
    ],

    'actions' => [
        'created' => '新規作成',
        'updated' => '更新',
        'unchanged' => '変更なし',
        'unpublished' => '下書きに戻した（ファイルが無くなったため）',
        'skipped' => 'スキップ',
    ],

    'policy' => [
        'update_denied' => 'GitHub のファイルから取り込んだ記事は、管理画面では編集できません。GitHub のファイルを編集してください。',
        'delete_denied' => 'GitHub のファイルから取り込んだ記事は削除できません。元のファイルが無くなって下書きに戻った記事だけ削除できます。',
    ],

    'path_not_found' => '記事のフォルダが見つかりません：:path',
    'dry_run' => '確認だけのモードです（--dry-run）。データベースには書き込んでいません。',
    'summary' => '新規作成 :created件・更新 :updated件・変更なし :unchanged件・下書きに戻した :unpublished件・スキップ :skipped件',

];
