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
use UnitEnum;

class GoogleSearchConsoleSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|\BackedEnum|null $navigationIcon = 'brand-google-search-console';

    protected string $view = 'filament.pages.google-search-console-settings';

    protected static ?string $navigationLabel = 'Google Search Console';

    protected static ?string $title = 'Google Search Console';

    protected static ?string $slug = 'google-search-console';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 43;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $record = SeoSetting::query()->first();

        $this->form->fill([
            'google_search_console_enabled' => $record !== null ? $record->google_search_console_enabled : false,
            'google_site_verification' => $record?->google_site_verification ?? '',
            'google_search_console_html_file' => $record?->google_search_console_html_file ?? '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Ownership Verification')
                    ->description(
                        'In Search Console add a "URL prefix" property for this site, then use the HTML tag or HTML file method below. The Google Analytics / Tag Manager methods work automatically once Settings → Google Analytics is enabled. The Domain (DNS TXT record) method is configured at your domain registrar, not here.',
                    )
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Toggle::make('google_search_console_enabled')
                            ->label('Enable Google Search Console verification')
                            ->default(false)
                            ->live(),

                        TextInput::make('google_site_verification')
                            ->label('HTML tag')
                            ->placeholder('<meta name="google-site-verification" content="..." /> or just the content value')
                            ->helperText('Search Console → Settings → Ownership verification → HTML tag. Paste the whole tag or only its content value.'),

                        TextInput::make('google_search_console_html_file')
                            ->label('HTML file name')
                            ->placeholder('google1234567890abcdef.html')
                            ->helperText('Search Console → HTML file. Paste only the file name; the site serves it automatically at its root, no upload needed.'),
                    ])
                    ->columns(1),

                Section::make('Sitemap')
                    ->description('After verifying, submit this URL in Search Console → Sitemaps: '.route('sitemap'))
                    ->icon('heroicon-o-map'),
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

        $enabled = (bool) ($data['google_search_console_enabled'] ?? false);
        $verificationCode = $this->extractVerificationCode((string) ($data['google_site_verification'] ?? ''));
        $htmlFile = trim((string) ($data['google_search_console_html_file'] ?? ''));

        Validator::validate(
            [
                'enabled' => $enabled,
                'google_site_verification' => $verificationCode,
                'google_search_console_html_file' => $htmlFile,
            ],
            [
                'enabled' => ['boolean'],
                'google_site_verification' => [
                    $enabled ? 'required_without:google_search_console_html_file' : 'nullable',
                    'string',
                    'regex:/^[A-Za-z0-9_-]{10,100}$/',
                ],
                'google_search_console_html_file' => ['nullable', 'string', 'regex:/^google[a-z0-9]+\.html$/'],
            ],
            [
                'google_site_verification.required_without' => 'Provide the HTML tag or the HTML file name to enable verification.',
            ],
            [
                'google_site_verification' => 'HTML tag',
                'google_search_console_html_file' => 'HTML file name',
            ]
        );

        SeoSetting::getSettings();

        SeoSetting::query()->firstOrFail()->update([
            'google_search_console_enabled' => $enabled,
            'google_site_verification' => $verificationCode !== '' ? $verificationCode : null,
            'google_search_console_html_file' => $htmlFile !== '' ? $htmlFile : null,
        ]);

        $this->form->fill([
            'google_search_console_enabled' => $enabled,
            'google_site_verification' => $verificationCode,
            'google_search_console_html_file' => $htmlFile,
        ]);

        Notification::make()
            ->title('Google Search Console settings saved')
            ->success()
            ->send();
    }

    /**
     * Accept either the full meta tag or only its content value.
     */
    protected function extractVerificationCode(string $input): string
    {
        $input = trim($input);

        if (preg_match('/content\s*=\s*["\']([^"\']+)["\']/i', $input, $matches) === 1) {
            return trim($matches[1]);
        }

        return $input;
    }
}
