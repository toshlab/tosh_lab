<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Database\UniqueConstraintViolationException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * 記事の「出どころ」（管理画面／ファイル）とタグの列（docs/blog-import-plan.md #3）
 */
class PostSourceTest extends TestCase
{
    use RefreshDatabase;

    public function test_管理画面で作った記事の出どころはadminになる(): void
    {
        $post = Post::create(['title' => 'タイトル', 'slug' => 'admin-post', 'body' => '本文']);

        $this->assertSame(Post::SOURCE_ADMIN, $post->fresh()->source);
        $this->assertFalse($post->fresh()->isFromFile());
    }

    public function test_ファイルから取り込んだ記事はfrom_fileで絞り込める(): void
    {
        $file = Post::factory()->fromFile('2026-11-20-raid.md')->create(['slug' => 'raid']);
        Post::factory()->create(['slug' => 'admin-post']);

        $this->assertTrue($file->isFromFile());
        $this->assertSame(['raid'], Post::fromFile()->pluck('slug')->all());
        $this->assertSame('2026-11-20-raid.md', $file->source_path);
        $this->assertSame(64, strlen($file->source_hash));
    }

    public function test_タグは配列で保存して取り出せる(): void
    {
        $post = Post::factory()->create(['tags' => ['RAID', '基本情報']]);

        $this->assertSame(['RAID', '基本情報'], $post->fresh()->tags);
    }

    public function test_同じファイルのパスで2つの記事は作れない(): void
    {
        Post::factory()->fromFile('same.md')->create();

        $this->expectException(UniqueConstraintViolationException::class);
        Post::factory()->fromFile('same.md')->create();
    }

    public function test_記事のページにタグが出てjson_ldのkeywordsになる(): void
    {
        $post = Post::factory()->create(['tags' => ['RAID', '基本情報']]);

        $response = $this->get("/blog/{$post->slug}")
            ->assertOk()
            ->assertSee('#RAID')
            ->assertSee('#基本情報');

        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $response->getContent(), $m);
        $this->assertSame('RAID, 基本情報', json_decode($m[1], true)['keywords']);
    }

    public function test_タグが無い記事はjson_ldにkeywordsを出さない(): void
    {
        $post = Post::factory()->create(['tags' => null]);

        preg_match('#<script type="application/ld\+json">(.*?)</script>#s', $this->get("/blog/{$post->slug}")->getContent(), $m);
        $this->assertArrayNotHasKey('keywords', json_decode($m[1], true));
    }

    public function test_カテゴリの候補は3つ(): void
    {
        $this->assertSame(['基本情報の用語解説', '作業の記録', 'ITニュース解説'], config('blog.categories'));
    }
}
