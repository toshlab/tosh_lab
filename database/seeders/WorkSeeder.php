<?php

namespace Database\Seeders;

use App\Models\Work;
use Illuminate\Database\Seeder;

/**
 * 公開時点の実績3件。デモURL・GitHub・画像は管理画面（/admin）から追加する。
 * すでに同じslugがあれば上書きしない。
 */
class WorkSeeder extends Seeder
{
    public function run(): void
    {
        $works = [
            [
                'slug' => 'inventory-system',
                'title' => '在庫管理システム',
                'label' => 'sample',
                'summary' => 'Excelの在庫管理をWeb化する想定で制作。入出庫の履歴・CSV出力・在庫アラート。',
                'tech' => ['Laravel', '業務システム'],
                'sort_order' => 10,
            ],
            [
                'slug' => 'factory-visualization-excel',
                'title' => '工場の作業時間・利益の見える化ツール',
                'label' => 'demo',
                'summary' => '作業ログを入力すると、工程ごとの平均時間と商品ごとの利益を自動で集計。マクロを使わず、社内の人が直せる作り。',
                'tech' => ['Excel', '製造業向け'],
                'sort_order' => 20,
            ],
            [
                'slug' => 'soccer-school-theme',
                'title' => 'パーソナルサッカースクールのサイト',
                'label' => 'sample',
                'summary' => 'スクールサイトを想定して制作したオリジナルテーマ。体験申込フォーム・コース紹介・料金ページ、表示速度とSEOの基本設定まで。',
                'tech' => ['WordPress', 'オリジナルテーマ'],
                'github_url' => 'https://github.com/toshlab/fields-wp',
                'sort_order' => 30,
            ],
        ];

        foreach ($works as $work) {
            Work::firstOrCreate(
                ['slug' => $work['slug']],
                $work + ['is_published' => true],
            );
        }
    }
}
