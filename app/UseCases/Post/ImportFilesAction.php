<?php

namespace App\UseCases\Post;

use App\Models\Post;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

/**
 * 記事のフォルダ（content/posts/）の *.md を、ブログの記事として DB に取り込む（docs/blog-import-plan.md）。
 *
 * - slug をキーに、新規作成または更新する。ファイルの中身が前回と同じなら何もしない（何度実行しても同じ結果）
 * - 問題のあるファイルはスキップして理由を残し、ほかのファイルは続ける
 * - 管理画面で作った記事と slug が重なったら、上書きしない
 * - フォルダから無くなったファイルの記事は、削除せずに下書きに戻す
 * - $dryRun のときは、同じ処理をトランザクションの中で行い、最後に取り消す（結果は本番と同じに分かる）
 */
class ImportFilesAction
{
    public function __construct(private ParseFileAction $parse) {}

    /**
     * @return list<array{path: string, action: string, slug: ?string, reasons: list<string>}>
     */
    public function __invoke(string $directory, bool $dryRun = false): array
    {
        if (! is_dir($directory)) {
            throw new InvalidArgumentException(__('post_import.path_not_found', ['path' => $directory]));
        }

        $files = glob(rtrim($directory, '/').'/*.md') ?: [];
        sort($files);

        DB::beginTransaction();
        try {
            $results = $this->importFiles($files);
            $results = [...$results, ...$this->unpublishRemoved(array_map('basename', $files))];
        } catch (\Throwable $e) {
            DB::rollBack();
            throw $e;
        }

        $dryRun ? DB::rollBack() : DB::commit();

        foreach ($results as $result) {
            if ($result['action'] === 'skipped') {
                Log::warning('ブログ記事の取り込みをスキップしました', ['path' => $result['path'], 'reasons' => $result['reasons'], 'dry_run' => $dryRun]);
            }
        }

        return $results;
    }

    /**
     * @param  list<string>  $files
     * @return list<array{path: string, action: string, slug: ?string, reasons: list<string>}>
     */
    private function importFiles(array $files): array
    {
        $results = [];
        $claimed = []; // slug => このときに取り込んだファイル名（同じ slug のファイルが2つあるとき用）

        foreach ($files as $file) {
            $path = basename($file);
            $contents = @file_get_contents($file);
            if ($contents === false) {
                $results[] = $this->result($path, 'skipped', null, [__('post_import.reasons.unreadable')]);

                continue;
            }

            $parsed = ($this->parse)($path, $contents);
            if (! $parsed['ok']) {
                $results[] = $this->result($path, 'skipped', null, $parsed['reasons']);

                continue;
            }

            $data = $parsed['data'];
            $slug = $data['slug'];

            if (isset($claimed[$slug])) {
                $results[] = $this->result($path, 'skipped', $slug, [__('post_import.reasons.slug_duplicated', ['slug' => $slug, 'other' => $claimed[$slug]])]);

                continue;
            }

            $post = Post::where('slug', $slug)->first() ?? Post::where('source_path', $path)->first();

            if ($post && ! $post->isFromFile()) {
                $results[] = $this->result($path, 'skipped', $slug, [__('post_import.reasons.slug_taken_by_admin', ['slug' => $slug])]);

                continue;
            }

            $claimed[$slug] = $path;
            $hash = hash('sha256', $contents);

            if ($post && $post->source_hash === $hash && $post->source_path === $path) {
                $results[] = $this->result($path, 'unchanged', $slug);

                continue;
            }

            $attributes = $data + ['source' => Post::SOURCE_FILE, 'source_path' => $path, 'source_hash' => $hash];

            if ($post) {
                $post->fill($attributes)->save();
                $results[] = $this->result($path, 'updated', $slug);
            } else {
                Post::create($attributes);
                $results[] = $this->result($path, 'created', $slug);
            }
        }

        return $results;
    }

    /**
     * フォルダから無くなったファイルの記事を下書きに戻す（削除はしない）。
     * 中身に問題があってスキップしたファイルは「まだある」ので、前回取り込んだ内容のまま残す。
     *
     * @param  list<string>  $present
     * @return list<array{path: string, action: string, slug: ?string, reasons: list<string>}>
     */
    private function unpublishRemoved(array $present): array
    {
        $results = [];

        $removed = Post::fromFile()->whereNotIn('source_path', $present ?: [''])->get();
        foreach ($removed as $post) {
            $wasPublished = $post->published_at !== null;

            // ハッシュを空にして「ファイルが無くなった」と記録する。ファイルを戻したときは取り込み直され、
            // 戻さなければ管理画面から削除できるようになる（Post::isRemovedFileDraft）。
            // もともと draft だったファイルも同じように記録する
            if ($wasPublished || $post->source_hash !== null) {
                $post->forceFill(['published_at' => null, 'source_hash' => null])->save();
            }

            // 件数に出すのは、公開中だった記事を下書きに戻したときだけ
            if ($wasPublished) {
                $results[] = $this->result($post->source_path, 'unpublished', $post->slug);
            }
        }

        return $results;
    }

    /** @return array{path: string, action: string, slug: ?string, reasons: list<string>} */
    private function result(string $path, string $action, ?string $slug, array $reasons = []): array
    {
        return compact('path', 'action', 'slug', 'reasons');
    }
}
