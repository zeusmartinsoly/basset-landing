<?php

use App\Models\LandingPageSection;

beforeEach(function () {
    // Run seeder to ensure sections exist
    $this->artisan('db:seed', ['--class' => 'LandingPageSectionSeeder']);
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
            ->and($sections)->toHaveKey('founder')
            ->and($sections)->toHaveKey('intro')
            ->and($sections)->toHaveKey('work')
            ->and($sections)->toHaveKey('footer');
    });
});

describe('Landing Page Route', function () {
    it('loads the home page successfully', function () {
        $response = $this->get('/');

        $response->assertSuccessful();
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
            ->has('sections.founder')
            ->has('sections.intro')
            ->has('sections.work')
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
