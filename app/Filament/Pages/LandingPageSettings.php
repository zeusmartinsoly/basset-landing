<?php

namespace App\Filament\Pages;

use App\Models\LandingPageSection;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;

class LandingPageSettings extends Page implements HasSchemas
{
    use InteractsWithSchemas;

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected string $view = 'filament.pages.landing-page-settings';

    protected static ?string $navigationLabel = 'Landing Page';

    protected static ?string $title = 'Landing Page Settings';

    protected static ?string $slug = 'landing-page-settings';

    protected static ?int $navigationSort = 99;

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(): void
    {
        $sections = LandingPageSection::all()->keyBy('key');

        $formData = [];
        foreach ($sections as $key => $section) {
            $formData[$key] = $section->content;
        }

        $this->form->fill($formData);
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Sections')
                    ->tabs([
                        $this->getNavbarTab(),
                        $this->getHeroTab(),
                        $this->getAboutTab(),
                        $this->getCtaTab(),
                        $this->getGalleryTab(),
                        $this->getForWhomTab(),
                        $this->getPhasesTab(),
                        $this->getFounderTab(),
                        $this->getIntroTab(),
                        $this->getWorkTab(),
                        $this->getFooterTab(),
                    ])
                    ->persistTabInQueryString(),
            ])
            ->statePath('data');
    }

    private function getNavbarTab(): Tab
    {
        return Tab::make('Navbar')
            ->icon('heroicon-o-bars-3')
            ->schema([
                Section::make('Navigation Bar')
                    ->description('Logo and navigation links')
                    ->schema([
                        FileUpload::make('navbar.logo')
                            ->label('Logo')
                            ->disk('landing')
                            ->directory('brand')
                            ->visibility('public')
                            ->image()
                            ->imagePreviewHeight('100'),

                        TextInput::make('navbar.cta_text')
                            ->label('CTA Button Text')
                            ->required(),

                        TextInput::make('navbar.cta_url')
                            ->label('CTA Button URL')
                            ->required(),

                        Repeater::make('navbar.links')
                            ->label('Navigation Links')
                            ->schema([
                                TextInput::make('text')
                                    ->label('Link Text')
                                    ->required(),
                                TextInput::make('url')
                                    ->label('URL')
                                    ->required(),
                                Toggle::make('external')
                                    ->label('External Link')
                                    ->default(false),
                            ])
                            ->columns(3)
                            ->defaultItems(3)
                            ->reorderable()
                            ->collapsible(),
                    ]),
            ]);
    }

    private function getHeroTab(): Tab
    {
        return Tab::make('Hero')
            ->icon('heroicon-o-home')
            ->schema([
                Section::make('Hero Section')
                    ->description('Main landing section with branding')
                    ->schema([
                        Textarea::make('hero.arabic_quote')
                            ->label('Arabic Quote')
                            ->rows(2)
                            ->required(),

                        Textarea::make('hero.english_text')
                            ->label('English Text')
                            ->rows(2)
                            ->required(),

                        TextInput::make('hero.edition')
                            ->label('Edition Text')
                            ->required(),

                        TextInput::make('hero.brand_name')
                            ->label('Brand Name (Arabic)')
                            ->required(),

                        TextInput::make('hero.brand_number')
                            ->label('Brand Number')
                            ->required(),

                        TextInput::make('hero.year')
                            ->label('Year')
                            ->required(),

                        Section::make('Hero Images')
                            ->description('SVG and WebP images for the hero section')
                            ->schema([
                                FileUpload::make('hero.images.bran_svg')
                                    ->label('Bran SVG')
                                    ->disk('landing')
                                    ->directory('hero')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/svg+xml'])
                                    ->imagePreviewHeight('100'),

                                FileUpload::make('hero.images.dat_svg')
                                    ->label('Dat SVG')
                                    ->disk('landing')
                                    ->directory('hero')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/svg+xml'])
                                    ->imagePreviewHeight('100'),

                                FileUpload::make('hero.images.number_svg')
                                    ->label('Number 103 SVG')
                                    ->disk('landing')
                                    ->directory('hero')
                                    ->visibility('public')
                                    ->acceptedFileTypes(['image/svg+xml'])
                                    ->imagePreviewHeight('100'),

                                FileUpload::make('hero.images.dice_left')
                                    ->label('Dice Left Image')
                                    ->disk('landing')
                                    ->directory('hero')
                                    ->visibility('public')
                                    ->image()
                                    ->imagePreviewHeight('100'),

                                FileUpload::make('hero.images.dice_right')
                                    ->label('Dice Right Image')
                                    ->disk('landing')
                                    ->directory('hero')
                                    ->visibility('public')
                                    ->image()
                                    ->imagePreviewHeight('100'),
                            ])
                            ->columns(2)
                            ->collapsed(),
                    ]),
            ]);
    }

    private function getAboutTab(): Tab
    {
        return Tab::make('About')
            ->icon('heroicon-o-information-circle')
            ->schema([
                Section::make('About Section')
                    ->description('Camp introduction and description')
                    ->schema([
                        Textarea::make('about.title')
                            ->label('Title')
                            ->rows(2)
                            ->required(),

                        Textarea::make('about.description_1')
                            ->label('Description 1')
                            ->rows(4)
                            ->required(),

                        Textarea::make('about.description_2')
                            ->label('Description 2')
                            ->rows(4)
                            ->required(),

                        Section::make('About Images')
                            ->schema([
                                FileUpload::make('about.images.image_1')
                                    ->label('Image 1')
                                    ->disk('landing')
                                    ->directory('hero')
                                    ->visibility('public')
                                    ->image()
                                    ->imagePreviewHeight('100'),

                                FileUpload::make('about.images.image_2')
                                    ->label('Image 2')
                                    ->disk('landing')
                                    ->directory('hero')
                                    ->visibility('public')
                                    ->image()
                                    ->imagePreviewHeight('100'),
                            ])
                            ->columns(2)
                            ->collapsed(),
                    ]),
            ]);
    }

    private function getCtaTab(): Tab
    {
        return Tab::make('CTA')
            ->icon('heroicon-o-cursor-arrow-rays')
            ->schema([
                Section::make('Call to Action')
                    ->description('Main CTA section')
                    ->schema([
                        TextInput::make('cta.button_text')
                            ->label('Button Text')
                            ->required(),

                        TextInput::make('cta.button_url')
                            ->label('Button URL')
                            ->required(),

                        Textarea::make('cta.tagline')
                            ->label('Tagline')
                            ->rows(2)
                            ->required(),
                    ]),
            ]);
    }

    private function getGalleryTab(): Tab
    {
        return Tab::make('Gallery')
            ->icon('heroicon-o-photo')
            ->schema([
                Section::make('Gallery Section')
                    ->description('Scrolling gallery images')
                    ->schema([
                        FileUpload::make('gallery.row_1_images')
                            ->label('Row 1 Images')
                            ->disk('landing')
                            ->directory('gallery')
                            ->visibility('public')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->imagePreviewHeight('100'),

                        FileUpload::make('gallery.row_2_images')
                            ->label('Row 2 Images')
                            ->disk('landing')
                            ->directory('gallery')
                            ->visibility('public')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->imagePreviewHeight('100'),
                    ]),
            ]);
    }

    private function getForWhomTab(): Tab
    {
        return Tab::make('For Whom')
            ->icon('heroicon-o-user-group')
            ->schema([
                Section::make('For Whom Section')
                    ->description('Target audience section')
                    ->schema([
                        TextInput::make('for_whom.title')
                            ->label('Title (Arabic)')
                            ->required(),

                        TextInput::make('for_whom.subtitle')
                            ->label('Subtitle (English)')
                            ->required(),

                        Repeater::make('for_whom.items')
                            ->label('Target Audience Items')
                            ->schema([
                                TextInput::make('text')
                                    ->label('Bold Text')
                                    ->required(),
                                Textarea::make('description')
                                    ->label('Description')
                                    ->rows(2)
                                    ->required(),
                                Toggle::make('highlight')
                                    ->label('Has Asterisk'),
                                Select::make('color')
                                    ->label('Text Color')
                                    ->options([
                                        'white' => 'White',
                                        'red' => 'Red',
                                    ])
                                    ->default('white'),
                            ])
                            ->columns(2)
                            ->defaultItems(6)
                            ->reorderable()
                            ->collapsible(),
                    ]),
            ]);
    }

    private function getPhasesTab(): Tab
    {
        return Tab::make('Phases')
            ->icon('heroicon-o-arrow-path')
            ->schema([
                Section::make('Phases Section')
                    ->description('Camp phases overview')
                    ->schema([
                        Repeater::make('phases.phases')
                            ->label('Phases')
                            ->schema([
                                TextInput::make('number')
                                    ->label('Phase Number')
                                    ->required(),
                                TextInput::make('title')
                                    ->label('Phase Title')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(5)
                            ->reorderable(),

                        FileUpload::make('phases.image')
                            ->label('Section Image')
                            ->disk('landing')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/svg+xml', 'image/png', 'image/webp'])
                            ->imagePreviewHeight('100'),
                    ]),
            ]);
    }

    private function getFounderTab(): Tab
    {
        return Tab::make('Founder')
            ->icon('heroicon-o-user')
            ->schema([
                Section::make('Founder Section')
                    ->description('Founder showcase')
                    ->schema([
                        TextInput::make('founder.founder_name')
                            ->label('Founder Name')
                            ->required(),

                        TextInput::make('founder.founder_title')
                            ->label('Founder Title')
                            ->required(),

                        FileUpload::make('founder.founder_image')
                            ->label('Founder Image')
                            ->disk('landing')
                            ->directory('assets')
                            ->visibility('public')
                            ->image()
                            ->imagePreviewHeight('100'),

                        FileUpload::make('founder.background_image')
                            ->label('Background Image')
                            ->disk('landing')
                            ->visibility('public')
                            ->acceptedFileTypes(['image/svg+xml', 'image/png', 'image/webp'])
                            ->imagePreviewHeight('100'),
                    ]),
            ]);
    }

    private function getIntroTab(): Tab
    {
        return Tab::make('Intro')
            ->icon('heroicon-o-sparkles')
            ->schema([
                Section::make('Introduction Section')
                    ->description('Personal introduction with achievements')
                    ->schema([
                        TextInput::make('intro.title')
                            ->label('Title')
                            ->required(),

                        Repeater::make('intro.paragraphs')
                            ->label('Paragraphs')
                            ->simple(
                                Textarea::make('text')
                                    ->rows(3)
                                    ->required()
                            )
                            ->defaultItems(3),

                        TextInput::make('intro.years_design')
                            ->label('Years in Design')
                            ->numeric()
                            ->required(),

                        TextInput::make('intro.years_branding')
                            ->label('Years in Branding')
                            ->numeric()
                            ->required(),

                        FileUpload::make('intro.award_image')
                            ->label('Award Image')
                            ->disk('landing')
                            ->directory('assets')
                            ->visibility('public')
                            ->image()
                            ->imagePreviewHeight('100'),

                        Textarea::make('intro.cta_title')
                            ->label('CTA Title')
                            ->rows(2)
                            ->required(),

                        TextInput::make('intro.cta_button_text')
                            ->label('CTA Button Text')
                            ->required(),

                        TextInput::make('intro.cta_button_url')
                            ->label('CTA Button URL')
                            ->required(),
                    ]),
            ]);
    }

    private function getWorkTab(): Tab
    {
        return Tab::make('Work')
            ->icon('heroicon-o-briefcase')
            ->schema([
                Section::make('Work Showcase Section')
                    ->description('Portfolio/work display section')
                    ->schema([
                        TextInput::make('work.title')
                            ->label('Title')
                            ->required(),

                        TextInput::make('work.subtitle')
                            ->label('Subtitle')
                            ->required(),

                        TextInput::make('work.instructions')
                            ->label('Instructions Text')
                            ->required(),

                        FileUpload::make('work.images')
                            ->label('Work Images')
                            ->disk('landing')
                            ->directory('work')
                            ->visibility('public')
                            ->image()
                            ->multiple()
                            ->reorderable()
                            ->imagePreviewHeight('100')
                            ->helperText('Upload portfolio/work images. You can drag to reorder them.'),
                    ]),
            ]);
    }

    private function getFooterTab(): Tab
    {
        return Tab::make('Footer')
            ->icon('heroicon-o-link')
            ->schema([
                Section::make('Footer Section')
                    ->description('Footer links, social links and copyright')
                    ->schema([
                        Repeater::make('footer.footer_links')
                            ->label('Footer Links')
                            ->schema([
                                TextInput::make('text')
                                    ->label('Link Text')
                                    ->required(),
                                TextInput::make('url')
                                    ->label('URL')
                                    ->required(),
                            ])
                            ->columns(2)
                            ->defaultItems(2)
                            ->reorderable()
                            ->collapsible(),

                        Repeater::make('footer.social_links')
                            ->label('Social Links')
                            ->schema([
                                TextInput::make('name')
                                    ->label('Platform Name')
                                    ->required(),
                                FileUpload::make('icon')
                                    ->label('Icon')
                                    ->disk('landing')
                                    ->directory('social')
                                    ->visibility('public')
                                    ->image()
                                    ->imagePreviewHeight('50'),
                                TextInput::make('url')
                                    ->label('URL')
                                    ->url()
                                    ->required(),
                            ])
                            ->columns(3)
                            ->defaultItems(4)
                            ->reorderable()
                            ->collapsible(),

                        TextInput::make('footer.copyright_text')
                            ->label('Copyright Text')
                            ->required(),
                    ]),
            ]);
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Save All Settings')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        $data = $this->form->getState();

        $sectionKeys = [
            'navbar',
            'hero',
            'about',
            'cta',
            'gallery',
            'for_whom',
            'phases',
            'founder',
            'intro',
            'work',
            'footer',
        ];

        foreach ($sectionKeys as $key) {
            if (isset($data[$key])) {
                LandingPageSection::where('key', $key)->update([
                    'content' => $data[$key],
                ]);
            }
        }

        Notification::make()
            ->title('Landing page settings saved successfully')
            ->success()
            ->send();
    }
}
