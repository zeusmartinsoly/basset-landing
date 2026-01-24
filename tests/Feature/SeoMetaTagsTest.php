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

it('renders Open Graph meta tags server-side in HTML', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    // Check Open Graph tags are in the raw HTML
    expect($content)->toContain('property="og:title"');
    expect($content)->toContain('property="og:description"');
    expect($content)->toContain('property="og:type"');
    expect($content)->toContain('property="og:url"');
    expect($content)->toContain('property="og:locale"');
});

it('renders Twitter Card meta tags server-side in HTML', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    // Check Twitter Card tags are in the raw HTML
    expect($content)->toContain('name="twitter:card"');
    expect($content)->toContain('name="twitter:title"');
    expect($content)->toContain('name="twitter:description"');
});

it('renders basic SEO meta tags server-side in HTML', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    // Check basic meta tags are in the raw HTML
    expect($content)->toContain('name="description"');
    expect($content)->toContain('name="keywords"');
    expect($content)->toContain('name="author"');
    expect($content)->toContain('name="robots"');
    expect($content)->toContain('rel="canonical"');
});

it('renders correct OG title content from database', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    expect($content)->toContain('content="Test OG Title"');
});

it('renders correct OG description content from database', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    expect($content)->toContain('content="Test OG Description"');
});

it('renders correct site description from database', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    expect($content)->toContain('content="Test site description for SEO"');
});

it('renders Google site verification when set', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    expect($content)->toContain('name="google-site-verification"');
    expect($content)->toContain('content="test-verification-code"');
});

it('renders Twitter handle correctly', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    expect($content)->toContain('name="twitter:site"');
    expect($content)->toContain('@testhandle');
});

it('falls back to site title when OG title is empty', function () {
    SeoSetting::query()->delete();
    SeoSetting::create([
        'site_title' => 'Fallback Site Title',
        'site_description' => 'Fallback description',
        'og_title' => null,
        'og_description' => null,
    ]);

    $response = $this->get('/');
    $content = $response->getContent();

    // OG title should fall back to site title
    expect($content)->toContain('content="Fallback Site Title"');
});
