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
        $section = LandingPageSection::query()->where('key', 'contact_waitlist')->first();

        if ($section === null) {
            return;
        }

        /** @var array<string, mixed> $content */
        $content = $section->content ?? [];

        $content['navbar_link_text'] ??= 'تواصل معنا';
        $content['submit_button_text'] ??= 'إرسال الطلب';

        $section->update(['content' => $content]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $section = LandingPageSection::query()->where('key', 'contact_waitlist')->first();

        if ($section === null) {
            return;
        }

        /** @var array<string, mixed> $content */
        $content = $section->content ?? [];

        unset($content['navbar_link_text'], $content['submit_button_text']);

        $section->update(['content' => $content]);
    }
};
