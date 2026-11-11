<?php

namespace App\Filament\Resources\Posts\Tables;

use App\Models\Post;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
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
                    ->state(fn (Post $record): string => match (true) {
                        $record->published_at === null => '下書き',
                        $record->isPublished() => '公開中',
                        default => '予約',
                    })
                    ->color(fn (string $state): string => match ($state) {
                        '公開中' => 'success',
                        '予約' => 'warning',
                        default => 'gray',
                    }),
                TextColumn::make('published_at')
                    ->label('公開日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->recordActions([
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
