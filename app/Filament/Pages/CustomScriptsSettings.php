<?php

namespace App\Filament\Pages;

use App\Models\SeoSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use UnitEnum;

class CustomScriptsSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-code-bracket';

    protected string $view = 'filament.pages.custom-scripts-settings';

    protected static ?string $navigationLabel = 'Custom Scripts';

    protected static ?string $title = 'Custom Scripts';

    protected static ?string $slug = 'custom-scripts';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 48;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $record = SeoSetting::query()->first();

        $this->form->fill([
            'custom_head_scripts' => $record?->custom_head_scripts ?? '',
            'custom_footer_scripts' => $record?->custom_footer_scripts ?? '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Custom Scripts')
                    ->description(
                        'Raw HTML injected on every public page, unescaped. Only paste code from trusted sources (pixels, chat widgets, verification tags).',
                    )
                    ->icon('heroicon-o-code-bracket')
                    ->schema([
                        CodeEditor::make('custom_head_scripts')
                            ->label('Header Scripts')
                            ->language(Language::Html)
                            ->helperText('Inserted just before </head>.'),

                        CodeEditor::make('custom_footer_scripts')
                            ->label('Footer Scripts')
                            ->language(Language::Html)
                            ->helperText('Inserted just before </body>.'),
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

        SeoSetting::getSettings();

        SeoSetting::query()->firstOrFail()->update([
            'custom_head_scripts' => filled($data['custom_head_scripts'] ?? null) ? $data['custom_head_scripts'] : null,
            'custom_footer_scripts' => filled($data['custom_footer_scripts'] ?? null) ? $data['custom_footer_scripts'] : null,
        ]);

        Notification::make()
            ->title('Custom scripts saved')
            ->success()
            ->send();
    }
}
