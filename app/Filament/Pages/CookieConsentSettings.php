<?php

namespace App\Filament\Pages;

use App\Models\SeoSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use UnitEnum;

class CookieConsentSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-finger-print';

    protected string $view = 'filament.pages.cookie-consent-settings';

    protected static ?string $navigationLabel = 'Cookie Consent';

    protected static ?string $title = 'Cookie Consent';

    protected static ?string $slug = 'cookie-consent';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 42;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $record = SeoSetting::query()->first();

        $this->form->fill([
            ...SeoSetting::COOKIE_CONSENT_DEFAULTS,
            ...($record?->cookie_consent ?? []),
            'enabled' => $record !== null ? $record->cookie_consent_enabled : false,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Cookie Consent Banner')
                    ->description('Asks visitors for consent and passes their choice to Google (Consent Mode v2 via GA4 / GTM), Meta Pixel, TikTok Pixel and Microsoft Clarity. Until a visitor accepts, analytics and marketing storage stay "denied". Custom Scripts are not controlled by the banner.')
                    ->icon('heroicon-o-finger-print')
                    ->schema([
                        Toggle::make('enabled')
                            ->label('Show cookie consent banner'),
                    ]),

                Tabs::make('Banner')
                    ->tabs([
                        Tab::make('Content')
                            ->icon('heroicon-o-chat-bubble-bottom-center-text')
                            ->columns(2)
                            ->schema([
                                TextInput::make('title')->label('Title')->required()->maxLength(120)->columnSpanFull(),
                                Textarea::make('message')->label('Message')->required()->rows(3)->maxLength(1000)->columnSpanFull(),
                                TextInput::make('accept_label')->label('"Accept all" button')->required()->maxLength(40),
                                TextInput::make('reject_label')->label('"Reject" button')->required()->maxLength(40),
                                TextInput::make('customize_label')->label('"Customize" button')->required()->maxLength(40),
                                TextInput::make('save_label')->label('"Save choices" button')->required()->maxLength(40),
                                TextInput::make('privacy_link_label')->label('Privacy link text')->maxLength(60),
                                TextInput::make('privacy_link_url')
                                    ->label('Privacy link URL')
                                    ->placeholder('/page/privacy-policy or https://…')
                                    ->regex('#^(/(?!/)|https?://)#i')
                                    ->maxLength(255)
                                    ->helperText('Leave empty to hide the link.'),
                            ]),

                        Tab::make('Categories')
                            ->icon('heroicon-o-squares-2x2')
                            ->columns(2)
                            ->schema([
                                TextInput::make('necessary_label')->label('Necessary: label')->required()->maxLength(60),
                                TextInput::make('necessary_description')->label('Necessary: description')->required()->maxLength(300),

                                ...$this->categoryFields('analytics', 'Analytics (analytics_storage, GA4, Clarity)'),
                                ...$this->categoryFields('marketing', 'Marketing (ad_storage, ad_user_data, ad_personalization, Meta, TikTok)'),
                                ...$this->categoryFields('functional', 'Functional (functionality_storage, personalization_storage)'),
                            ]),

                        Tab::make('Appearance')
                            ->icon('heroicon-o-paint-brush')
                            ->columns(2)
                            ->schema([
                                Select::make('layout')->label('Layout')->options(SeoSetting::COOKIE_CONSENT_LAYOUTS)->required()->selectablePlaceholder(false),
                                Select::make('direction')->label('Text direction')->options(['rtl' => 'Right to left (Arabic)', 'ltr' => 'Left to right'])->required()->selectablePlaceholder(false),
                                ColorPicker::make('background_color')->label('Background')->required()->regex('/^#[0-9a-fA-F]{6}$/'),
                                ColorPicker::make('text_color')->label('Text')->required()->regex('/^#[0-9a-fA-F]{6}$/'),
                                ColorPicker::make('primary_color')->label('Accept button background')->required()->regex('/^#[0-9a-fA-F]{6}$/'),
                                ColorPicker::make('primary_text_color')->label('Accept button text')->required()->regex('/^#[0-9a-fA-F]{6}$/'),
                                ColorPicker::make('secondary_color')->label('Secondary buttons / borders')->required()->regex('/^#[0-9a-fA-F]{6}$/'),
                                TextInput::make('border_radius')->label('Corner radius (px)')->numeric()->integer()->minValue(0)->maxValue(32)->required(),
                                Toggle::make('show_reject_button')->label('Show "Reject" button on the first layer'),
                                Toggle::make('show_reopen_button')->label('Show floating button to change choices later')->live(),
                                Select::make('reopen_position')
                                    ->label('Floating button side')
                                    ->options(['start' => 'Start side', 'end' => 'End side'])
                                    ->selectablePlaceholder(false)
                                    ->visible(fn (Get $get): bool => (bool) $get('show_reopen_button'))
                                    ->dehydratedWhenHidden(),
                                TextInput::make('reopen_label')
                                    ->label('Floating button label (screen readers / tooltip)')
                                    ->maxLength(60)
                                    ->visible(fn (Get $get): bool => (bool) $get('show_reopen_button'))
                                    ->dehydratedWhenHidden(),
                            ]),

                        Tab::make('Behavior')
                            ->icon('heroicon-o-clock')
                            ->columns(2)
                            ->schema([
                                TextInput::make('expiry_days')
                                    ->label('Remember choice for (days)')
                                    ->numeric()
                                    ->integer()
                                    ->minValue(1)
                                    ->maxValue(395)
                                    ->required()
                                    ->helperText('Max 395 days (13 months) per common privacy guidance.'),
                                TextInput::make('version')
                                    ->label('Consent version')
                                    ->numeric()
                                    ->integer()
                                    ->minValue(1)
                                    ->required()
                                    ->helperText('Increase to ask every visitor again (or use "Ask everyone again" above).'),
                            ]),
                    ]),
            ])
            ->statePath('data');
    }

    /**
     * @return list<Toggle|TextInput>
     */
    protected function categoryFields(string $category, string $title): array
    {
        return [
            Toggle::make("{$category}_enabled")
                ->label("{$title}: show in banner")
                ->live()
                ->columnSpanFull(),
            TextInput::make("{$category}_label")
                ->label('Label')
                ->maxLength(60)
                ->required(fn (Get $get): bool => (bool) $get("{$category}_enabled"))
                ->visible(fn (Get $get): bool => (bool) $get("{$category}_enabled"))
                ->dehydratedWhenHidden(),
            TextInput::make("{$category}_description")
                ->label('Description')
                ->maxLength(300)
                ->required(fn (Get $get): bool => (bool) $get("{$category}_enabled"))
                ->visible(fn (Get $get): bool => (bool) $get("{$category}_enabled"))
                ->dehydratedWhenHidden(),
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('resetConsents')
                ->label('Ask everyone again')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalDescription('Every visitor will see the banner again on their next visit, because the consent version is increased.')
                ->action(function (): void {
                    $this->data['version'] = (int) ($this->data['version'] ?? 1) + 1;
                    $this->save();
                }),

            Action::make('restoreDefaults')
                ->label('Restore default texts & design')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('gray')
                ->requiresConfirmation()
                ->action(function (): void {
                    $this->form->fill([
                        ...SeoSetting::COOKIE_CONSENT_DEFAULTS,
                        'enabled' => (bool) ($this->data['enabled'] ?? false),
                        'version' => (int) ($this->data['version'] ?? 1),
                    ]);
                }),
        ];
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

        SeoSetting::getSettings();

        SeoSetting::query()->firstOrFail()->update([
            'cookie_consent_enabled' => $enabled,
            'cookie_consent' => collect($data)->only(array_keys(SeoSetting::COOKIE_CONSENT_DEFAULTS))->all(),
        ]);

        Notification::make()
            ->title('Cookie consent settings saved')
            ->success()
            ->send();
    }
}
