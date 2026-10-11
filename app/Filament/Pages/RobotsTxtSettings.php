<?php

namespace App\Filament\Pages;

use App\Models\SeoSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use UnitEnum;

class RobotsTxtSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.robots-txt-settings';

    protected static ?string $navigationLabel = 'robots.txt';

    protected static ?string $title = 'robots.txt';

    protected static ?string $slug = 'robots-txt';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 50;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $record = SeoSetting::query()->first();

        $this->form->fill([
            'robots_txt' => filled($record?->robots_txt) ? $record->robots_txt : SeoSetting::DEFAULT_ROBOTS_TXT,
            'robots_txt_append_sitemap' => $record !== null ? $record->robots_txt_append_sitemap : true,
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('robots.txt')
                    ->description('Served at '.route('robots').' (written to public/robots.txt on save; run "php artisan seo:robots-txt" after each deploy). Tells search engine crawlers which paths they may crawl. Leave empty to use the default rules.')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        CodeEditor::make('robots_txt')
                            ->label('Rules')
                            ->rules(['nullable', 'string', 'max:20000'])
                            ->helperText('One directive per line, e.g. "User-agent: *", "Disallow: /admin". Blocking "/" hides the whole site from search engines.'),

                        Toggle::make('robots_txt_append_sitemap')
                            ->label('Append sitemap line automatically')
                            ->helperText('Adds "Sitemap: '.rtrim((string) config('app.url'), '/').'/sitemap.xml" (from APP_URL).'),
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

        $content = trim(str_replace("\r\n", "\n", (string) ($data['robots_txt'] ?? '')));

        SeoSetting::getSettings();

        SeoSetting::query()->firstOrFail()->update([
            'robots_txt' => $content === '' || $content === trim(SeoSetting::DEFAULT_ROBOTS_TXT) ? null : $content,
            'robots_txt_append_sitemap' => (bool) ($data['robots_txt_append_sitemap'] ?? true),
        ]);

        SeoSetting::writeRobotsTxtFile();

        Notification::make()
            ->title('robots.txt saved')
            ->success()
            ->send();
    }
}
