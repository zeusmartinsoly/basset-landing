<?php

namespace App\Filament\Pages;

use App\Models\SeoSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\KeyValue;
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
use Illuminate\Support\Str;
use UnitEnum;

class GoogleTagManagerSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|\BackedEnum|null $navigationIcon = 'brand-google-tag-manager';

    protected string $view = 'filament.pages.google-tag-manager-settings';

    protected static ?string $navigationLabel = 'Google Tag Manager';

    protected static ?string $title = 'Google Tag Manager';

    protected static ?string $slug = 'google-tag-manager';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 44;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $record = SeoSetting::query()->first();

        $this->form->fill([
            'enabled' => $record !== null ? $record->google_tag_manager_enabled : false,
            'containers' => [],
            'data_layer_name' => 'dataLayer',
            'lead_event_enabled' => true,
            'lead_event_name' => 'generate_lead',
            'consent' => array_fill_keys(SeoSetting::GOOGLE_CONSENT_TYPES, 'denied'),
            ...($record?->google_tag_manager ?? []),
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Containers')
                    ->description('Load one or more GTM containers on every public page. Container IDs come from tagmanager.google.com (top bar, e.g. GTM-ABC1234). Use Settings → Google Analytics only for a direct GA4 tag; if GA4 is already configured inside GTM, leave it disabled to avoid double counting.')
                    ->icon('brand-google-tag-manager')
                    ->schema([
                        Toggle::make('enabled')
                            ->label('Enable Google Tag Manager')
                            ->live(),

                        Repeater::make('containers')
                            ->label('Containers')
                            ->addActionLabel('Add container')
                            ->required(fn (Get $get): bool => (bool) $get('enabled'))
                            ->itemLabel(fn (array $state): ?string => $state['container_id'] ?? null)
                            ->collapsible()
                            ->columns(3)
                            ->schema([
                                TextInput::make('container_id')
                                    ->label('Container ID')
                                    ->placeholder('GTM-XXXXXXX')
                                    ->required()
                                    ->dehydrateStateUsing(fn (?string $state): string => Str::upper(trim((string) $state)))
                                    ->regex('/^\s*[Gg][Tt][Mm]-[A-Za-z0-9]{4,12}\s*$/'),

                                TextInput::make('environment_auth')
                                    ->label('Environment gtm_auth')
                                    ->placeholder('Optional')
                                    ->helperText('Admin → Environments → Get snippet.')
                                    ->requiredWith('environment_preview')
                                    ->regex('/^[A-Za-z0-9_-]+$/'),

                                TextInput::make('environment_preview')
                                    ->label('Environment gtm_preview')
                                    ->placeholder('env-3')
                                    ->requiredWith('environment_auth')
                                    ->regex('/^env-\d+$/'),
                            ]),
                    ]),

                Section::make('Loading')
                    ->description('Advanced loader options.')
                    ->icon('heroicon-o-server-stack')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        TextInput::make('data_layer_name')
                            ->label('Data layer name')
                            ->required()
                            ->regex('/^[A-Za-z_$][A-Za-z0-9_$]*$/')
                            ->helperText('Keep "dataLayer" unless your container expects a custom name.'),

                        TextInput::make('server_container_url')
                            ->label('Server-side tagging URL')
                            ->placeholder('https://sgtm.example.com')
                            ->url()
                            ->startsWith(['https://'])
                            ->helperText('Optional. Loads gtm.js from your server container / first-party domain instead of googletagmanager.com.'),
                    ]),

                Section::make('Consent Mode v2')
                    ->description('Sends a default consent state before any container loads. Your consent banner (CMP) should then send the "update" command.')
                    ->icon('heroicon-o-shield-check')
                    ->collapsed()
                    ->columns(2)
                    ->schema([
                        Toggle::make('consent_mode_enabled')
                            ->label('Enable Consent Mode defaults')
                            ->live()
                            ->columnSpanFull(),

                        ...collect(SeoSetting::GOOGLE_CONSENT_TYPES)
                            ->map(fn (string $type): Select => Select::make("consent.{$type}")
                                ->label($type)
                                ->options(['denied' => 'Denied', 'granted' => 'Granted'])
                                ->default('denied')
                                ->selectablePlaceholder(false)
                                ->visible(fn (Get $get): bool => (bool) $get('consent_mode_enabled')))
                            ->all(),

                        TextInput::make('consent_wait_for_update')
                            ->label('wait_for_update (ms)')
                            ->numeric()
                            ->minValue(0)
                            ->maxValue(10000)
                            ->placeholder('500')
                            ->visible(fn (Get $get): bool => (bool) $get('consent_mode_enabled')),

                        Toggle::make('ads_data_redaction')
                            ->label('Redact ads data when ad_storage is denied'),

                        Toggle::make('url_passthrough')
                            ->label('URL passthrough'),
                    ]),

                Section::make('Data Layer')
                    ->description('Variables pushed to the data layer on every page load (before containers fire), and events sent from the site.')
                    ->icon('heroicon-o-variable')
                    ->collapsed()
                    ->schema([
                        KeyValue::make('data_layer_variables')
                            ->label('Page variables')
                            ->keyLabel('Variable')
                            ->valueLabel('Value')
                            ->addActionLabel('Add variable')
                            ->rules([
                                fn (): \Closure => function (string $attribute, mixed $value, \Closure $fail): void {
                                    foreach (array_keys((array) $value) as $key) {
                                        if (preg_match('/^[A-Za-z0-9_.]{1,64}$/', (string) $key) !== 1) {
                                            $fail("Variable \"{$key}\" may only contain letters, numbers, dots and underscores.");
                                        }
                                    }
                                },
                            ]),

                        Toggle::make('lead_event_enabled')
                            ->label('Push an event when the contact / waitlist form is submitted')
                            ->live(),

                        TextInput::make('lead_event_name')
                            ->label('Event name')
                            ->required(fn (Get $get): bool => (bool) $get('lead_event_enabled'))
                            ->regex('/^[A-Za-z0-9_]{1,40}$/')
                            ->helperText('Pushed as { event: "<name>", form_id: "contact_waitlist" }. Use it as a Custom Event trigger in GTM.')
                            ->visible(fn (Get $get): bool => (bool) $get('lead_event_enabled')),
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

        $data['containers'] = array_values($data['containers'] ?? []);

        SeoSetting::getSettings();

        SeoSetting::query()->firstOrFail()->update([
            'google_tag_manager_enabled' => $enabled,
            'google_tag_manager' => $data,
        ]);

        Notification::make()
            ->title('Google Tag Manager settings saved')
            ->success()
            ->send();
    }
}
