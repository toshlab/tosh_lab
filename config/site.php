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

    // お問い合わせの通知先（公開ページには表示しない）
    'admin_email' => env('SITE_ADMIN_EMAIL'),

];
