<?php

namespace App\Filament\Resources\ContactSubmissions;

use App\Filament\Resources\ContactSubmissions\Pages\ManageContactSubmissions;
use App\Models\ContactSubmission;
use App\Models\ContactSubmissionTab;
use BackedEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Columns\ToggleColumn;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Illuminate\Support\Collection;
use OpenSpout\Common\Entity\Row;
use OpenSpout\Writer\XLSX\Writer;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class ContactSubmissionResource extends Resource
{
    protected static ?string $model = ContactSubmission::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedChatBubbleBottomCenterText;

    protected static ?string $navigationLabel = 'Contact messages';

    protected static ?string $modelLabel = 'Contact submission';

    protected static ?string $pluralModelLabel = 'Contact submissions';

    protected static ?string $recordTitleAttribute = 'name';

    protected static string|UnitEnum|null $navigationGroup = 'Inbox';

    protected static ?int $navigationSort = 30;

    public static function form(Schema $schema): Schema
    {
        return $schema->components([]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->columns(1)
            ->components([
                Section::make('Submission')
                    ->description('Landing page contact / waitlist form.')
                    ->components([
                        TextEntry::make('created_at')
                            ->label('Submitted at')
                            ->dateTime(),
                        TextEntry::make('name')->label('Name'),
                        TextEntry::make('email')->label('Email')->copyable(),
                        TextEntry::make('phone')->label('Mobile')->copyable(),
                        IconEntry::make('prefers_whatsapp')
                            ->label('Prefers WhatsApp')
                            ->boolean(),
                        IconEntry::make('contacted')
                            ->label('تم التواصل')
                            ->boolean(),
                        TextEntry::make('tab.name')
                            ->label('Tab')
                            ->badge()
                            ->placeholder('Unassigned'),
                        TextEntry::make('message')
                            ->label('Message')
                            ->columnSpanFull()
                            ->wrap(),
                        TextEntry::make('ip_address')
                            ->label('IP address')
                            ->placeholder('—')
                            ->copyable(),
                        TextEntry::make('user_agent')
                            ->label('User agent')
                            ->placeholder('—')
                            ->columnSpanFull(),
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(),
                TextColumn::make('name')
                    ->searchable()
                    ->sortable(),
                TextColumn::make('email')
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('phone')
                    ->toggleable(),
                IconColumn::make('prefers_whatsapp')
                    ->label('WA')
                    ->boolean()
                    ->sortable(),
                ToggleColumn::make('contacted')
                    ->label('تم التواصل')
                    ->sortable(),
                TextColumn::make('tab.name')
                    ->label('Tab')
                    ->badge()
                    ->placeholder('Unassigned')
                    ->sortable()
                    ->toggleable(),
            ])
            ->recordActions([
                ViewAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                Action::make('exportExcel')
                    ->label('Export to Excel')
                    ->icon(Heroicon::OutlinedArrowDownTray)
                    ->color('gray')
                    ->action(fn (HasTable $livewire): StreamedResponse => static::exportToExcel($livewire)),
                BulkAction::make('moveToTab')
                    ->label('Move to tab')
                    ->icon(Heroicon::OutlinedFolderArrowDown)
                    ->schema([
                        Select::make('contact_submission_tab_id')
                            ->label('Tab')
                            ->placeholder('Unassigned')
                            ->options(fn (): array => ContactSubmissionTab::query()->orderBy('name')->pluck('name', 'id')->all())
                            ->searchable()
                            ->createOptionForm([
                                TextInput::make('name')
                                    ->label('Tab name')
                                    ->required()
                                    ->maxLength(255)
                                    ->unique(ContactSubmissionTab::class),
                            ])
                            ->createOptionUsing(fn (array $data): int => ContactSubmissionTab::create($data)->getKey()),
                    ])
                    ->action(function (Collection $records, array $data): void {
                        ContactSubmission::query()
                            ->whereKey($records->modelKeys())
                            ->update(['contact_submission_tab_id' => $data['contact_submission_tab_id'] ?: null]);
                    })
                    ->deselectRecordsAfterCompletion()
                    ->successNotificationTitle('Submissions moved'),
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    /**
     * Stream every submission currently matching the table's filters/search as an .xlsx file.
     */
    protected static function exportToExcel(HasTable $livewire): StreamedResponse
    {
        $query = ($livewire->getFilteredSortedTableQuery() ?? ContactSubmission::query())->with('tab');

        return response()->streamDownload(function () use ($query): void {
            $writer = new Writer;
            $writer->openToFile('php://output');
            $writer->addRow(Row::fromValues([
                'Submitted at',
                'Name',
                'Email',
                'Mobile',
                'Prefers WhatsApp',
                'تم التواصل',
                'Tab',
                'Message',
                'IP address',
                'User agent',
            ]));

            // ponytail: cursor() streams row by row; swap for chunkById only if the table outgrows memory
            foreach ($query->cursor() as $submission) {
                $writer->addRow(Row::fromValues([
                    $submission->created_at?->format('Y-m-d H:i'),
                    $submission->name,
                    $submission->email,
                    $submission->phone,
                    $submission->prefers_whatsapp ? 'Yes' : 'No',
                    $submission->contacted ? 'Yes' : 'No',
                    $submission->tab?->name,
                    $submission->message,
                    $submission->ip_address,
                    $submission->user_agent,
                ]));
            }

            $writer->close();
        }, 'contact-submissions-'.now()->format('Y-m-d-His').'.xlsx');
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageContactSubmissions::route('/'),
        ];
    }
}
