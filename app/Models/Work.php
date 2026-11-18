<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Work extends Model
{
    use HasFactory;

    public const LABELS = [
        'sample' => 'サンプル',
        'demo' => '提案デモ',
        'client' => '制作実績',
    ];

    protected $fillable = [
        'title',
        'slug',
        'label',
        'summary',
        'body',
        'tech',
        'thumbnail',
        'demo_url',
        'github_url',
        'sort_order',
        'is_published',
    ];

    protected function casts(): array
    {
        return [
            'tech' => 'array',
            'is_published' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** @param Builder<Work> $query */
    public function scopePublished(Builder $query): void
    {
        $query->where('is_published', true)->orderBy('sort_order')->orderBy('id');
    }

    /**
     * 詳細ページを持つ実績：公開中で、概要か本文のどちらかに中身がある。sitemap などで使う。
     * 判定は hasDetailPage() と必ず同じにすること（MySQL の TRIM() は改行を消さないので、
     * 「空白以外の文字が1つでもあるか」を正規表現で見る。PHP 側の hasText() と同じ考え方）。
     *
     * @param  Builder<Work>  $query
     */
    public function scopeWithDetailPage(Builder $query): void
    {
        $query->published()->where(function (Builder $q) {
            $q->where('summary', 'REGEXP', '[^[:space:]]')
                ->orWhere('body', 'REGEXP', '[^[:space:]]');
        });
    }

    /**
     * 詳細ページがあるか。概要も本文も空白だけの実績は、ページとして成り立たないので持たない
     * （トップの行はデモ・GitHub へリンクし、/works/{slug} は 404 にする）。
     */
    public function hasDetailPage(): bool
    {
        return $this->is_published && (self::hasText($this->summary) || self::hasText($this->body));
    }

    /** 空白（全角スペース・改行を含む）以外の文字が1つでもあるか */
    private static function hasText(?string $value): bool
    {
        return $value !== null && preg_match('/[^\s\p{Z}]/u', $value) === 1;
    }

    public function labelText(): string
    {
        return self::LABELS[$this->label] ?? $this->label;
    }

    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail ? Storage::disk('public')->url($this->thumbnail) : null;
    }
}
