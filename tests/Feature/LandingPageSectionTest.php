<?php

use App\Models\LandingPageSection;

beforeEach(function () {
    // Run seeders to ensure sections exist (testimonials live in their own seeder)
    $this->artisan('db:seed', ['--class' => 'LandingPageSectionSeeder']);
    $this->artisan('db:seed', ['--class' => 'TestimonialsSectionSeeder']);
});

describe('LandingPageSection Model', function () {
    it('can retrieve a section by key', function () {
        $section = LandingPageSection::getByKey('hero');

        expect($section)->not->toBeNull()
            ->and($section->key)->toBe('hero')
            ->and($section->name)->toBe('Hero Section');
    });

    it('can retrieve section content by key', function () {
        $content = LandingPageSection::getContent('navbar');

        expect($content)->toBeArray()
            ->and($content)->toHaveKey('logo')
            ->and($content)->toHaveKey('cta_text')
            ->and($content)->toHaveKey('links');
    });

    it('returns null for non-existent section key', function () {
        $section = LandingPageSection::getByKey('non-existent-key');

        expect($section)->toBeNull();
    });

    it('can retrieve all sections as keyed array', function () {
        $sections = LandingPageSection::getAllSections();

        expect($sections)->toBeArray()
            ->and($sections)->toHaveKey('navbar')
            ->and($sections)->toHaveKey('hero')
            ->and($sections)->toHaveKey('about')
            ->and($sections)->toHaveKey('cta')
            ->and($sections)->toHaveKey('gallery')
            ->and($sections)->toHaveKey('for_whom')
            ->and($sections)->toHaveKey('phases')
            ->and($sections)->toHaveKey('cambers_works')
            ->and($sections)->toHaveKey('founder')
            ->and($sections)->toHaveKey('intro')
            ->and($sections)->toHaveKey('testimonials')
            ->and($sections)->toHaveKey('work')
            ->and($sections)->toHaveKey('contact_waitlist')
            ->and($sections)->toHaveKey('footer');
    });
});

describe('Landing Page Route', function () {
    it('loads the home page successfully', function () {
        $response = $this->get('/');

        $response->assertSuccessful();
    });

    it('loads the home page when cambers_works row is missing from the database', function () {
        LandingPageSection::query()->where('key', 'cambers_works')->delete();

        $this->get('/')
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->has('sections.cambers_works')
                ->where('sections.cambers_works.visible', true)
                ->where('sections.cambers_works.heading', 'أعمال الكامبرز')
            );
    });

    it('loads the home page when testimonials row is missing from the database', function () {
        LandingPageSection::query()->where('key', 'testimonials')->delete();

        $this->get('/')
            ->assertSuccessful()
            ->assertInertia(fn ($page) => $page
                ->has('sections.testimonials')
                ->where('sections.testimonials.visible', true)
                ->where('sections.testimonials.heading', 'رأي الكامبرز')
                ->has('sections.testimonials.items')
            );
    });

    it('passes sections data to the view', function () {
        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->has('sections')
            ->has('sections.navbar')
            ->has('sections.hero')
            ->has('sections.about')
            ->has('sections.cta')
            ->has('sections.gallery')
            ->has('sections.for_whom')
            ->has('sections.phases')
            ->has('sections.cambers_works')
            ->has('sections.cambers_works.visible')
            ->has('sections.cambers_works.navbar_link_text')
            ->has('sections.cambers_works.heading')
            ->has('sections.cambers_works.row_1_images')
            ->has('sections.cambers_works.row_2_images')
            ->has('sections.founder')
            ->has('sections.intro')
            ->has('sections.testimonials')
            ->has('sections.testimonials.visible')
            ->has('sections.testimonials.navbar_link_text')
            ->has('sections.testimonials.heading')
            ->has('sections.testimonials.items')
            ->has('sections.work')
            ->has('sections.contact_waitlist.visible')
            ->has('sections.footer')
        );
    });

    it('passes work images to the view', function () {
        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->has('workImages')
        );
    });

    it('passes seo settings to the view', function () {
        $response = $this->get('/');

        $response->assertInertia(fn ($page) => $page
            ->has('seo')
            ->has('appUrl')
        );
    });

    it('exposes contact waitlist visibility as enabled by default', function () {
        $this->get('/')->assertInertia(fn ($page) => $page
            ->where('sections.contact_waitlist.visible', true)
            ->has('sections.contact_waitlist.heading')
            ->has('sections.contact_waitlist.description')
            ->has('sections.contact_waitlist.navbar_link_text')
            ->has('sections.contact_waitlist.submit_button_text')
        );
    });

    it('exposes contact waitlist as hidden when disabled in the database', function () {
        LandingPageSection::query()->where('key', 'contact_waitlist')->update([
            'content' => ['visible' => false],
        ]);

        $this->get('/')->assertInertia(fn ($page) => $page
            ->where('sections.contact_waitlist.visible', false)
        );
    });

    it('exposes campers works section as hidden when disabled in the database', function () {
        $section = LandingPageSection::getByKey('cambers_works');
        expect($section)->not->toBeNull();

        $content = $section->content;
        $content['visible'] = false;
        $section->update(['content' => $content]);

        $this->get('/')->assertInertia(fn ($page) => $page
            ->where('sections.cambers_works.visible', false)
        );
    });

    it('exposes testimonials section as hidden when disabled in the database', function () {
        $section = LandingPageSection::getByKey('testimonials');
        expect($section)->not->toBeNull();

        $content = $section->content;
        $content['visible'] = false;
        $section->update(['content' => $content]);

        $this->get('/')->assertInertia(fn ($page) => $page
            ->where('sections.testimonials.visible', false)
        );
    });
});

