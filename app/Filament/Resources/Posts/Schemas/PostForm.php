<?php

namespace App\Filament\Resources\Posts\Schemas;

use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class PostForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('記事')
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label('タイトル')
                            ->required()
                            ->maxLength(255),
                        MarkdownEditor::make('body')
                            ->label('本文（Markdown）')
                            ->required(),
                    ]),
                Section::make('設定')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('slug')
                            ->label('URL（スラッグ）')
                            ->helperText('/blog/〇〇 の〇〇部分。半角英数字とハイフン')
                            ->required()
                            ->alphaDash()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        TextInput::make('category')
                            ->label('カテゴリ')
                            ->maxLength(50),
                        Textarea::make('excerpt')
                            ->label('抜粋（空なら本文の冒頭を使う）')
                            ->maxLength(300)
                            ->rows(2)
                            ->columnSpanFull(),
                        DateTimePicker::make('published_at')
                            ->label('公開日時')
                            ->helperText('空欄なら下書き。未来の日時にすると予約投稿になります')
                            ->seconds(false),
                    ]),
            ]);
    }
}
