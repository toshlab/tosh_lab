<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostTest extends TestCase
{
    use RefreshDatabase;

    public function test_記事一覧に公開済みの記事が新しい順で表示される(): void
    {
        Post::factory()->create(['title' => '古い記事', 'published_at' => now()->subDays(2)]);
        Post::factory()->create(['title' => '新しい記事', 'published_at' => now()->subDay()]);

        $this->get('/blog')
            ->assertOk()
            ->assertSeeInOrder(['新しい記事', '古い記事']);
    }

    public function test_公開済みの記事を開ける(): void
    {
        $post = Post::factory()->create(['body' => "## 見出し\n\n本文です。"]);

        $this->get("/blog/{$post->slug}")
            ->assertOk()
            ->assertSee($post->title)
            ->assertSee('<h2>見出し</h2>', false);
    }

    public function test_下書きの記事は404になる(): void
    {
        $post = Post::factory()->draft()->create();

        $this->get("/blog/{$post->slug}")->assertNotFound();
    }

    public function test_予約投稿の記事は公開日時になるまで404で_過ぎたら表示される(): void
    {
        $this->travelTo(now()->startOfDay());
        $post = Post::factory()->create(['published_at' => now()->addHour()]);

        $this->get("/blog/{$post->slug}")->assertNotFound();

        $this->travel(2)->hours();

        $this->get("/blog/{$post->slug}")->assertOk();
    }

    public function test_本文に書かれた_htm_lやスクリプトは出力しない(): void
    {
        $post = Post::factory()->create([
            'body' => "<script>alert('x')</script>\n\n[リンク](javascript:alert(1))",
        ]);

        $this->get("/blog/{$post->slug}")
            ->assertOk()
            ->assertDontSee("<script>alert('x')</script>", false)
            ->assertDontSee('javascript:alert(1)', false);
    }
}
