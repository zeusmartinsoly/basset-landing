<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SeoSetting extends Model
{
    protected $fillable = [
        'site_title',
        'site_description',
        'keywords',
        'author',
        'og_title',
        'og_description',
        'og_image',
        'twitter_handle',
        'twitter_card_type',
        'organization_name',
        'organization_logo',
        'course_name',
        'course_description',
        'canonical_url',
        'google_site_verification',
        'custom_head_scripts',
        'google_analytics_enabled',
        'google_analytics_measurement_id',
        'microsoft_clarity_enabled',
        'microsoft_clarity_project_id',
    ];

    protected function casts(): array
    {
        return [
            'google_analytics_enabled' => 'boolean',
            'microsoft_clarity_enabled' => 'boolean',
        ];
    }

    /**
     * Get the SEO settings, creating default if none exist.
     *
     * @return array<string, mixed>
     */
    public static function getSettings(): array
    {
        $settings = self::first();

        if (! $settings) {
            $settings = self::create([
                'site_title' => 'Baseet - Bran—dat 103',
                'site_description' => 'كامب مكثف متخصص في صناعة الهوية البصرية والعلامات التجارية',
                'organization_name' => 'Baseet',
                'course_name' => 'Bran—dat 103',
            ]);
        }

        $data = $settings->toArray();

        // Convert image paths to full URLs using the seo disk (no symlink needed)
        if (! empty($data['og_image'])) {
            $data['og_image'] = Storage::disk('seo')->url($data['og_image']);
        }

        if (! empty($data['organization_logo'])) {
            $data['organization_logo'] = Storage::disk('seo')->url($data['organization_logo']);
        }

        return $data;
    }
}
