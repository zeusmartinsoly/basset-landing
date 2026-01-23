<?php

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Laravel\Fortify\Features;

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
        'canRegister' => Features::enabled(Features::registration()),
        'workImages' => $workImages,
    ]);
})->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('dashboard', function () {
        return Inertia::render('dashboard');
    })->name('dashboard');
});

require __DIR__.'/settings.php';
