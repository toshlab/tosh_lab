<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Post extends Model
{
    use HasFactory;

    /** 管理画面で作った記事 */
    public const SOURCE_ADMIN = 'admin';

    /** content/posts/ のファイルから取り込んだ記事（管理画面では編集しない） */
    public const SOURCE_FILE = 'file';

    protected $fillable = [
        'title',
        'slug',
        'category',
        'tags',
        'excerpt',
        'body',
        'published_at',
        'source',
        'source_path',
        'source_hash',
    ];

    protected $attributes = [
        'source' => self::SOURCE_ADMIN,
    ];

    protected function casts(): array
    {
        return [
            'tags' => 'array',
            'published_at' => 'datetime',
        ];
    }

    /** 管理画面に出す出どころの名前 */
    public function sourceLabel(): string
    {
        return $this->isFromFile() ? 'ファイル' : '管理画面';
    }

    /** 管理画面に出す状態（下書き／予約／公開中） */
    public function statusLabel(): string
    {
        return match (true) {
            $this->published_at === null => '下書き',
            $this->isPublished() => '公開中',
            default => '予約',
        };
    }

    public function isFromFile(): bool
    {
        return $this->source === self::SOURCE_FILE;
    }

    /**
     * 元のファイルが無くなって下書きに戻った、ファイル由来の記事か（管理画面から削除してよい）。
     * 取り込みで「無くなった」と記録した（source_hash を空にした）うえで、念のため今もファイルが無いことを確かめる。
     */
    public function isRemovedFileDraft(): bool
    {
        return $this->isFromFile()
            && $this->published_at === null
            && $this->source_hash === null
            && ! is_file(rtrim(config('blog.content_path'), '/').'/'.$this->source_path);
    }

    /** @param  Builder<Post>  $query */
    public function scopeFromFile(Builder $query): void
    {
        $query->where('source', self::SOURCE_FILE);
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * 公開日時を過ぎた記事だけ。新しい順。
     *
     * @param  Builder<Post>  $query
     */
    public function scopePublished(Builder $query): void
    {
        $query->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->latest('published_at');
    }

    public function isPublished(): bool
    {
        return $this->published_at !== null && $this->published_at->lte(now());
    }

    /** 本文のMarkdownをHTMLにする。記事内の生HTMLは取り除く。 */
    public function bodyHtml(): string
    {
        return Str::markdown($this->body, [
            'html_input' => 'strip',
            'allow_unsafe_links' => false,
        ]);
    }

    /**
     * 記事の構造化データ（schema.org の BlogPosting）。記事のページに JSON-LD として出す。
     *
     * @return array<string, mixed>
     */
    public function structuredData(): array
    {
        $url = route('posts.show', $this);
        $image = asset(config('site.og_image'));

        return array_filter([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $this->title,
            'description' => $this->excerptText(),
            'url' => $url,
            'mainEntityOfPage' => ['@type' => 'WebPage', '@id' => $url],
            'datePublished' => $this->published_at?->toIso8601String(),
            'dateModified' => $this->updated_at?->toIso8601String(),
            'inLanguage' => 'ja',
            'articleSection' => $this->category,
            'keywords' => $this->tags ? implode(', ', $this->tags) : null,
            'image' => [$image],
            'author' => [
                '@type' => 'Person',
                'name' => config('site.owner.name'),
                'url' => route('home'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('site.name'),
                'url' => route('home'),
                'logo' => ['@type' => 'ImageObject', 'url' => $image],
            ],
        ], fn ($value) => $value !== null);
    }

    public function excerptText(): string
    {
        return $this->excerpt ?: Str::limit(strip_tags($this->bodyHtml()), 120);
    }
}
