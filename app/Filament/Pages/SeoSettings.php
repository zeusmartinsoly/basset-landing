<?php

namespace App\Filament\Pages;

use App\Models\SeoSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;

class SeoSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-magnifying-glass';

    protected string $view = 'filament.pages.seo-settings';

    protected static ?string $navigationLabel = 'SEO Settings';

    protected static ?string $title = 'SEO Settings';

    protected static ?string $slug = 'seo-settings';

    protected static ?int $navigationSort = 100;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $settings = SeoSetting::first() ?? new SeoSetting;

        $this->form->fill($settings->toArray());
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make('Basic SEO')
                    ->description('Core meta tags for search engines')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextInput::make('site_title')
                            ->label('Site Title')
                            ->required()
                            ->maxLength(60)
                            ->hint('Recommended: 50-60 characters')
                            ->helperText('The main title that appears in search results'),

                        Textarea::make('site_description')
                            ->label('Meta Description')
                            ->rows(3)
                            ->maxLength(160)
                            ->hint('Recommended: 150-160 characters')
                            ->helperText('The description shown in search results'),

                        TextInput::make('keywords')
                            ->label('Meta Keywords')
                            ->helperText('Comma-separated keywords (less important for modern SEO)'),

                        TextInput::make('author')
                            ->label('Author')
                            ->helperText('The author of the website content'),
                    ])
                    ->columns(1),

                Section::make('Open Graph (Social Sharing)')
                    ->description('How your site appears when shared on Facebook, LinkedIn, etc.')
                    ->icon('heroicon-o-share')
                    ->schema([
                        TextInput::make('og_title')
                            ->label('OG Title')
                            ->maxLength(60)
                            ->helperText('Leave empty to use Site Title'),

                        Textarea::make('og_description')
                            ->label('OG Description')
                            ->rows(3)
                            ->maxLength(160)
                            ->helperText('Leave empty to use Meta Description'),

                        FileUpload::make('og_image')
                            ->label('OG Image')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1.91:1')
                            ->imageResizeTargetWidth('1200')
                            ->imageResizeTargetHeight('630')
                            ->directory('seo')
                            ->helperText('Recommended size: 1200x630 pixels'),
                    ])
                    ->columns(1),

                Section::make('Twitter Card')
                    ->description('How your site appears when shared on Twitter/X')
                    ->icon('heroicon-o-chat-bubble-left')
                    ->schema([
                        TextInput::make('twitter_handle')
                            ->label('Twitter Handle')
                            ->prefix('@')
                            ->helperText('Your Twitter username without @'),

                        Select::make('twitter_card_type')
                            ->label('Card Type')
                            ->options([
                                'summary' => 'Summary',
                                'summary_large_image' => 'Summary with Large Image',
                            ])
                            ->default('summary_large_image'),
                    ])
                    ->columns(2),

                Section::make('Structured Data (JSON-LD)')
                    ->description('Rich snippets for better search results')
                    ->icon('heroicon-o-code-bracket')
                    ->schema([
                        TextInput::make('organization_name')
                            ->label('Organization Name')
                            ->helperText('Your company/brand name'),

                        FileUpload::make('organization_logo')
                            ->label('Organization Logo')
                            ->image()
                            ->disk('public')
                            ->visibility('public')
                            ->directory('seo')
                            ->helperText('Logo for structured data (min 112x112 pixels)'),

                        TextInput::make('course_name')
                            ->label('Course/Camp Name')
                            ->helperText('Name of your course or camp'),

                        Textarea::make('course_description')
                            ->label('Course/Camp Description')
                            ->rows(3)
                            ->helperText('Description for course schema'),
                    ])
                    ->columns(1),

                Section::make('Advanced')
                    ->description('Additional SEO settings')
                    ->icon('heroicon-o-cog-6-tooth')
                    ->collapsed()
                    ->schema([
                        TextInput::make('canonical_url')
                            ->label('Canonical URL')
                            ->url()
                            ->helperText('Base URL for canonical tags (e.g., https://example.com)'),

                        TextInput::make('google_site_verification')
                            ->label('Google Site Verification')
                            ->helperText('Google Search Console verification code'),

                        Textarea::make('custom_head_scripts')
                            ->label('Custom Head Scripts')
                            ->rows(5)
                            ->helperText('Custom scripts to add to the head (e.g., Google Analytics)'),
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

        $settings = SeoSetting::first();

        if ($settings) {
            $settings->update($data);
        } else {
            SeoSetting::create($data);
        }

        Notification::make()
            ->title('SEO Settings saved successfully')
            ->success()
            ->send();
    }
}
