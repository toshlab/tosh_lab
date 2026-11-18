<?php

namespace Tests\Feature;

use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkTest extends TestCase
{
    use RefreshDatabase;

    public function test_公開中で本文がある実績の詳細ページを開ける(): void
    {
        $work = Work::factory()->create(['body' => '実績の本文です。']);

        $this->get("/works/{$work->slug}")
            ->assertOk()
            ->assertSee($work->title)
            ->assertSee($work->summary)
            ->assertSee('実績の本文です。');
    }

    public function test_本文が空でも概要があれば詳細ページを開けて概要と技術が出る(): void
    {
        $work = Work::factory()->create([
            'summary' => '在庫の管理をWeb化する想定のサンプル。',
            'body' => null,
            'tech' => ['Laravel', '業務システム'],
        ]);

        $this->get("/works/{$work->slug}")
            ->assertOk()
            ->assertSee('在庫の管理をWeb化する想定のサンプル。')
            ->assertSee('Laravel / 業務システム')
            ->assertDontSee('class="prose-post"', false);
    }

    public function test_概要も本文も空白だけの実績は詳細ページが404になる(): void
    {
        $blanks = [['', null], ["  \n ", ''], ['　', "  \n\t "]];
        foreach ($blanks as $i => [$summary, $body]) {
            $work = Work::factory()->create(['slug' => "blank-{$i}", 'summary' => $summary, 'body' => $body]);

            $this->get("/works/{$work->slug}")->assertNotFound();
        }
    }

    public function test_概要が空でも本文があれば詳細ページを開ける(): void
    {
        $work = Work::factory()->create(['summary' => '', 'body' => '本文だけの実績']);

        $this->get("/works/{$work->slug}")->assertOk()->assertSee('本文だけの実績');
    }

    public function test_非公開の実績は404になる(): void
    {
        $work = Work::factory()->unpublished()->create();

        $this->get("/works/{$work->slug}")->assertNotFound();
    }

    public function test_詳細ページがある実績だけ一覧から詳細ページへのリンクが出る(): void
    {
        $withSummary = Work::factory()->create(['body' => null]);
        $blank = Work::factory()->create(['summary' => ' ', 'body' => null]);

        $this->get('/')
            ->assertSee(route('works.show', $withSummary), false)
            ->assertDontSee(route('works.show', $blank), false);
    }

    public function test_hasdetailpageとwithdetailpageの判定が一致する(): void
    {
        $cases = [
            ['概要', null], ['', '本文'], ['', null], ["  \n ", "\t"], ['　', null], [' x ', ' '],
        ];
        foreach ($cases as $i => [$summary, $body]) {
            Work::factory()->create(['slug' => "case-{$i}", 'summary' => $summary, 'body' => $body]);
        }
        Work::factory()->unpublished()->create(['slug' => 'hidden']);

        $byModel = Work::all()->filter->hasDetailPage()->pluck('slug')->sort()->values()->all();
        $byQuery = Work::withDetailPage()->pluck('slug')->sort()->values()->all();

        $this->assertSame(['case-0', 'case-1', 'case-5'], $byModel);
        $this->assertSame($byModel, $byQuery);
    }
}
