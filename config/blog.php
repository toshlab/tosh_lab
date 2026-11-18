<?php

/*
| ブログの設定（docs/blog-import-plan.md）
*/

return [

    // カテゴリの候補。記事のファイルの front matter も、管理画面の選択肢も、ここから選ぶ
    'categories' => [
        '基本情報の用語解説',
        '作業の記録',
        'ITニュース解説',
    ],

    // 記事のファイル（*.md）を置くフォルダ。サーバーでは記事専用のクローン（/var/www/toshlab-content/content/posts）を指す
    // .env に空のまま書いたときも既定のフォルダを使う（env() は空文字を返すため ?: にしている）
    'content_path' => env('BLOG_CONTENT_PATH') ?: base_path('content/posts'),

    // front matter の description（meta description になる）の最大文字数
    'description_max' => 120,

    // front matter の published_at の書き方とタイムゾーン（日本時間）
    'published_at_format' => 'Y-m-d H:i',
    'published_at_timezone' => 'Asia/Tokyo',

];
