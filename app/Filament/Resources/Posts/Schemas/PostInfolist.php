<?php

namespace App\Filament\Resources\Posts\Schemas;

use App\Models\Post;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Components\Callout;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                // ファイルから取り込んだ記事は、管理画面で直しても次の取り込みで上書きされるので、編集させない
                Callout::make('GitHub のファイルから取り込んだ記事です')
                    ->description(fn (Post $record): string => "この記事は content/posts/{$record->source_path} から自動で取り込んでいます。管理画面では編集・削除できません。直すときは GitHub のファイルを編集し、main にマージしてください。ファイルを消すと、記事は下書きに戻ります。")
                    ->warning()
                    ->columnSpanFull()
                    ->visible(fn (Post $record): bool => $record->isFromFile() && ! $record->isRemovedFileDraft()),
                Callout::make('元のファイルが無くなった下書きです')
                    ->description(fn (Post $record): string => "content/posts/{$record->source_path} が無くなったため、下書きに戻っています。ファイルを戻せば、次の取り込みで再び公開されます。不要なら、右上の「削除」で消せます。")
                    ->info()
                    ->columnSpanFull()
                    ->visible(fn (Post $record): bool => $record->isRemovedFileDraft()),
                Section::make('記事')
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('title')->label('タイトル'),
                        TextEntry::make('body')->label('本文')->markdown()->columnSpanFull(),
                    ]),
                Section::make('設定')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextEntry::make('slug')
                            ->label('URL（スラッグ）')
                            ->url(fn (Post $record): ?string => $record->isPublished() ? route('posts.show', $record) : null, shouldOpenInNewTab: true),
                        TextEntry::make('status')
                            ->label('状態')
                            ->state(fn (Post $record): string => $record->statusLabel())
                            ->badge(),
                        TextEntry::make('category')->label('カテゴリ')->placeholder('なし'),
                        TextEntry::make('tags')->label('タグ')->badge()->placeholder('なし'),
                        TextEntry::make('published_at')->label('公開日時')->dateTime('Y/m/d H:i')->placeholder('下書き'),
                        TextEntry::make('source')
                            ->label('出どころ')
                            ->state(fn (Post $record): string => $record->sourceLabel()),
                        TextEntry::make('source_path')
                            ->label('ファイル')
                            ->state(fn (Post $record): ?string => $record->source_path ? "content/posts/{$record->source_path}" : null)
                            ->visible(fn (Post $record): bool => $record->isFromFile()),
                        TextEntry::make('excerpt')->label('抜粋（meta description）')->placeholder('なし')->columnSpanFull(),
                    ]),
            ]);
    }
}
