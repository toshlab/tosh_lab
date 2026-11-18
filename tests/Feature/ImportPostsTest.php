<?php

namespace Tests\Feature;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * php artisan posts:import（docs/blog-import-plan.md #4）
 */
class ImportPostsTest extends TestCase
{
    use RefreshDatabase;

    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->dir = storage_path('framework/testing/posts-'.uniqid());
        File::ensureDirectoryExists($this->dir);
        config(['blog.content_path' => $this->dir]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->dir);

        parent::tearDown();
    }

    /** 記事のファイルを書く。$meta で front matter の一部を上書き・削除（null）できる */
    private function writePost(string $filename, array $meta = [], string $body = "## 見出し\n\n本文です。"): void
    {
        $slug = preg_replace('/^\d{4}-\d{2}-\d{2}-(.+)\.md$/', '$1', $filename);
        $meta = array_filter(array_merge([
            'title' => 'RAID5とRAID6の違い',
            'slug' => $slug,
            'description' => 'RAID5とRAID6の違いを、基本情報技術者試験向けに整理します。',
            'category' => '基本情報の用語解説',
            'tags' => '[RAID, 基本情報]',
            'published_at' => '2026-11-20 08:00',
            'status' => 'published',
        ], $meta), fn ($v) => $v !== null);

        $yaml = implode("\n", array_map(fn ($k, $v) => "{$k}: {$v}", array_keys($meta), $meta));
        File::put("{$this->dir}/{$filename}", "---\n{$yaml}\n---\n\n{$body}\n");
    }

    public function test_正しいファイルを取り込むと記事ができる(): void
    {
        $this->writePost('2026-11-20-raid5-raid6-difference.md');

        $this->artisan('posts:import')->assertSuccessful();

        $post = Post::sole();
        $this->assertSame('raid5-raid6-difference', $post->slug);
        $this->assertSame('RAID5とRAID6の違い', $post->title);
        $this->assertSame('RAID5とRAID6の違いを、基本情報技術者試験向けに整理します。', $post->excerpt);
        $this->assertSame('基本情報の用語解説', $post->category);
        $this->assertSame(['RAID', '基本情報'], $post->tags);
        $this->assertSame("## 見出し\n\n本文です。", $post->body);
        $this->assertSame('2026-11-20 08:00:00', $post->published_at->timezone('Asia/Tokyo')->format('Y-m-d H:i:s'));
        $this->assertTrue($post->isFromFile());
        $this->assertSame('2026-11-20-raid5-raid6-difference.md', $post->source_path);
        $this->assertSame(hash('sha256', File::get("{$this->dir}/2026-11-20-raid5-raid6-difference.md")), $post->source_hash);
    }

    public function test_公開日時を過ぎていれば記事のページを開ける(): void
    {
        $this->travelTo(now('Asia/Tokyo')->setDate(2026, 11, 20)->setTime(9, 0));
        $this->writePost('2026-11-20-raid.md');

        $this->artisan('posts:import')->assertSuccessful();

        $this->get('/blog/raid')->assertOk()->assertSee('RAID5とRAID6の違い')->assertSee('#RAID');
    }

    public function test_同じファイルを2回取り込んでも結果は同じで更新もされない(): void
    {
        $this->writePost('2026-11-20-raid.md');
        $this->artisan('posts:import')->assertSuccessful();
        $first = Post::sole();

        $this->travel(1)->hours();
        $this->artisan('posts:import')->assertSuccessful()->expectsOutputToContain('変更なし 1件');

        $this->assertSame(1, Post::count());
        $this->assertTrue($first->updated_at->equalTo(Post::sole()->updated_at));
    }

    public function test_ファイルを書き換えると記事が更新される(): void
    {
        $this->writePost('2026-11-20-raid.md');
        $this->artisan('posts:import')->assertSuccessful();

        $this->writePost('2026-11-20-raid.md', ['title' => '新しいタイトル'], '新しい本文');
        $this->artisan('posts:import')->assertSuccessful()->expectsOutputToContain('更新 1件');

        $this->assertSame('新しいタイトル', Post::sole()->title);
        $this->assertSame('新しい本文', Post::sole()->body);
    }

    public function test_draftは取り込むが公開しない(): void
    {
        $this->writePost('2026-11-20-raid.md', ['status' => 'draft']);

        $this->artisan('posts:import')->assertSuccessful();

        $this->assertNull(Post::sole()->published_at);
        $this->get('/blog/raid')->assertNotFound();
    }

    public function test_未来の日時は予約投稿になり公開日時まで表示されない(): void
    {
        $this->travelTo(now('Asia/Tokyo')->setDate(2026, 11, 20)->setTime(7, 59));
        $this->writePost('2026-11-20-raid.md');

        $this->artisan('posts:import')->assertSuccessful();
        $this->get('/blog/raid')->assertNotFound();

        $this->travel(2)->minutes();
        $this->get('/blog/raid')->assertOk();
    }

    public function test_front_matterが足りないファイルはスキップしてほかの記事は取り込む(): void
    {
        $this->writePost('2026-11-20-no-title.md', ['title' => null]);
        $this->writePost('2026-11-21-ok.md', ['published_at' => '2026-11-21 08:00']);

        $this->artisan('posts:import')
            ->assertFailed()
            ->expectsOutputToContain('2026-11-20-no-title.md')
            ->expectsOutputToContain('title');

        $this->assertSame(['ok'], Post::pluck('slug')->all());
    }

    /**
     * @return array<string, array{0: string, 1: array<string, mixed>, 2: string}>
     */
    public static function invalidFiles(): array
    {
        return [
            'カテゴリが候補に無い' => ['2026-11-20-raid.md', ['category' => '雑記'], 'category'],
            'descriptionが120文字を超える' => ['2026-11-20-raid.md', ['description' => str_repeat('あ', 121)], 'description'],
            'published_atの形が違う' => ['2026-11-20-raid.md', ['published_at' => '2026/11/20 8:00'], 'published_at'],
            '日付だけのpublished_at' => ['2026-11-20-raid.md', ['published_at' => '2026-11-20'], 'YYYY-MM-DD HH:MM'],
            'statusが不正' => ['2026-11-20-raid.md', ['status' => 'public'], 'status'],
            'slugがファイル名と違う' => ['2026-11-20-raid.md', ['slug' => 'other-slug'], 'other-slug'],
            '日付がファイル名と違う' => ['2026-11-20-raid.md', ['published_at' => '2026-11-21 08:00'], '2026-11-21'],
            'ファイル名の形が違う' => ['raid.md', [], '公開日-スラッグ.md'],
        ];
    }

    #[DataProvider('invalidFiles')]
    public function test_不正なfront_matterはスキップして理由を出す(string $filename, array $meta, string $reason): void
    {
        $this->writePost($filename, $meta);

        $this->artisan('posts:import')->assertFailed()->expectsOutputToContain($reason);

        $this->assertSame(0, Post::count());
    }

    public function test_front_matterが無いファイルや本文が無いファイルはスキップする(): void
    {
        File::put("{$this->dir}/2026-11-20-no-front.md", "# タイトルだけ\n");
        $this->writePost('2026-11-20-no-body.md', [], '');

        $this->artisan('posts:import')
            ->assertFailed()
            ->expectsOutputToContain('front matter')
            ->expectsOutputToContain('本文がありません');

        $this->assertSame(0, Post::count());
    }

    public function test_yamlとして読めないfront_matterはスキップする(): void
    {
        File::put("{$this->dir}/2026-11-20-broken.md", "---\ntitle: [閉じていない\n---\n\n本文\n");

        $this->artisan('posts:import')->assertFailed()->expectsOutputToContain('YAML');

        $this->assertSame(0, Post::count());
    }

    public function test_管理画面で作った記事とslugが重なったら上書きしない(): void
    {
        $admin = Post::factory()->create(['slug' => 'raid', 'title' => '手で書いた記事']);
        $this->writePost('2026-11-20-raid.md');

        $this->artisan('posts:import')->assertFailed()->expectsOutputToContain('管理画面で作った記事');

        $this->assertSame('手で書いた記事', $admin->fresh()->title);
        $this->assertFalse($admin->fresh()->isFromFile());
    }

    public function test_同じslugのファイルが2つあれば2つ目をスキップする(): void
    {
        $this->writePost('2026-11-20-raid.md');
        $this->writePost('2026-11-21-raid.md', ['published_at' => '2026-11-21 08:00']);

        $this->artisan('posts:import')->assertFailed()->expectsOutputToContain('同じ slug');

        $this->assertSame('2026-11-20-raid.md', Post::sole()->source_path);
    }

    public function test_ファイルを消すと記事は削除されず下書きに戻り戻すと再び公開される(): void
    {
        $this->writePost('2026-11-20-raid.md');
        $this->artisan('posts:import')->assertSuccessful();

        File::delete("{$this->dir}/2026-11-20-raid.md");
        $this->artisan('posts:import')->assertSuccessful()->expectsOutputToContain('下書きに戻した 1件');

        $this->assertSame(1, Post::count());
        $this->assertNull(Post::sole()->published_at);

        // もう一度実行しても、二重に数えない
        $this->artisan('posts:import')->assertSuccessful()->expectsOutputToContain('下書きに戻した 0件');

        $this->writePost('2026-11-20-raid.md');
        $this->artisan('posts:import')->assertSuccessful();
        $this->assertNotNull(Post::sole()->published_at);
    }

    public function test_draftのファイルを消しても無くなったと記録され管理画面から削除できるようになる(): void
    {
        $this->writePost('2026-11-20-raid.md', ['status' => 'draft']);
        $this->artisan('posts:import')->assertSuccessful();
        $this->assertFalse(Post::sole()->isRemovedFileDraft());

        File::delete("{$this->dir}/2026-11-20-raid.md");
        // もともと下書きなので「下書きに戻した」には数えないが、無くなったことは記録する
        $this->artisan('posts:import')->assertSuccessful()->expectsOutputToContain('下書きに戻した 0件');

        $this->assertNull(Post::sole()->source_hash);
        $this->assertTrue(Post::sole()->isRemovedFileDraft());
    }

    public function test_中身に問題があるファイルは前回取り込んだ記事をそのまま残す(): void
    {
        $this->writePost('2026-11-20-raid.md');
        $this->artisan('posts:import')->assertSuccessful();

        $this->writePost('2026-11-20-raid.md', ['category' => '雑記']);
        $this->artisan('posts:import')->assertFailed();

        $this->assertSame('基本情報の用語解説', Post::sole()->category);
        $this->assertNotNull(Post::sole()->published_at);
    }

    public function test_dry_runはデータベースに書き込まずに結果だけ出す(): void
    {
        $this->writePost('2026-11-20-raid.md');

        $this->artisan('posts:import', ['--dry-run' => true])
            ->assertSuccessful()
            ->expectsOutputToContain('新規作成 1件')
            ->expectsOutputToContain('--dry-run');

        $this->assertSame(0, Post::count());
    }

    public function test_dry_runでも問題があれば失敗する(): void
    {
        $this->writePost('2026-11-20-raid.md', ['category' => '雑記']);

        $this->artisan('posts:import', ['--dry-run' => true])->assertFailed();
    }

    public function test_pathで別のフォルダを指定できる(): void
    {
        $other = "{$this->dir}/other";
        File::ensureDirectoryExists($other);
        $this->writePost('2026-11-20-raid.md');
        File::move("{$this->dir}/2026-11-20-raid.md", "{$other}/2026-11-20-raid.md");

        $this->artisan('posts:import', ['--path' => $other])->assertSuccessful();

        $this->assertSame('raid', Post::sole()->slug);
    }

    public function test_フォルダが無ければ失敗する(): void
    {
        $this->artisan('posts:import', ['--path' => "{$this->dir}/missing"])
            ->assertFailed()
            ->expectsOutputToContain('記事のフォルダが見つかりません');
    }
}
