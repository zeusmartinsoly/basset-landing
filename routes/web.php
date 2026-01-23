<?php

use App\Models\SeoSetting;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    // Dynamically get work images from the public/images/work directory
    $workImages = collect(File::files(public_path('images/work')))
        ->filter(fn ($file) => in_array($file->getExtension(), ['webp', 'jpg', 'jpeg', 'png']))
        ->map(fn ($file) => [
            'src' => '/images/work/'.$file->getFilename(),
            'alt' => pathinfo($file->getFilename(), PATHINFO_FILENAME),
        ])
        ->sortBy('src')
        ->values()
        ->all();

    return Inertia::render('welcome', [
        'workImages' => $workImages,
        'seo' => SeoSetting::getSettings(),
        'appUrl' => config('app.url'),
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
