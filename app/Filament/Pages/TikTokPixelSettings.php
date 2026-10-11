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

class TikTokPixelSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|\BackedEnum|null $navigationIcon = 'brand-tiktok';

    protected string $view = 'filament.pages.tiktok-pixel-settings';

    protected static ?string $navigationLabel = 'TikTok Pixel';

    protected static ?string $title = 'TikTok Pixel';

    protected static ?string $slug = 'tiktok-pixel';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 48;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $record = SeoSetting::query()->first();

        $this->form->fill([
            'enabled' => $record !== null ? $record->tiktok_pixel_enabled : false,
            'pixels' => [],
            'lead_event_enabled' => true,
            'lead_event_name' => 'Lead',
            ...($record?->tiktok_pixel ?? []),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Pixels')
                    ->description('Loads the TikTok Pixel on every public page. Find the Pixel ID in TikTok Ads Manager → Tools → Events → Web events → your pixel. Add more than one pixel to report to several ad accounts.')
                    ->icon('brand-tiktok')
                    ->schema([
                        Toggle::make('enabled')
                            ->label('Enable TikTok Pixel')
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
                                    ->placeholder('C1A2B3C4D5E6F7G8H9I0')
                                    ->required()
                                    ->dehydrateStateUsing(fn (?string $state): string => strtoupper(trim((string) $state)))
                                    ->regex('/^\s*[A-Za-z0-9]{15,30}\s*$/')
                                    ->helperText('Letters and digits, e.g. C1A2B3C4D5E6F7G8H9I0.'),
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
                            ->options(SeoSetting::TIKTOK_PIXEL_EVENTS)
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
            'tiktok_pixel_enabled' => $enabled,
            'tiktok_pixel' => $data,
        ]);

        Notification::make()
            ->title('TikTok Pixel settings saved')
            ->success()
            ->send();
    }
}
