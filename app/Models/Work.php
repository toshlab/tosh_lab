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

    public function labelText(): string
    {
        return self::LABELS[$this->label] ?? $this->label;
    }

    public function thumbnailUrl(): ?string
    {
        return $this->thumbnail ? Storage::disk('public')->url($this->thumbnail) : null;
    }
}
