<?php

namespace App\UseCases\Post;

use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Symfony\Component\Yaml\Exception\ParseException;
use Symfony\Component\Yaml\Yaml;

/**
 * 記事のファイル1つを読んで、front matter を確かめる（docs/blog-import-plan.md）。
 * DB には触らない。問題があれば、理由（日本語）の一覧を返す。
 */
class ParseFileAction
{
    /** 公開日-スラッグ.md */
    private const FILENAME = '/^(\d{4}-\d{2}-\d{2})-([a-z0-9]+(?:-[a-z0-9]+)*)\.md$/';

    /**
     * @return array{ok: true, data: array<string, mixed>}|array{ok: false, reasons: list<string>}
     */
    public function __invoke(string $filename, string $contents): array
    {
        if (! preg_match(self::FILENAME, $filename, $name)) {
            return $this->fail(__('post_import.reasons.filename'));
        }
        [, $fileDate, $fileSlug] = $name;

        // 改行コードと先頭の BOM をそろえてから、front matter と本文に分ける
        $contents = str_replace(["\r\n", "\r"], "\n", preg_replace('/^\xEF\xBB\xBF/', '', $contents));
        if (! preg_match('/^---\n(.*?)\n---(?:\n(.*))?$/s', $contents, $parts)) {
            return $this->fail(__('post_import.reasons.no_front_matter'));
        }
        $body = trim($parts[2] ?? '');

        try {
            $meta = Yaml::parse($parts[1]);
        } catch (ParseException $e) {
            return $this->fail(__('post_import.reasons.invalid_yaml', ['message' => $e->getMessage()]));
        }
        if (! is_array($meta) || array_is_list($meta)) {
            return $this->fail(__('post_import.reasons.not_mapping'));
        }

        // YAML は日付だけ（2026-11-20）や秒つきの日時を数値に変えてしまうので、文字列で書かれたものだけ受け付ける
        if (isset($meta['published_at']) && ! is_string($meta['published_at'])) {
            return $this->fail(__('post_import.reasons.published_at_format'));
        }

        $validator = Validator::make($meta, [
            'title' => ['required', 'string', 'max:255'],
            'slug' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'max:'.config('blog.description_max')],
            'category' => ['required', 'string', Rule::in(config('blog.categories'))],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['string', 'max:50'],
            'published_at' => ['required', 'date_format:'.config('blog.published_at_format')],
            'status' => ['required', Rule::in(['published', 'draft'])],
        ], [], __('post_import.attributes'));

        $reasons = $validator->errors()->all();
        if ($body === '') {
            $reasons[] = __('post_import.reasons.empty_body');
        }
        if (is_string($meta['slug'] ?? null) && $meta['slug'] !== $fileSlug) {
            $reasons[] = __('post_import.reasons.slug_mismatch', ['file' => $fileSlug, 'slug' => $meta['slug']]);
        }
        if ($reasons) {
            return ['ok' => false, 'reasons' => $reasons];
        }

        // front matter の日時は日本時間として読み、アプリのタイムゾーンにそろえる。
        // 「!」を付けないと、書いていない秒が今の時刻で埋まる（docs/coding-standards.md §2-7）
        $publishedAt = CarbonImmutable::createFromFormat(
            '!'.config('blog.published_at_format'),
            $meta['published_at'],
            config('blog.published_at_timezone'),
        )->setTimezone(config('app.timezone'));

        $publishedDate = $publishedAt->setTimezone(config('blog.published_at_timezone'))->format('Y-m-d');
        if ($publishedDate !== $fileDate) {
            return $this->fail(__('post_import.reasons.date_mismatch', ['file' => $fileDate, 'date' => $publishedDate]));
        }

        return ['ok' => true, 'data' => [
            'title' => $meta['title'],
            'slug' => $meta['slug'],
            'excerpt' => $meta['description'],
            'category' => $meta['category'],
            'tags' => array_values($meta['tags'] ?? []) ?: null,
            'body' => $body,
            // draft は取り込むが公開しない（published_at が null の記事は下書き）
            'published_at' => $meta['status'] === 'published' ? $publishedAt : null,
        ]];
    }

    /** @return array{ok: false, reasons: list<string>} */
    private function fail(string $reason): array
    {
        return ['ok' => false, 'reasons' => [$reason]];
    }
}
