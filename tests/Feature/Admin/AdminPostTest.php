<?php

namespace Tests\Feature\Admin;

use App\Filament\Resources\Posts\Pages\CreatePost;
use App\Filament\Resources\Posts\Pages\EditPost;
use App\Filament\Resources\Posts\Pages\ListPosts;
use App\Filament\Resources\Posts\Pages\ViewPost;
use App\Filament\Resources\Posts\PostResource;
use App\Models\Post;
use App\Models\User;
use Filament\Actions\DeleteAction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * 管理画面のブログ記事（docs/blog-import-plan.md #5）
 */
class AdminPostTest extends TestCase
{
    use RefreshDatabase;

    private string $dir;

    protected function setUp(): void
    {
        parent::setUp();

        $this->actingAs(User::factory()->create());

        // 「元のファイルが今も無いか」を確かめる先（記事のフォルダ）を、テスト用の空のフォルダにする
        $this->dir = storage_path('framework/testing/admin-posts-'.uniqid());
        File::ensureDirectoryExists($this->dir);
        config(['blog.content_path' => $this->dir]);
    }

    protected function tearDown(): void
    {
        File::deleteDirectory($this->dir);

        parent::tearDown();
    }

    /** 元のファイルが無くなって下書きに戻った、ファイル由来の記事（取り込みがそう記録した状態） */
    private function removedFileDraft(string $path = '2026-11-20-old.md'): Post
    {
        return Post::factory()->fromFile($path)->create(['published_at' => null, 'source_hash' => null]);
    }

    public function test_ファイル由来の記事は編集ページを開けず表示ページに案内とファイルのパスが出る(): void
    {
        $post = Post::factory()->fromFile('2026-11-20-raid.md')->create(['slug' => 'raid']);

        $this->get("/admin/posts/{$post->getRouteKey()}/edit")->assertForbidden();
        $this->get("/admin/posts/{$post->getRouteKey()}")
            ->assertOk()
            ->assertSee('GitHub のファイルから取り込んだ記事です')
            ->assertSee('content/posts/2026-11-20-raid.md');
    }

    public function test_ファイル由来の記事の編集ページは画面の処理として開いても403になる(): void
    {
        $post = Post::factory()->fromFile()->create();

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])->assertForbidden();
    }

    public function test_ファイル由来の記事は管理画面から編集も削除もできない(): void
    {
        $file = Post::factory()->fromFile()->create();
        $admin = Post::factory()->create();

        $this->assertFalse(PostResource::canEdit($file));
        $this->assertFalse(PostResource::canDelete($file));
        $this->assertTrue(PostResource::canEdit($admin));
        $this->assertTrue(PostResource::canDelete($admin));
    }

    public function test_元のファイルが無くなったファイル由来の下書きは表示ページから削除できる(): void
    {
        $post = $this->removedFileDraft();

        $this->get("/admin/posts/{$post->getRouteKey()}")
            ->assertOk()
            ->assertSee('元のファイルが無くなった下書きです')
            ->assertDontSee('GitHub のファイルから取り込んだ記事です');

        Livewire::test(ViewPost::class, ['record' => $post->getRouteKey()])
            ->assertActionVisible(DeleteAction::class)
            ->callAction(DeleteAction::class);

        $this->assertModelMissing($post);
    }

    /**
     * 削除のボタンが出ない記事に、削除の処理を直接呼んでも消えないこと（Filament は実行の直前にもポリシーを見る）
     *
     * @return array<string, array{0: \Closure(self): Post}>
     */
    public static function undeletableFilePosts(): array
    {
        return [
            '公開中' => [fn (self $t) => Post::factory()->fromFile('2026-11-20-a.md')->create()],
            '元のファイルがある下書き' => [fn (self $t) => Post::factory()->fromFile('2026-11-20-b.md')->create(['published_at' => null])],
            '予約投稿' => [fn (self $t) => Post::factory()->fromFile('2026-11-20-c.md')->scheduled()->create()],
            '記録上は無くなったが、ファイルが戻っている' => [function (self $t) {
                File::put("{$t->dir}/2026-11-20-d.md", '戻したファイル');

                return $t->removedFileDraft('2026-11-20-d.md');
            }],
        ];
    }

    #[DataProvider('undeletableFilePosts')]
    public function test_条件に合わないファイル由来の記事は削除の処理を直接呼んでも消えない(\Closure $make): void
    {
        $post = $make($this);

        $this->assertFalse(PostResource::canDelete($post));

        Livewire::test(ViewPost::class, ['record' => $post->getRouteKey()])
            ->assertActionHidden(DeleteAction::class)
            ->call('mountAction', 'delete')
            ->call('callMountedAction');

        $this->assertModelExists($post);
    }

    public function test_管理画面で作った記事は編集ページから削除できる(): void
    {
        $post = Post::factory()->create();

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->callAction(DeleteAction::class);

        $this->assertModelMissing($post);
    }

    public function test_管理画面で作った記事は今までどおり編集できて案内は出ない(): void
    {
        $post = Post::factory()->create(['title' => '手で書いた記事']);

        $this->get("/admin/posts/{$post->getRouteKey()}/edit")->assertOk();
        $this->get("/admin/posts/{$post->getRouteKey()}")
            ->assertOk()
            ->assertDontSee('GitHub のファイルから取り込んだ記事です');

        Livewire::test(EditPost::class, ['record' => $post->getRouteKey()])
            ->fillForm(['title' => '直したタイトル'])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertSame('直したタイトル', $post->fresh()->title);
    }

    public function test_一覧に出どころが出て絞り込める(): void
    {
        $file = Post::factory()->fromFile()->create(['title' => 'ファイルの記事']);
        $admin = Post::factory()->create(['title' => '管理画面の記事']);

        Livewire::test(ListPosts::class)
            ->assertCanSeeTableRecords([$file, $admin])
            ->assertSee('ファイル')
            ->assertSee('管理画面')
            ->filterTable('source', Post::SOURCE_FILE)
            ->assertCanSeeTableRecords([$file])
            ->assertCanNotSeeTableRecords([$admin]);
    }

    public function test_カテゴリは候補から選びタグも保存できる(): void
    {
        Livewire::test(CreatePost::class)
            ->fillForm([
                'title' => '新しい記事',
                'body' => '本文',
                'slug' => 'new-post',
                'category' => '作業の記録',
                'tags' => ['Laravel', 'VPS'],
            ])
            ->call('create')
            ->assertHasNoFormErrors();

        $post = Post::where('slug', 'new-post')->sole();
        $this->assertSame('作業の記録', $post->category);
        $this->assertSame(['Laravel', 'VPS'], $post->tags);
        $this->assertSame(Post::SOURCE_ADMIN, $post->source);
    }

    public function test_候補に無いカテゴリは保存できない(): void
    {
        Livewire::test(CreatePost::class)
            ->fillForm(['title' => '記事', 'body' => '本文', 'slug' => 'bad-category', 'category' => '雑記'])
            ->call('create')
            ->assertHasFormErrors(['category']);

        $this->assertDatabaseMissing('posts', ['slug' => 'bad-category']);
    }
}
