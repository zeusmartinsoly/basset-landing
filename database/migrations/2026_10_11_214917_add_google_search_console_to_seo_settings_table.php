<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->boolean('google_search_console_enabled')->default(false)->after('google_site_verification');
            $table->string('google_search_console_html_file')->nullable()->after('google_search_console_enabled');
        });

        DB::table('seo_settings')
            ->whereNotNull('google_site_verification')
            ->where('google_site_verification', '!=', '')
            ->update(['google_search_console_enabled' => true]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->dropColumn([
                'google_search_console_enabled',
                'google_search_console_html_file',
            ]);
        });
    }
};
