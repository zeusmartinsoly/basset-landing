<?php

use App\Models\LandingPageSection;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $section = LandingPageSection::query()->where('key', 'cambers_works')->first();

        if ($section !== null) {
            /** @var array<string, mixed> $content */
            $content = $section->content ?? [];

            $content['visible'] ??= true;
            $content['navbar_link_text'] ??= 'أعمال الكامبرز';

            $section->update(['content' => $content]);
        }

        $navbar = LandingPageSection::query()->where('key', 'navbar')->first();

        if ($navbar === null) {
            return;
        }

        /** @var array<string, mixed> $navContent */
        $navContent = $navbar->content ?? [];
        $links = $navContent['links'] ?? [];

        if (! is_array($links)) {
            return;
        }

        $navContent['links'] = array_values(array_filter($links, function (mixed $link): bool {
            if (! is_array($link)) {
                return true;
            }

            return ($link['url'] ?? '') !== '#campers-works';
        }));

        $navbar->update(['content' => $navContent]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $section = LandingPageSection::query()->where('key', 'cambers_works')->first();

        if ($section === null) {
            return;
        }

        /** @var array<string, mixed> $content */
        $content = $section->content ?? [];

        unset($content['visible'], $content['navbar_link_text']);

        $section->update(['content' => $content]);
    }
};
