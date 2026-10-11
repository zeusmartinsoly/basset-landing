<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->boolean('meta_pixel_enabled')->default(false)->after('google_tag_manager');
            $table->json('meta_pixel')->nullable()->after('meta_pixel_enabled');
            $table->boolean('tiktok_pixel_enabled')->default(false)->after('meta_pixel');
            $table->json('tiktok_pixel')->nullable()->after('tiktok_pixel_enabled');
            $table->boolean('cookie_consent_enabled')->default(false)->after('tiktok_pixel');
            $table->json('cookie_consent')->nullable()->after('cookie_consent_enabled');
            $table->text('robots_txt')->nullable()->after('cookie_consent');
            $table->boolean('robots_txt_append_sitemap')->default(true)->after('robots_txt');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->dropColumn([
                'meta_pixel_enabled',
                'meta_pixel',
                'tiktok_pixel_enabled',
                'tiktok_pixel',
                'cookie_consent_enabled',
                'cookie_consent',
                'robots_txt',
                'robots_txt_append_sitemap',
            ]);
        });
    }
};
