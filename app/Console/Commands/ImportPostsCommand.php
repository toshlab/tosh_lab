<?php

namespace App\Console\Commands;

use App\UseCases\Post\ImportFilesAction;
use Illuminate\Console\Command;
use InvalidArgumentException;

/**
 * content/posts/ の Markdown を、ブログの記事として取り込む。
 *   php artisan posts:import             取り込む
 *   php artisan posts:import --dry-run   確認だけ（プルリクエストのチェックで使う）
 *
 * スキップしたファイルが1つでもあれば、終了コードを 1 にする（GitHub Actions で失敗として見えるように）。
 */
class ImportPostsCommand extends Command
{
    protected $signature = 'posts:import
        {--path= : 記事のフォルダ（省略時は config/blog.php の content_path）}
        {--dry-run : データベースに書き込まずに、結果だけを確認する}';

    protected $description = 'content/posts/ の Markdown をブログの記事として取り込む';

    public function handle(ImportFilesAction $import): int
    {
        $path = $this->option('path') ?: config('blog.content_path');
        $dryRun = (bool) $this->option('dry-run');

        try {
            $results = $import($path, $dryRun);
        } catch (InvalidArgumentException $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        if ($results) {
            $this->table(
                ['ファイル', '結果', 'slug'],
                array_map(fn (array $r) => [$r['path'], __("post_import.actions.{$r['action']}"), $r['slug'] ?? '-'], $results),
            );
        }

        $skipped = array_filter($results, fn (array $r) => $r['action'] === 'skipped');
        foreach ($skipped as $result) {
            $this->warn("{$result['path']}：");
            foreach ($result['reasons'] as $reason) {
                $this->line("  - {$reason}");
            }
        }

        $counts = array_count_values(array_column($results, 'action'));
        $this->info(__('post_import.summary', [
            'created' => $counts['created'] ?? 0,
            'updated' => $counts['updated'] ?? 0,
            'unchanged' => $counts['unchanged'] ?? 0,
            'unpublished' => $counts['unpublished'] ?? 0,
            'skipped' => $counts['skipped'] ?? 0,
        ]));
        if ($dryRun) {
            $this->comment(__('post_import.dry_run'));
        }

        return $skipped ? self::FAILURE : self::SUCCESS;
    }
}
