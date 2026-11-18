<?php

namespace App\Filament\Resources\Posts\Pages;

use App\Filament\Resources\Posts\PostResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        // どちらも PostPolicy で出す・出さないが決まる（編集は管理画面の記事だけ、
        // 削除は管理画面の記事と、元のファイルが無くなったファイル由来の下書きだけ）
        return [
            EditAction::make(),
            DeleteAction::make(),
        ];
    }
}
