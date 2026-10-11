<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SeoSetting extends Model
{
    /**
     * Consent Mode v2 types sent with the default consent command.
     *
     * @var list<string>
     */
    public const GOOGLE_CONSENT_TYPES = [
        'ad_storage',
        'ad_user_data',
        'ad_personalization',
        'analytics_storage',
        'functionality_storage',
        'personalization_storage',
        'security_storage',
    ];

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
        'google_search_console_enabled',
        'google_search_console_html_file',
        'custom_head_scripts',
        'custom_footer_scripts',
        'google_analytics_enabled',
        'google_analytics_measurement_id',
        'google_tag_manager_enabled',
        'google_tag_manager',
        'microsoft_clarity_enabled',
        'microsoft_clarity_project_id',
    ];

    protected function casts(): array
    {
        return [
            'google_analytics_enabled' => 'boolean',
            'google_tag_manager_enabled' => 'boolean',
            'google_tag_manager' => 'array',
            'microsoft_clarity_enabled' => 'boolean',
            'google_search_console_enabled' => 'boolean',
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

    /**
     * Build a sanitized Google Tag Manager config for rendering, or null when inactive.
     *
     * @param  array<string, mixed>  $seo
     * @return array{
     *     containers: list<array{id: string, query: string}>,
     *     data_layer_name: string,
     *     base_url: string,
     *     consent: array<string, string|int>|null,
     *     ads_data_redaction: bool,
     *     url_passthrough: bool,
     *     data_layer_variables: array<string, string>,
     * }|null
     */
    public static function googleTagManagerConfig(array $seo): ?array
    {
        $settings = $seo['google_tag_manager'] ?? null;

        if (! ($seo['google_tag_manager_enabled'] ?? false) || ! is_array($settings)) {
            return null;
        }

        $containers = collect($settings['containers'] ?? [])
            ->filter(fn (mixed $container): bool => is_array($container)
                && preg_match('/^GTM-[A-Z0-9]{4,12}$/', (string) ($container['container_id'] ?? '')) === 1)
            ->map(function (array $container): array {
                $auth = (string) ($container['environment_auth'] ?? '');
                $preview = (string) ($container['environment_preview'] ?? '');
                $hasEnvironment = preg_match('/^[A-Za-z0-9_-]+$/', $auth) === 1 && preg_match('/^env-\d+$/', $preview) === 1;

                return [
                    'id' => $container['container_id'],
                    'query' => $hasEnvironment ? "&gtm_auth={$auth}&gtm_preview={$preview}&gtm_cookies_win=x" : '',
                ];
            })
            ->values()
            ->all();

        if ($containers === []) {
            return null;
        }

        $dataLayerName = (string) ($settings['data_layer_name'] ?? '');
        $serverUrl = rtrim((string) ($settings['server_container_url'] ?? ''), '/');

        $consent = null;

        if ($settings['consent_mode_enabled'] ?? false) {
            $consent = collect(self::GOOGLE_CONSENT_TYPES)
                ->mapWithKeys(fn (string $type): array => [
                    $type => ($settings['consent'][$type] ?? 'denied') === 'granted' ? 'granted' : 'denied',
                ])
                ->all();

            $waitForUpdate = (int) ($settings['consent_wait_for_update'] ?? 0);

            if ($waitForUpdate > 0) {
                $consent['wait_for_update'] = $waitForUpdate;
            }
        }

        return [
            'containers' => $containers,
            'data_layer_name' => preg_match('/^[A-Za-z_$][A-Za-z0-9_$]*$/', $dataLayerName) === 1 ? $dataLayerName : 'dataLayer',
            'base_url' => str_starts_with($serverUrl, 'https://') && filter_var($serverUrl, FILTER_VALIDATE_URL) ? $serverUrl : 'https://www.googletagmanager.com',
            'consent' => $consent,
            'ads_data_redaction' => (bool) ($settings['ads_data_redaction'] ?? false),
            'url_passthrough' => (bool) ($settings['url_passthrough'] ?? false),
            'data_layer_variables' => collect($settings['data_layer_variables'] ?? [])
                ->filter(fn (mixed $value, mixed $key): bool => is_string($key) && preg_match('/^[A-Za-z0-9_.]{1,64}$/', $key) === 1 && is_scalar($value))
                ->map(fn (mixed $value): string => (string) $value)
                ->all(),
        ];
    }
}
