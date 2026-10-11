<?php

namespace App\Filament\Resources\Redirects;

use App\Filament\Resources\Redirects\Pages\ManageRedirects;
use App\Models\Redirect;
use BackedEnum;
use Closure;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Field;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use UnitEnum;

class RedirectResource extends Resource
{
    protected static ?string $model = Redirect::class;

    protected static string|BackedEnum|null $navigationIcon = 'heroicon-o-arrow-uturn-right';

    protected static ?string $navigationLabel = 'Redirects';

    protected static ?string $modelLabel = 'Redirect';

    protected static ?string $recordTitleAttribute = 'from_path';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 51;

    /** @return array<int, string> */
    public static function statusOptions(): array
    {
        return [
            301 => '301 Permanent',
            302 => '302 Temporary',
            307 => '307 Temporary (keep method)',
            308 => '308 Permanent (keep method)',
        ];
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('from_path')
                    ->label('From path')
                    ->placeholder('/old-page')
                    ->required()
                    ->maxLength(255)
                    ->dehydrateStateUsing(fn (?string $state): string => Redirect::normalizePath((string) $state))
                    ->rules([
                        fn (Get $get, Field $component): Closure => function (string $attribute, mixed $value, Closure $fail) use ($get, $component): void {
                            $value = (string) $value;

                            if (! str_starts_with($value, '/')) {
                                $fail('The path must start with "/".');

                                return;
                            }

                            if (Redirect::isProtectedPath($value)) {
                                $fail('This path is reserved and cannot be redirected.');

                                return;
                            }

                            $isDuplicate = Redirect::query()
                                ->where('from_path', Redirect::normalizePath($value))
                                ->when($component->getRecord()?->getKey(), fn ($query, $id) => $query->whereKeyNot($id))
                                ->exists();

                            if ($isDuplicate) {
                                $fail('This path already has a redirect.');

                                return;
                            }

                            $target = (string) $get('to_url');
                            $targetPath = parse_url($target, PHP_URL_PATH);

                            if (is_string($targetPath) && Redirect::normalizePath($targetPath) === Redirect::normalizePath($value)
                                && (str_starts_with($target, '/') || parse_url($target, PHP_URL_HOST) === parse_url((string) config('app.url'), PHP_URL_HOST))) {
                                $fail('The path cannot redirect to itself.');
                            }
                        },
                    ])
                    ->helperText('Stored lowercase without trailing slash.'),
                TextInput::make('to_url')
                    ->label('Redirect to')
                    ->placeholder('/new-page or https://example.com')
                    ->required()
                    ->maxLength(2048)
                    ->rules([
                        fn (): Closure => function (string $attribute, mixed $value, Closure $fail): void {
                            $value = (string) $value;
                            $isPath = str_starts_with($value, '/') && ! str_starts_with($value, '//');

                            if (! $isPath && ! preg_match('#^https?://#i', $value) || (! $isPath && filter_var($value, FILTER_VALIDATE_URL) === false)) {
                                $fail('Enter a valid http(s) URL or a path starting with "/".');
                            }
                        },
                    ]),
                Select::make('status_code')
                    ->options(self::statusOptions())
                    ->default(301)
                    ->required()
                    ->selectablePlaceholder(false),
                Toggle::make('is_active')
                    ->label('Active')
                    ->default(true),
                Toggle::make('preserve_query_string')
                    ->label('Preserve query string')
                    ->default(true),
                Textarea::make('notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('from_path')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('to_url')
                    ->limit(50)
                    ->searchable(),
                TextColumn::make('status_code')
                    ->label('Status')
                    ->badge()
                    ->sortable(),
                ToggleColumn::make('is_active')
                    ->label('Active'),
                TextColumn::make('hits')
                    ->sortable(),
                TextColumn::make('last_hit_at')
                    ->label('Last hit')
                    ->since()
                    ->placeholder('Never')
                    ->sortable(),
            ])
            ->filters([
                TernaryFilter::make('is_active')->label('Active'),
                SelectFilter::make('status_code')
                    ->label('Status')
                    ->options(self::statusOptions()),
            ])
            ->recordActions([
                Action::make('test')
                    ->label('Test')
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->url(fn (Redirect $record): string => url($record->from_path), shouldOpenInNewTab: true),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('from_path')
            ->emptyStateHeading('No redirects yet');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageRedirects::route('/'),
        ];
    }
}
