<?php

use App\Http\Controllers\PageController;
use App\Http\Controllers\StoreContactSubmissionController;
use App\Models\LandingPageSection;
use App\Models\SeoSetting;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

Route::get('/', function () {
    // Get all landing page sections content
    $sections = LandingPageSection::getAllSections();

    $sections['cambers_works'] = array_merge(
        [
            'heading' => 'أعمال الكامبرز',
            'visible' => true,
            'navbar_link_text' => '',
            'row_1_images' => [],
            'row_2_images' => [],
        ],
        $sections['cambers_works'] ?? [],
    );

    $sections['testimonials'] = array_merge(
        [
            'heading' => 'رأي الكامبرز',
            'visible' => true,
            'navbar_link_text' => '',
            'items' => [],
        ],
        $sections['testimonials'] ?? [],
    );

    // Get work images from the database (work section)
    $workImages = collect($sections['work']['images'] ?? [])
        ->map(fn (string $image) => [
            'src' => Storage::disk('landing')->url($image),
            'alt' => pathinfo($image, PATHINFO_FILENAME),
        ])
        ->values()
        ->all();

    return Inertia::render('welcome', [
        'workImages' => $workImages,
        'seo' => SeoSetting::getSettings(),
        'appUrl' => config('app.url'),
        'sections' => $sections,
    ]);
})->name('home');

Route::post('/contact', StoreContactSubmissionController::class)
    ->middleware(['throttle:10,1'])
    ->name('contact.store');

// Sitemap for SEO
Route::get('/sitemap.xml', function () {
    return response()
        ->view('sitemap', [
            'lastmod' => now()->toIso8601String(),
        ])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

Route::get('/robots.txt', fn () => response(SeoSetting::robotsTxtContent())
    ->header('Content-Type', 'text/plain; charset=UTF-8'))
    ->name('robots');

// Google Search Console HTML file verification
Route::get('/{file}', function (string $file) {
    $settings = SeoSetting::query()->first();

    abort_unless(
        $settings?->google_search_console_enabled && $settings->google_search_console_html_file === $file,
        404,
    );

    return response("google-site-verification: {$file}")
        ->header('Content-Type', 'text/html; charset=UTF-8');
})->where('file', 'google[a-z0-9]+\.html')->name('google-search-console.verification-file');

// Static pages (privacy, terms, etc.)
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');
