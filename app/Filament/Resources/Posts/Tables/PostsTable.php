<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Models\Post;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class PostsTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                TextColumn::make('title')
                    ->label('タイトル')
                    ->searchable(),
                TextColumn::make('category')
                    ->label('カテゴリ')
                    ->searchable(),
                TextColumn::make('status')
                    ->label('状態')
                    ->badge()
                    ->state(fn (Post $record): string => $record->statusLabel())
                    ->color(fn (string $state): string => match ($state) {
                        '公開中' => 'success',
                        '予約' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('source')
                    ->label('出どころ')
                    ->badge()
                    ->state(fn (Post $record): string => $record->sourceLabel())
                    ->color(fn (Post $record): string => $record->isFromFile() ? 'info' : 'gray'),
                TextColumn::make('published_at')
                    ->label('公開日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->filters([
                SelectFilter::make('source')
                    ->label('出どころ')
                    ->options([Post::SOURCE_ADMIN => '管理画面', Post::SOURCE_FILE => 'ファイル']),
            ])
            // ファイルから取り込んだ記事は編集できない（PostPolicy）ので、行を押すと表示ページが開く
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ]);
        // まとめての削除は置かない（ファイル由来の記事まで消えてしまうため。1件ずつ編集ページから消す）
    }
}
