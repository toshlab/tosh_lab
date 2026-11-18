<?php

return [

    /*
    | サイト全体で使う表示用の設定。
    | 実名・勤務先が分かる情報はここにも書かない。
    */

    'name' => 'toshLab',

    'owner' => [
        'name' => 'tosh',
        'kana' => 'トッシュ',
        'role' => 'サッカー選手／Webエンジニア',
    ],

    'threads_url' => env('SITE_THREADS_URL', 'https://www.threads.net/@yuru_saka_se'),
    'threads_handle' => '@yuru_saka_se',

    'github_url' => env('SITE_GITHUB_URL'),

    // SNSでシェアされたときの画像（public/ からのパス、1200×630）。元は docs/design/og-default.html
    'og_image' => 'images/og-default.png',
    'og_image_width' => 1200,
    'og_image_height' => 630,

    // ページ単体の説明が無いときの meta description
    'description' => '実務4年目のLaravelエンジニア「tosh」のポートフォリオ。Excelや手作業の業務を使いやすいシステムに。業務システム開発、既存システムの改修・移行、サーバー構築・保守、ホームページ制作のご相談を受け付けています。',

    // お問い合わせの通知先（公開ページには表示しない）
    'admin_email' => env('SITE_ADMIN_EMAIL'),

];
