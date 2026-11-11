<?php

namespace App\Filament\Resources\Inquiries\Schemas;

use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;

class InquiryInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('name')
                    ->label('お名前'),
                TextEntry::make('email')
                    ->label('メールアドレス')
                    ->copyable(),
                TextEntry::make('created_at')
                    ->label('受信日時')
                    ->dateTime('Y/m/d H:i'),
                TextEntry::make('message')
                    ->label('ご相談内容')
                    ->columnSpanFull()
                    ->extraAttributes(['class' => 'whitespace-pre-line']),
            ]);
    }
}
