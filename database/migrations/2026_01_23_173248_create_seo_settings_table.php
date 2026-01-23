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
        Schema::create('seo_settings', function (Blueprint $table) {
            $table->id();

            // Basic SEO
            $table->string('site_title')->default('Baseet - Bran—dat 103');
            $table->text('site_description')->nullable();
            $table->string('keywords')->nullable();
            $table->string('author')->nullable();

            // Open Graph
            $table->string('og_title')->nullable();
            $table->text('og_description')->nullable();
            $table->string('og_image')->nullable();

            // Twitter Card
            $table->string('twitter_handle')->nullable();
            $table->string('twitter_card_type')->default('summary_large_image');

            // Structured Data (JSON-LD)
            $table->string('organization_name')->nullable();
            $table->string('organization_logo')->nullable();
            $table->string('course_name')->nullable();
            $table->text('course_description')->nullable();

            // Advanced
            $table->string('canonical_url')->nullable();
            $table->string('google_site_verification')->nullable();
            $table->text('custom_head_scripts')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('seo_settings');
    }
};
