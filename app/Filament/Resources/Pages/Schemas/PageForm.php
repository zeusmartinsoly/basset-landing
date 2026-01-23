<?php

namespace App\Filament\Resources\Pages\Schemas;

use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Illuminate\Support\Str;

class PageForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Page Content')
                    ->description('Main page content')
                    ->schema([
                        TextInput::make('title')
                            ->label('Title')
                            ->required()
                            ->maxLength(255)
                            ->live(onBlur: true)
                            ->afterStateUpdated(fn ($state, callable $set, $operation) => $operation === 'create' ? $set('slug', Str::slug($state)) : null),

                        TextInput::make('slug')
                            ->label('Slug (URL)')
                            ->required()
                            ->unique(ignoreRecord: true)
                            ->maxLength(255)
                            ->helperText('Used in the URL: /page/{slug}'),

                        RichEditor::make('content')
                            ->label('Content')
                            ->required()
                            ->columnSpanFull(),

                        Toggle::make('is_active')
                            ->label('Active')
                            ->default(true)
                            ->helperText('Inactive pages will not be accessible'),
                    ])
                    ->columns(2),

                Section::make('SEO Settings')
                    ->description('Search engine optimization')
                    ->schema([
                        TextInput::make('meta_title')
                            ->label('Meta Title')
                            ->maxLength(255)
                            ->helperText('Leave empty to use page title'),

                        Textarea::make('meta_description')
                            ->label('Meta Description')
                            ->maxLength(500)
                            ->rows(3),
                    ])
                    ->collapsible()
                    ->collapsed(),
            ]);
    }
}
