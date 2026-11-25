<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inquiry extends Model
{
    use HasFactory;

    // bot はフォームを開いてすぐ送信するため、表示からこの秒数未満の送信は拒否する
    // （人が名前・メール・本文を入力するには、通常これ以上かかる）
    public const MIN_SUBMIT_SECONDS = 3;

    protected $fillable = [
        'name',
        'email',
        'message',
        'read_at',
    ];

    protected function casts(): array
    {
        return [
            'read_at' => 'datetime',
        ];
    }

    public function markAsRead(): void
    {
        if ($this->read_at === null) {
            $this->forceFill(['read_at' => now()])->save();
        }
    }
}
