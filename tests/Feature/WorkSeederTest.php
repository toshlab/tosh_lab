<?php

namespace Tests\Feature;

use App\Models\Work;
use Database\Seeders\WorkSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_空の_d_bに5件が公開順で入り_追加分の値が仕様どおり(): void
    {
        $this->seed(WorkSeeder::class);

        $this->assertSame(
            ['toshlab-site', 'inventory-system', 'factory-visualization-excel', 'soccer-school-theme', 'leave-workflow-gas'],
            Work::published()->pluck('slug')->all(),
        );

        $this->assertSame('/downloads/factory-visualization-demo.xlsx', Work::where('slug', 'factory-visualization-excel')->value('demo_url'));

        $site = Work::where('slug', 'toshlab-site')->first();
        $this->assertSame('このサイト（toshlab.jp）の設計・構築・運用', $site->title);
        $this->assertSame('sample', $site->label);
        $this->assertSame(['Laravel', 'Filament', 'Ansible', 'GitHub Actions'], $site->tech);
        $this->assertSame('https://github.com/toshlab/tosh_lab', $site->github_url);
        $this->assertSame('https://toshlab.jp', $site->demo_url);
        $this->assertSame('Laravel で作ったこのサイトを、サーバーの構築から自動化して運用しています。ブログは GitHub に記事を置くと自動で公開される仕組みです。', $site->summary);
        $this->assertSame(5, $site->sort_order);

        $gas = Work::where('slug', 'leave-workflow-gas')->first();
        $this->assertSame('休暇申請ワークフロー（Google スプレッドシート＋GAS）', $gas->title);
        $this->assertSame('sample', $gas->label);
        $this->assertSame(['Google Apps Script', 'スプレッドシート'], $gas->tech);
        $this->assertSame('Google のサービスだけで、休暇の申請・承認・予定の共有を仕組みにしたデモ。祝日を除いた申請期限や残日数のチェック、同時に操作されても壊れない作り。', $gas->summary);
        $this->assertSame(40, $gas->sort_order);

        $this->assertNull($site->body);
        $this->assertNull($gas->body);
    }

    public function test_同じslugの行があれば再実行しても値を上書きしない(): void
    {
        Work::factory()->create(['slug' => 'toshlab-site', 'title' => '手で書き換えたタイトル']);

        $this->seed(WorkSeeder::class);
        $this->seed(WorkSeeder::class);

        $this->assertSame('手で書き換えたタイトル', Work::where('slug', 'toshlab-site')->value('title'));
        $this->assertSame(1, Work::where('slug', 'toshlab-site')->count());
    }
}
