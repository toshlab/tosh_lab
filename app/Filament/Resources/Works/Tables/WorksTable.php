<?php

namespace App\Filament\Resources\Works\Tables;

use App\Models\Work;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Table;

class WorksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('sort_order')
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('title')
                    ->label('タイトル')
                    ->searchable(),
                TextColumn::make('label')
                    ->label('ラベル')
                    ->badge()
                    ->formatStateUsing(fn (string $state): string => Work::LABELS[$state] ?? $state),
                TextColumn::make('tech')
                    ->label('技術・タグ')
                    ->badge(),
                ToggleColumn::make('is_published')
                    ->label('公開'),
                TextColumn::make('updated_at')
                    ->label('更新日時')
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
