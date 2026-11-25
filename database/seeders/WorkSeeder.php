<?php

namespace Database\Seeders;

use App\Models\Work;
use Illuminate\Database\Seeder;

/**
 * 公開時点の実績5件。本文・画像は管理画面（/admin）から追加する。
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
                'demo_url' => '/downloads/factory-visualization-demo.xlsx',
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
            [
                'slug' => 'toshlab-site',
                'title' => 'このサイト（toshlab.jp）の設計・構築・運用',
                'label' => 'sample',
                'summary' => 'Laravel で作ったこのサイトを、サーバーの構築から自動化して運用しています。ブログは GitHub に記事を置くと自動で公開される仕組みです。',
                'tech' => ['Laravel', 'Filament', 'Ansible', 'GitHub Actions'],
                'github_url' => 'https://github.com/toshlab/tosh_lab',
                'demo_url' => 'https://toshlab.jp',
                'sort_order' => 5,
            ],
            [
                'slug' => 'leave-workflow-gas',
                'title' => '休暇申請ワークフロー（Google スプレッドシート＋GAS）',
                'label' => 'sample',
                'summary' => 'Google のサービスだけで、休暇の申請・承認・予定の共有を仕組みにしたデモ。祝日を除いた申請期限や残日数のチェック、同時に操作されても壊れない作り。',
                'tech' => ['Google Apps Script', 'スプレッドシート'],
                'sort_order' => 40,
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
