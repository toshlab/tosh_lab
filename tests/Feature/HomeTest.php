<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_トップページが表示される(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee(config('site.owner.name'))
            ->assertSee(config('site.threads_url'), false);
    }

    public function test_公開中の実績だけがラベル付きで表示される(): void
    {
        Work::factory()->create(['title' => '公開中の実績', 'label' => 'demo']);
        Work::factory()->unpublished()->create(['title' => '非公開の実績']);

        $this->get('/')
            ->assertSee('公開中の実績')
            ->assertSee(Work::LABELS['demo'])
            ->assertDontSee('非公開の実績');
    }

    public function test_実績は並び順の小さい順に表示される(): void
    {
        Work::factory()->create(['title' => '二番目の実績', 'sort_order' => 20]);
        Work::factory()->create(['title' => '一番目の実績', 'sort_order' => 10]);

        $this->get('/')->assertSeeInOrder(['一番目の実績', '二番目の実績']);
    }

    public function test_公開済みのブログ記事だけが表示される(): void
    {
        Post::factory()->create(['title' => '公開済みの記事']);
        Post::factory()->draft()->create(['title' => '下書きの記事']);
        Post::factory()->scheduled()->create(['title' => '予約中の記事']);

        $this->get('/')
            ->assertSee('公開済みの記事')
            ->assertDontSee('下書きの記事')
            ->assertDontSee('予約中の記事');
    }

    public function test_トップのブログ記事は設定した件数まで(): void
    {
        $count = config('const.home_posts_count');
        Post::factory()->count($count + 1)->create();

        $response = $this->get('/');

        $this->assertCount($count, $response->viewData('posts'));
    }
}
