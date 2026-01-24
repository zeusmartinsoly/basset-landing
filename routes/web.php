<?php

use App\Http\Controllers\PageController;
use App\Models\LandingPageSection;
use App\Models\SeoSetting;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;

Route::get('/', function () {
    // Get all landing page sections content
    $sections = LandingPageSection::getAllSections();

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

// Sitemap for SEO
Route::get('/sitemap.xml', function () {
    return response()
        ->view('sitemap', [
            'lastmod' => now()->toIso8601String(),
        ])
        ->header('Content-Type', 'application/xml');
})->name('sitemap');

// Static pages (privacy, terms, etc.)
Route::get('/page/{slug}', [PageController::class, 'show'])->name('page.show');