describe('LandingPageSection Content Structure', function () {
    it('hero section has correct structure', function () {
        $content = LandingPageSection::getContent('hero');

        expect($content)
            ->toHaveKey('arabic_quote')
            ->toHaveKey('english_text')
            ->toHaveKey('edition')
            ->toHaveKey('brand_name')
            ->toHaveKey('brand_number')
            ->toHaveKey('year')
            ->toHaveKey('images');

        expect($content['images'])
            ->toHaveKey('bran_svg')
            ->toHaveKey('dat_svg')
            ->toHaveKey('number_svg')
            ->toHaveKey('dice_left')
            ->toHaveKey('dice_right');
    });

    it('navbar section has correct structure', function () {
        $content = LandingPageSection::getContent('navbar');

        expect($content)
            ->toHaveKey('logo')
            ->toHaveKey('cta_text')
            ->toHaveKey('cta_url')
            ->toHaveKey('links');

        expect($content['links'])->toBeArray();
        expect($content['links'][0])
            ->toHaveKey('text')
            ->toHaveKey('url')
            ->toHaveKey('external');
    });

    it('stores campers navbar label on cambers_works instead of navbar links repeater', function () {
        $navbar = LandingPageSection::getContent('navbar');

        foreach ($navbar['links'] ?? [] as $link) {
            expect($link['url'] ?? '')->not->toBe('#campers-works');
        }

        $campers = LandingPageSection::getContent('cambers_works');

        expect($campers['navbar_link_text'] ?? '')->not->toBeEmpty();
    });

    it('stores testimonials navbar label on testimonials instead of navbar links repeater', function () {
        $navbar = LandingPageSection::getContent('navbar');

        foreach ($navbar['links'] ?? [] as $link) {
            expect($link['url'] ?? '')->not->toBe('#testimonials');
        }

        $testimonials = LandingPageSection::getContent('testimonials');

        expect($testimonials['navbar_link_text'] ?? '')->not->toBeEmpty();
    });

    it('phases section has correct structure', function () {
        $content = LandingPageSection::getContent('phases');

        expect($content)
            ->toHaveKey('phases')
            ->toHaveKey('image');

        expect($content['phases'])->toBeArray();
        expect(count($content['phases']))->toBe(5);
        expect($content['phases'][0])
            ->toHaveKey('number')
            ->toHaveKey('title');
    });

    it('cambers_works section has correct structure', function () {
        $content = LandingPageSection::getContent('cambers_works');

        expect($content)
            ->toHaveKey('visible')
            ->toHaveKey('navbar_link_text')
            ->toHaveKey('heading')
            ->toHaveKey('row_1_images')
            ->toHaveKey('row_2_images');

        expect($content['visible'])->toBeTrue();
        expect($content['navbar_link_text'])->toBe('أعمال الكامبرز');
        expect($content['heading'])->toBe('أعمال الكامبرز');
        expect($content['row_1_images'])->toBeArray();
        expect($content['row_2_images'])->toBeArray();
    });

    it('testimonials section has correct structure', function () {
        $content = LandingPageSection::getContent('testimonials');

        expect($content)
            ->toHaveKey('visible')
            ->toHaveKey('navbar_link_text')
            ->toHaveKey('heading')
            ->toHaveKey('items');

        expect($content['visible'])->toBeTrue();
        expect($content['heading'])->toBe('رأي الكامبرز');
        expect($content['items'])->toBeArray();
        expect(count($content['items']))->toBe(6);
        expect($content['items'][0])
            ->toHaveKey('image')
            ->toHaveKey('name')
            ->toHaveKey('course')
            ->toHaveKey('content');
    });

    it('footer section has correct structure', function () {
        $content = LandingPageSection::getContent('footer');

        expect($content)
            ->toHaveKey('social_links')
            ->toHaveKey('copyright_text')
            ->toHaveKey('developer_credit');

        expect($content['social_links'])->toBeArray();
        expect($content['social_links'][0])
            ->toHaveKey('name')
            ->toHaveKey('icon')
            ->toHaveKey('url');
    });

    it('contact_waitlist section has visibility flag', function () {
        $content = LandingPageSection::getContent('contact_waitlist');

        expect($content)->toHaveKeys([
            'visible',
            'heading',
            'description',
            'navbar_link_text',
            'submit_button_text',
        ]);
        expect($content['visible'])->toBeTrue()
            ->and($content['heading'])->not->toBeEmpty()
            ->and($content['description'])->not->toBeEmpty()
            ->and($content['navbar_link_text'])->not->toBeEmpty()
            ->and($content['submit_button_text'])->not->toBeEmpty();
    });
});

describe('LandingPageSection CRUD', function () {
    it('can update section content', function () {
        $section = LandingPageSection::getByKey('cta');
        $originalContent = $section->content;

        $newContent = array_merge($originalContent, [
            'button_text' => 'سجل الآن',
        ]);

        $section->update(['content' => $newContent]);

        $updatedSection = LandingPageSection::getByKey('cta');
        expect($updatedSection->content['button_text'])->toBe('سجل الآن');
    });

    it('can create a new section using factory', function () {
        $section = LandingPageSection::factory()->create([
            'key' => 'test-section',
            'name' => 'Test Section',
            'content' => ['test' => 'value'],
        ]);

        expect($section->key)->toBe('test-section')
            ->and($section->name)->toBe('Test Section')
            ->and($section->content)->toBe(['test' => 'value']);
    });
});
