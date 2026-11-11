<?php

namespace Tests\Feature;

use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkTest extends TestCase
{
    use RefreshDatabase;

    public function test_公開中の実績の詳細ページを開ける(): void
    {
        $work = Work::factory()->create(['body' => '実績の本文です。']);

        $this->get("/works/{$work->slug}")
            ->assertOk()
            ->assertSee($work->title)
            ->assertSee('実績の本文です。');
    }

    public function test_非公開の実績は404になる(): void
    {
        $work = Work::factory()->unpublished()->create();

        $this->get("/works/{$work->slug}")->assertNotFound();
    }

    public function test_本文がある実績だけ一覧から詳細ページへのリンクが出る(): void
    {
        $withBody = Work::factory()->create(['body' => '本文あり']);
        $withoutBody = Work::factory()->create(['body' => null]);

        $this->get('/')
            ->assertSee(route('works.show', $withBody), false)
            ->assertDontSee(route('works.show', $withoutBody), false);
    }
}
