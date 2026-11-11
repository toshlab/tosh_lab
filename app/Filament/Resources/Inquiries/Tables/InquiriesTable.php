<?php

namespace App\Filament\Resources\Inquiries\Tables;

use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InquiriesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->defaultSort('created_at', 'desc')
            ->columns([
                IconColumn::make('read_at')
                    ->label('既読')
                    ->state(fn ($record): bool => $record->read_at !== null)
                    ->boolean(),
                TextColumn::make('name')
                    ->label('お名前')
                    ->searchable(),
                TextColumn::make('email')
                    ->label('メールアドレス')
                    ->searchable(),
                TextColumn::make('message')
                    ->label('ご相談内容')
                    ->limit(40),
                TextColumn::make('created_at')
                    ->label('受信日時')
                    ->dateTime('Y/m/d H:i')
                    ->sortable(),
            ])
            ->recordActions([
                ViewAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }
}
