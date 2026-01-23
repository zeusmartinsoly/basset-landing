<?php

use App\Models\SeoSetting;

beforeEach(function () {
    // Create SEO settings for tests
    SeoSetting::create([
        'site_title' => 'Test Site Title',
        'site_description' => 'Test site description for SEO',
        'keywords' => 'test, seo, keywords',
        'author' => 'Test Author',
        'og_title' => 'Test OG Title',
        'og_description' => 'Test OG Description',
        'twitter_handle' => 'testhandle',
        'twitter_card_type' => 'summary_large_image',
        'organization_name' => 'Test Organization',
        'course_name' => 'Test Course',
        'course_description' => 'Test course description',
        'canonical_url' => 'https://example.com',
        'google_site_verification' => 'test-verification-code',
    ]);
});

it('loads the home page successfully', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});

it('passes SEO data to the home page', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->component('welcome')
        ->has('seo')
        ->where('seo.site_title', 'Test Site Title')
        ->where('seo.site_description', 'Test site description for SEO')
        ->where('seo.organization_name', 'Test Organization')
        ->where('seo.course_name', 'Test Course')
    );
});

it('passes appUrl to the home page', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
    $response->assertInertia(fn ($page) => $page
        ->has('appUrl')
    );
});

it('creates default SEO settings if none exist', function () {
    // Delete all settings
    SeoSetting::query()->delete();

    // Visit home page - should create default settings
    $response = $this->get('/');
    $response->assertStatus(200);

    // Verify default settings were created
    $settings = SeoSetting::first();
    expect($settings)->not->toBeNull();
    expect($settings->site_title)->toBe('Baseet - Bran—dat 103');
});

it('sitemap returns valid XML', function () {
    $response = $this->get('/sitemap.xml');

    $response->assertStatus(200);
    $response->assertHeader('Content-Type', 'application/xml');

    $content = $response->getContent();

    // Check XML structure
    expect($content)->toContain('<?xml version="1.0" encoding="UTF-8"?>');
    expect($content)->toContain('<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">');
    expect($content)->toContain('<url>');
    expect($content)->toContain('<loc>');
    expect($content)->toContain('<lastmod>');
    expect($content)->toContain('<changefreq>weekly</changefreq>');
    expect($content)->toContain('<priority>1.0</priority>');
});

it('robots.txt file exists in public directory', function () {
    $robotsPath = public_path('robots.txt');

    expect(file_exists($robotsPath))->toBeTrue();

    $content = file_get_contents($robotsPath);
    expect($content)->toContain('User-agent: *');
    expect($content)->toContain('Sitemap:');
    expect($content)->toContain('Disallow: /admin');
});

it('SEO settings model returns correct data structure', function () {
    $settings = SeoSetting::getSettings();

    expect($settings)->toBeArray();
    expect($settings)->toHaveKey('site_title');
    expect($settings)->toHaveKey('site_description');
    expect($settings)->toHaveKey('og_title');
    expect($settings)->toHaveKey('og_description');
    expect($settings)->toHaveKey('twitter_handle');
    expect($settings)->toHaveKey('organization_name');
    expect($settings)->toHaveKey('course_name');
    expect($settings)->toHaveKey('course_description');
});

it('SEO settings can be updated', function () {
    $settings = SeoSetting::first();

    $settings->update([
        'site_title' => 'Updated Title',
        'site_description' => 'Updated Description',
    ]);

    $updated = SeoSetting::first();
    expect($updated->site_title)->toBe('Updated Title');
    expect($updated->site_description)->toBe('Updated Description');
});
