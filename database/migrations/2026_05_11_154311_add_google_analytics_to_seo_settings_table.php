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
            $table->boolean('google_analytics_enabled')->default(false)->after('custom_head_scripts');
            $table->string('google_analytics_measurement_id')->nullable()->after('google_analytics_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->dropColumn([
                'google_analytics_enabled',
                'google_analytics_measurement_id',
            ]);
        });
    }
};
