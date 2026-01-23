<?php

namespace App\Http\Controllers;

use App\Models\Page;
use Inertia\Inertia;
use Inertia\Response;

class PageController extends Controller
{
    public function show(string $slug): Response
    {
        $page = Page::query()
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        return Inertia::render('Page/Show', [
            'page' => [
                'title' => $page->title,
                'content' => $page->content,
                'meta_title' => $page->meta_title ?? $page->title,
                'meta_description' => $page->meta_description,
            ],
        ]);
    }
}
