<?php

namespace App\Filament\Resources\Works\Schemas;

use App\Models\Work;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class WorkForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('基本情報')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('title')
                            ->label('タイトル')
                            ->required()
                            ->maxLength(255),
                        TextInput::make('slug')
                            ->label('URL（スラッグ）')
                            ->helperText('/works/〇〇 の〇〇部分。半角英数字とハイフン')
                            ->required()
                            ->alphaDash()
                            ->maxLength(255)
                            ->unique(ignoreRecord: true),
                        // 実案件でないものを実案件に見せないため、ラベルは必須
                        Select::make('label')
                            ->label('ラベル')
                            ->options(Work::LABELS)
                            ->default('sample')
                            ->required(),
                        TagsInput::make('tech')
                            ->label('技術・タグ')
                            ->placeholder('Laravel など'),
                        Textarea::make('summary')
                            ->label('概要（一覧に表示）')
                            ->required()
                            ->maxLength(500)
                            ->rows(3)
                            ->columnSpanFull(),
                    ]),
                Section::make('詳細ページ')
                    ->description('本文を入れると「詳しく見る」から詳細ページが開けるようになります')
                    ->columnSpanFull()
                    ->schema([
                        MarkdownEditor::make('body')
                            ->label('本文（Markdown）'),
                        FileUpload::make('thumbnail')
                            ->label('サムネイル画像')
                            ->image()
                            ->disk('public')
                            ->directory('works')
                            ->maxSize(2048),
                    ]),
                Section::make('リンク・公開設定')
                    ->columns(2)
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('demo_url')
                            ->label('デモのURL')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('github_url')
                            ->label('GitHubのURL')
                            ->url()
                            ->maxLength(255),
                        TextInput::make('sort_order')
                            ->label('並び順（小さいほど先）')
                            ->numeric()
                            ->default(0)
                            ->required(),
                        Toggle::make('is_published')
                            ->label('サイトに公開する'),
                    ]),
            ]);
    }
}
