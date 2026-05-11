<?php

namespace App\Filament\Pages;

use App\Models\SeoSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use UnitEnum;

class GoogleAnalyticsSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-chart-bar-square';

    protected string $view = 'filament.pages.google-analytics-settings';

    protected static ?string $navigationLabel = 'Google Analytics';

    protected static ?string $title = 'Google Analytics';

    protected static ?string $slug = 'google-analytics';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 45;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $record = SeoSetting::query()->first();

        $this->form->fill([
            'google_analytics_enabled' => $record !== null ? $record->google_analytics_enabled : false,
            'google_analytics_measurement_id' => $record?->google_analytics_measurement_id ?? '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Google Analytics 4')
                    ->description(
                        'Load the GA4 tag on every public page (leave disabled on local/testing if you prefer). When the contact/waitlist form submits successfully, the site sends GA4\'s recommended "generate_lead" event with form_id contact_waitlist. Count them in Reports → Engagement → Events, or mark generate_lead as a key/conversion event in GA4 Admin.',
                    )
                    ->icon('heroicon-o-chart-bar-square')
                    ->schema([
                        Toggle::make('google_analytics_enabled')
                            ->label('Enable Google Analytics')
                            ->default(false)
                            ->live(),

                        TextInput::make('google_analytics_measurement_id')
                            ->label('Measurement ID')
                            ->placeholder('G-XXXXXXXXXX')
                            ->helperText('From Admin → Data streams → your web stream.'),
                    ])
                    ->columns(1),
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

        $enabled = (bool) ($data['google_analytics_enabled'] ?? false);
        $measurementId = isset($data['google_analytics_measurement_id'])
            ? trim((string) $data['google_analytics_measurement_id'])
            : '';

        $rules = [
            'enabled' => ['boolean'],
        ];

        if ($enabled) {
            $rules['google_analytics_measurement_id'] = [
                'required',
                'string',
                Rule::regex('/^G-[A-Z0-9]+$/'),
            ];
        } else {
            $rules['google_analytics_measurement_id'] = ['nullable', 'string'];
        }

        Validator::validate(
            [
                'enabled' => $enabled,
                'google_analytics_measurement_id' => $measurementId,
            ],
            $rules,
            [],
            [
                'google_analytics_measurement_id' => 'Measurement ID',
            ]
        );

        SeoSetting::getSettings();

        /** @var SeoSetting $settings */
        $settings = SeoSetting::query()->firstOrFail();

        $settings->update([
            'google_analytics_enabled' => $enabled,
            'google_analytics_measurement_id' => $enabled ? $measurementId : null,
        ]);

        Notification::make()
            ->title('Google Analytics settings saved')
            ->success()
            ->send();
    }
}
