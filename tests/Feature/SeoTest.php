<?php

namespace Tests\Feature;

use App\Models\Post;
use App\Models\Work;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_トップにog_descriptionとog_imageとtwitter_cardが出る(): void
    {
        $this->get('/')
            ->assertOk()
            ->assertSee('<meta property="og:description" content="'.e(config('site.description')).'">', false)
            ->assertSee('<meta property="og:image" content="'.asset(config('site.og_image')).'">', false)
            ->assertSee('<meta property="og:type" content="website">', false)
            ->assertSee('<meta name="twitter:card" content="summary_large_image">', false);
    }

    public function test_og_imageの画像ファイルがある(): void
    {
        $this->assertFileExists(public_path(config('site.og_image')));
    }

    public function test_記事のページはog_typeがarticleで抜粋がog_descriptionになる(): void
    {
        $post = Post::factory()->create(['excerpt' => '記事の抜粋です。']);

        $this->get("/blog/{$post->slug}")
            ->assertSee('<meta property="og:type" content="article">', false)
            ->assertSee('<meta property="og:description" content="記事の抜粋です。">', false)
            ->assertSee('<meta property="article:published_time" content="'.$post->published_at->toIso8601String().'">', false);
    }

    public function test_記事のページにblog_postingのjson_ldが出る(): void
    {
        $post = Post::factory()->create(['title' => 'RAIDの違い', 'category' => '基本情報の用語解説']);

        $html = $this->get("/blog/{$post->slug}")->getContent();
        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $html, $m);
        $data = json_decode($m[1] ?? '', true);

        $this->assertSame('BlogPosting', $data['@type']);
        $this->assertSame('RAIDの違い', $data['headline']);
        $this->assertSame(route('posts.show', $post), $data['url']);
        $this->assertSame($post->published_at->toIso8601String(), $data['datePublished']);
        $this->assertSame('基本情報の用語解説', $data['articleSection']);
        $this->assertSame(config('site.owner.name'), $data['author']['name']);
    }

    public function test_json_ldに書いたscriptタグは閉じられない(): void
    {
        $post = Post::factory()->create(['title' => '</script><script>alert(1)</script>']);

        $html = $this->get("/blog/{$post->slug}")->getContent();

        $this->assertStringNotContainsString('<script>alert(1)</script>', $html);
    }

    public function test_一覧ページはog_typeがwebsite(): void
    {
        $this->get('/blog')->assertSee('<meta property="og:type" content="website">', false);
    }

    public function test_サイトマップに公開中のページだけが載る(): void
    {
        $published = Post::factory()->create(['slug' => 'published-post']);
        Post::factory()->draft()->create(['slug' => 'draft-post']);
        Post::factory()->scheduled()->create(['slug' => 'scheduled-post']);
        Work::factory()->create(['slug' => 'work-with-body', 'body' => '本文']);
        Work::factory()->create(['slug' => 'work-summary-only', 'body' => null]);
        Work::factory()->create(['slug' => 'work-blank', 'summary' => '  ', 'body' => "\n "]);
        Work::factory()->unpublished()->create(['slug' => 'hidden-work', 'body' => '本文']);

        $this->get('/sitemap.xml')
            ->assertOk()
            ->assertHeader('Content-Type', 'application/xml; charset=UTF-8')
            ->assertSee('<loc>'.route('home').'</loc>', false)
            ->assertSee('<loc>'.route('posts.index').'</loc>', false)
            ->assertSee('<loc>'.route('posts.show', $published).'</loc>', false)
            ->assertSee('/works/work-with-body', false)
            ->assertSee('/works/work-summary-only', false)
            ->assertDontSee('draft-post')
            ->assertDontSee('scheduled-post')
            ->assertDontSee('work-blank')
            ->assertDontSee('hidden-work');
    }

    public function test_サイトマップに載る実績は詳細ページを開ける(): void
    {
        Work::factory()->create(['slug' => 'with-body', 'body' => '本文']);
        Work::factory()->create(['slug' => 'summary-only', 'body' => null]);
        Work::factory()->create(['slug' => 'blank', 'summary' => '', 'body' => '']);

        $xml = simplexml_load_string($this->get('/sitemap.xml')->getContent());
        $workUrls = array_values(array_filter(
            array_map(fn ($url) => (string) $url->loc, iterator_to_array($xml->url, false)),
            fn (string $loc) => str_contains($loc, '/works/'),
        ));

        $this->assertNotEmpty($workUrls);
        foreach ($workUrls as $loc) {
            $this->get(parse_url($loc, PHP_URL_PATH))->assertOk();
        }
    }

    public function test_サイトマップはxmlとして読める(): void
    {
        Post::factory()->create();

        $xml = simplexml_load_string($this->get('/sitemap.xml')->getContent());

        $this->assertNotFalse($xml);
        $this->assertSame('urlset', $xml->getName());
    }

    public function test_robots_txtにサイトマップの場所が書いてある(): void
    {
        $robots = file_get_contents(public_path('robots.txt'));

        $this->assertStringContainsString('Sitemap: https://toshlab.jp/sitemap.xml', $robots);
        $this->assertStringContainsString('Disallow: /admin', $robots);
    }
}
