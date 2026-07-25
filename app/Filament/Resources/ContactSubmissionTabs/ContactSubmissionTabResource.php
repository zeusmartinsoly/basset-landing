<?php

namespace App\Filament\Resources\ContactSubmissionTabs;

use App\Filament\Resources\ContactSubmissionTabs\Pages\ManageContactSubmissionTabs;
use App\Models\ContactSubmissionTab;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class ContactSubmissionTabResource extends Resource
{
    protected static ?string $model = ContactSubmissionTab::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedRectangleStack;

    protected static ?string $navigationLabel = 'Contact tabs';

    protected static ?string $modelLabel = 'Tab';

    protected static ?string $pluralModelLabel = 'Contact tabs';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'Inbox';

    protected static ?int $navigationSort = 31;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->label('Tab name')
                    ->placeholder('Round 3 Users')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Tab name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('submissions_count')
                    ->label('Submissions')
                    ->counts('submissions')
                    ->badge()
                    ->sortable(),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('name')
            ->emptyStateHeading('No tabs yet')
            ->emptyStateDescription('Create a tab such as "Round 3 Users", then move submissions into it from the Contact submissions table.');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageContactSubmissionTabs::route('/'),
        ];
    }
}
