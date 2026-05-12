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
            $table->boolean('microsoft_clarity_enabled')->default(false)->after('google_analytics_measurement_id');
            $table->string('microsoft_clarity_project_id')->nullable()->after('microsoft_clarity_enabled');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->dropColumn([
                'microsoft_clarity_enabled',
                'microsoft_clarity_project_id',
            ]);
        });
    }
};
