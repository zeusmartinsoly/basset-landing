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

class MicrosoftClaritySettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-arrow-trending-up';

    protected string $view = 'filament.pages.microsoft-clarity-settings';

    protected static ?string $navigationLabel = 'Microsoft Clarity';

    protected static ?string $title = 'Microsoft Clarity';

    protected static ?string $slug = 'microsoft-clarity';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 46;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $record = SeoSetting::query()->first();

        $this->form->fill([
            'microsoft_clarity_enabled' => $record !== null ? $record->microsoft_clarity_enabled : false,
            'microsoft_clarity_project_id' => $record?->microsoft_clarity_project_id ?? '',
        ]);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Microsoft Clarity')
                    ->description(
                        'Loads the official Clarity tag on every public page (after Google Analytics/GTM when those are enabled). Only enable where session recordings are appropriate. Project ID comes from clarity.microsoft.com → Setup → Install.',
                    )
                    ->icon('heroicon-o-arrow-trending-up')
                    ->schema([
                        Toggle::make('microsoft_clarity_enabled')
                            ->label('Enable Microsoft Clarity')
                            ->default(false)
                            ->live(),

                        TextInput::make('microsoft_clarity_project_id')
                            ->label('Project ID')
                            ->placeholder('e.g. wq4hora3m9')
                            ->helperText('Lowercase letters and digits only (8–32 characters), as shown in your Clarity install snippet.'),
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

        $enabled = (bool) ($data['microsoft_clarity_enabled'] ?? false);
        $projectId = isset($data['microsoft_clarity_project_id'])
            ? trim((string) $data['microsoft_clarity_project_id'])
            : '';

        $rules = [
            'enabled' => ['boolean'],
        ];

        if ($enabled) {
            $rules['microsoft_clarity_project_id'] = [
                'required',
                'string',
                'regex:/^[a-z0-9]{8,32}$/',
            ];
        } else {
            $rules['microsoft_clarity_project_id'] = ['nullable', 'string'];
        }

        Validator::validate(
            [
                'enabled' => $enabled,
                'microsoft_clarity_project_id' => $projectId,
            ],
            $rules,
            [],
            [
                'microsoft_clarity_project_id' => 'Project ID',
            ]
        );

        SeoSetting::getSettings();

        /** @var SeoSetting $settings */
        $settings = SeoSetting::query()->firstOrFail();

        $settings->update([
            'microsoft_clarity_enabled' => $enabled,
            'microsoft_clarity_project_id' => $enabled ? $projectId : null,
        ]);

        Notification::make()
            ->title('Microsoft Clarity settings saved')
            ->success()
            ->send();
    }
}
