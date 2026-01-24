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
    ];

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

        // Convert image paths to full URLs using the public disk
        if (! empty($data['og_image'])) {
            $data['og_image'] = Storage::disk('public')->url($data['og_image']);
        }

        if (! empty($data['organization_logo'])) {
            $data['organization_logo'] = Storage::disk('public')->url($data['organization_logo']);
        }

        return $data;
    }
}
