<?php

namespace App\Filament\Pages;

use App\Models\SeoSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use UnitEnum;

class MetaPixelSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|\BackedEnum|null $navigationIcon = 'brand-meta';

    protected string $view = 'filament.pages.meta-pixel-settings';

    protected static ?string $navigationLabel = 'Meta Pixel';

    protected static ?string $title = 'Meta Pixel';

    protected static ?string $slug = 'meta-pixel';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 47;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $record = SeoSetting::query()->first();

        $this->form->fill([
            'enabled' => $record !== null ? $record->meta_pixel_enabled : false,
            'pixels' => [],
            'lead_event_enabled' => true,
            'lead_event_name' => 'Lead',
            ...($record?->meta_pixel ?? []),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pixels')
                    ->description('Loads the Meta (Facebook / Instagram) Pixel on every public page. Find the Pixel ID in Meta Events Manager → Data sources → your pixel. Add more than one pixel to report to several ad accounts.')
                    ->icon('brand-meta')
                    ->schema([
                        Toggle::make('enabled')
                            ->label('Enable Meta Pixel')
                            ->live(),

                        Repeater::make('pixels')
                            ->label('Pixels')
                            ->addActionLabel('Add pixel')
                            ->required(fn (Get $get): bool => (bool) $get('enabled'))
                            ->itemLabel(fn (array $state): ?string => $state['pixel_id'] ?? null)
                            ->collapsible()
                            ->schema([
                                TextInput::make('pixel_id')
                                    ->label('Pixel ID')
                                    ->placeholder('123456789012345')
                                    ->required()
                                    ->dehydrateStateUsing(fn (?string $state): string => trim((string) $state))
                                    ->regex('/^\s*\d{10,20}\s*$/')
                                    ->helperText('Digits only, e.g. 123456789012345.'),
                            ]),
                    ]),

                Section::make('Events')
                    ->description('PageView is tracked automatically on every page. Choose the conversion event sent when the contact / waitlist form is submitted successfully.')
                    ->icon('heroicon-o-bolt')
                    ->schema([
                        Toggle::make('lead_event_enabled')
                            ->label('Track contact / waitlist form submissions')
                            ->live(),

                        Select::make('lead_event_name')
                            ->label('Event')
                            ->options(SeoSetting::META_PIXEL_EVENTS)
                            ->required(fn (Get $get): bool => (bool) $get('lead_event_enabled'))
                            ->selectablePlaceholder(false)
                            ->visible(fn (Get $get): bool => (bool) $get('lead_event_enabled'))
                            ->dehydratedWhenHidden(),
                    ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save Settings')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $enabled = (bool) ($data['enabled'] ?? false);
        unset($data['enabled']);

        $data['pixels'] = array_values($data['pixels'] ?? []);

        SeoSetting::getSettings();

        SeoSetting::query()->firstOrFail()->update([
            'meta_pixel_enabled' => $enabled,
            'meta_pixel' => $data,
        ]);

        Notification::make()
            ->title('Meta Pixel settings saved')
            ->success()
            ->send();
    }
}
