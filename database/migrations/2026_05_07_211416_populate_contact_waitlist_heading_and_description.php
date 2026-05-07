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

        $content['heading'] ??= 'تواصل معنا';
        $content['description'] ??= 'سجل الآن في قائمة الانتظار .. وهيتم التواصل معاك بمجرد فتح الباب للتسجيل في النسخة الجديدة';

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

        unset($content['heading'], $content['description']);

        $section->update(['content' => $content]);
    }
};
