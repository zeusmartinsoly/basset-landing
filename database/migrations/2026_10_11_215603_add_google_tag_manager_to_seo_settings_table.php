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
            $table->boolean('google_tag_manager_enabled')->default(false)->after('google_analytics_measurement_id');
            $table->json('google_tag_manager')->nullable()->after('google_tag_manager_enabled');
        });

        DB::table('seo_settings')
            ->where('google_analytics_measurement_id', 'like', 'GTM-%')
            ->get(['id', 'google_analytics_enabled', 'google_analytics_measurement_id'])
            ->each(function (object $row): void {
                DB::table('seo_settings')->where('id', $row->id)->update([
                    'google_tag_manager_enabled' => (bool) $row->google_analytics_enabled,
                    'google_tag_manager' => json_encode([
                        'containers' => [['container_id' => $row->google_analytics_measurement_id]],
                        'data_layer_name' => 'dataLayer',
                        'lead_event_enabled' => true,
                        'lead_event_name' => 'generate_lead',
                    ]),
                    'google_analytics_enabled' => false,
                    'google_analytics_measurement_id' => null,
                ]);
            });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('seo_settings', function (Blueprint $table) {
            $table->dropColumn([
                'google_tag_manager_enabled',
                'google_tag_manager',
            ]);
        });
    }
};
