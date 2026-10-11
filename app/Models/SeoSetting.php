<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SeoSetting extends Model
{
    /**
     * Meta Pixel standard events offered for the contact / waitlist form.
     *
     * @var array<string, string>
     */
    public const META_PIXEL_EVENTS = [
        'Lead' => 'Lead',
        'CompleteRegistration' => 'CompleteRegistration',
        'Contact' => 'Contact',
        'SubmitApplication' => 'SubmitApplication',
        'Schedule' => 'Schedule',
    ];

    /**
     * TikTok Pixel standard events offered for the contact / waitlist form.
     *
     * @var array<string, string>
     */
    public const TIKTOK_PIXEL_EVENTS = [
        'Lead' => 'Lead',
        'SubmitForm' => 'SubmitForm',
        'Contact' => 'Contact',
        'CompleteRegistration' => 'CompleteRegistration',
        'SubmitApplication' => 'SubmitApplication',
    ];

    /**
     * Cookie consent banner defaults; saved settings are merged over these.
     *
     * @var array<string, string|int|bool>
     */
    public const COOKIE_CONSENT_DEFAULTS = [
        'title' => 'نحن نستخدم ملفات تعريف الارتباط',
        'message' => 'نستخدم ملفات تعريف الارتباط (الكوكيز) لتحسين تجربتك، وتحليل زيارات الموقع، وقياس أداء إعلاناتنا. يمكنك قبول الكل أو رفض غير الضروري أو اختيار ما يناسبك.',
        'accept_label' => 'قبول الكل',
        'reject_label' => 'رفض غير الضروري',
        'customize_label' => 'تخصيص',
        'save_label' => 'حفظ الاختيارات',
        'privacy_link_label' => 'سياسة الخصوصية',
        'privacy_link_url' => '',
        'necessary_label' => 'ضرورية',
        'necessary_description' => 'مطلوبة لعمل الموقع بشكل صحيح ولا يمكن إيقافها.',
        'analytics_enabled' => true,
        'analytics_label' => 'تحليلية',
        'analytics_description' => 'تساعدنا على فهم كيفية استخدام الزوار للموقع (Google Analytics، Microsoft Clarity).',
        'marketing_enabled' => true,
        'marketing_label' => 'تسويقية',
        'marketing_description' => 'تُستخدم لقياس الإعلانات وعرض إعلانات مناسبة لك (Meta، TikTok، Google Ads).',
        'functional_enabled' => false,
        'functional_label' => 'وظيفية',
        'functional_description' => 'تتذكر تفضيلاتك لتقديم تجربة مخصصة.',
        'layout' => 'box_end',
        'direction' => 'rtl',
        'background_color' => '#111111',
        'text_color' => '#ffffff',
        'primary_color' => '#e11d2e',
        'primary_text_color' => '#ffffff',
        'secondary_color' => '#3f3f46',
        'border_radius' => 16,
        'show_reject_button' => true,
        'show_reopen_button' => true,
        'reopen_position' => 'start',
        'reopen_label' => 'إعدادات الكوكيز',
        'expiry_days' => 180,
        'version' => 1,
    ];

    /**
     * Banner layouts.
     *
     * @var array<string, string>
     */
    public const COOKIE_CONSENT_LAYOUTS = [
        'bar' => 'Full-width bar (bottom)',
        'box_start' => 'Floating box (bottom, start side)',
        'box_end' => 'Floating box (bottom, end side)',
        'modal' => 'Centered modal',
    ];

    /**
     * robots.txt rules served when no custom content is saved. The sitemap line is appended separately.
     */
    public const DEFAULT_ROBOTS_TXT = <<<'TXT'
        User-agent: *
        Allow: /
        Disallow: /admin
        Disallow: /dashboard
        Disallow: /settings
        Disallow: /login
        Disallow: /register
        Disallow: /forgot-password
        Disallow: /reset-password
        Disallow: /email/verify
        Disallow: /two-factor-challenge
        Disallow: /user/
        TXT;

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
        'meta_pixel_enabled',
        'meta_pixel',
        'tiktok_pixel_enabled',
        'tiktok_pixel',
        'cookie_consent_enabled',
        'cookie_consent',
        'robots_txt',
        'robots_txt_append_sitemap',
        'microsoft_clarity_enabled',
        'microsoft_clarity_project_id',
    ];

    protected function casts(): array
    {
        return [
            'google_analytics_enabled' => 'boolean',
            'google_tag_manager_enabled' => 'boolean',
            'google_tag_manager' => 'array',
            'meta_pixel_enabled' => 'boolean',
            'meta_pixel' => 'array',
            'tiktok_pixel_enabled' => 'boolean',
            'tiktok_pixel' => 'array',
            'cookie_consent_enabled' => 'boolean',
            'cookie_consent' => 'array',
            'robots_txt_append_sitemap' => 'boolean',
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

    /**
     * Build a sanitized Meta Pixel config for rendering, or null when inactive.
     *
     * @param  array<string, mixed>  $seo
     * @return array{pixel_ids: list<string>, lead_event: ?string}|null
     */
    public static function metaPixelConfig(array $seo): ?array
    {
        return self::pixelConfig($seo, 'meta_pixel', '/^\d{10,20}$/', self::META_PIXEL_EVENTS);
    }

    /**
     * Build a sanitized TikTok Pixel config for rendering, or null when inactive.
     *
     * @param  array<string, mixed>  $seo
     * @return array{pixel_ids: list<string>, lead_event: ?string}|null
     */
    public static function tiktokPixelConfig(array $seo): ?array
    {
        return self::pixelConfig($seo, 'tiktok_pixel', '/^[A-Z0-9]{15,30}$/', self::TIKTOK_PIXEL_EVENTS);
    }

    /**
     * @param  array<string, mixed>  $seo
     * @param  array<string, string>  $events
     * @return array{pixel_ids: list<string>, lead_event: ?string}|null
     */
    protected static function pixelConfig(array $seo, string $key, string $idPattern, array $events): ?array
    {
        $settings = $seo[$key] ?? null;

        if (! ($seo["{$key}_enabled"] ?? false) || ! is_array($settings)) {
            return null;
        }

        $pixelIds = collect($settings['pixels'] ?? [])
            ->map(fn (mixed $pixel): string => is_array($pixel) ? (string) ($pixel['pixel_id'] ?? '') : '')
            ->filter(fn (string $pixelId): bool => preg_match($idPattern, $pixelId) === 1)
            ->unique()
            ->values()
            ->all();

        if ($pixelIds === []) {
            return null;
        }

        $leadEvent = (string) ($settings['lead_event_name'] ?? '');

        return [
            'pixel_ids' => $pixelIds,
            'lead_event' => ($settings['lead_event_enabled'] ?? false) && array_key_exists($leadEvent, $events) ? $leadEvent : null,
        ];
    }

    /**
     * The full robots.txt body for the current settings.
     */
    public static function robotsTxtContent(): string
    {
        $settings = self::query()->first();

        $content = trim(str_replace("\r\n", "\n", filled($settings?->robots_txt) ? $settings->robots_txt : self::DEFAULT_ROBOTS_TXT));

        if ($settings?->robots_txt_append_sitemap ?? true) {
            $content .= "\n\nSitemap: ".rtrim((string) config('app.url'), '/').'/sitemap.xml';
        }

        return $content."\n";
    }

    /**
     * Write public/robots.txt so the web server serves it directly with a 200 status.
     */
    public static function writeRobotsTxtFile(): void
    {
        file_put_contents(public_path('robots.txt'), self::robotsTxtContent());
    }

    /**
     * Build a sanitized cookie consent banner config for rendering, or null when disabled.
     *
     * @param  array<string, mixed>  $seo
     * @return array<string, string|int|bool>|null
     */
    public static function cookieConsentConfig(array $seo): ?array
    {
        if (! ($seo['cookie_consent_enabled'] ?? false)) {
            return null;
        }

        $saved = is_array($seo['cookie_consent'] ?? null) ? $seo['cookie_consent'] : [];

        $config = collect(self::COOKIE_CONSENT_DEFAULTS)
            ->map(function (string|int|bool $default, string $key) use ($saved): string|int|bool {
                $value = $saved[$key] ?? null;

                return match (true) {
                    is_bool($default) => $value === null ? $default : (bool) $value,
                    is_int($default) => is_numeric($value) ? (int) $value : $default,
                    str_ends_with($key, '_color') => is_string($value) && preg_match('/^#[0-9a-fA-F]{6}$/', $value) === 1 ? $value : $default,
                    default => is_string($value) && trim($value) !== '' ? trim($value) : $default,
                };
            })
            ->all();

        $config['layout'] = array_key_exists((string) $config['layout'], self::COOKIE_CONSENT_LAYOUTS) ? $config['layout'] : self::COOKIE_CONSENT_DEFAULTS['layout'];
        $config['direction'] = $config['direction'] === 'ltr' ? 'ltr' : 'rtl';
        $config['reopen_position'] = $config['reopen_position'] === 'end' ? 'end' : 'start';
        $config['border_radius'] = max(0, min(32, (int) $config['border_radius']));
        $config['expiry_days'] = max(1, min(395, (int) $config['expiry_days']));
        $config['version'] = max(1, (int) $config['version']);

        $privacyUrl = (string) ($saved['privacy_link_url'] ?? '');
        $config['privacy_link_url'] = preg_match('#^(/(?!/)|https?://)#i', $privacyUrl) === 1 ? $privacyUrl : '';

        return $config;
    }

    /**
     * Everything the public layout needs to render tracking tags and the consent banner.
     *
     * @param  array<string, mixed>  $seo
     * @return array{
     *     gtm: array<string, mixed>|null,
     *     ga4_measurement_id: ?string,
     *     clarity_project_id: ?string,
     *     meta_pixel: array{pixel_ids: list<string>, lead_event: ?string}|null,
     *     tiktok_pixel: array{pixel_ids: list<string>, lead_event: ?string}|null,
     *     cookie_consent: array<string, string|int|bool>|null,
     *     data_layer_name: string,
     *     consent_defaults: array<string, string|int>|null,
     *     lead_tracking: array{data_layer_name: string, gtm_event: ?string, meta_event: ?string, tiktok_event: ?string},
     * }
     */
    public static function trackingConfig(array $seo): array
    {
        $gtm = self::googleTagManagerConfig($seo);
        $cookieConsent = self::cookieConsentConfig($seo);
        $dataLayerName = $gtm['data_layer_name'] ?? 'dataLayer';

        $measurementId = (string) ($seo['google_analytics_measurement_id'] ?? '');
        $clarityProjectId = trim((string) ($seo['microsoft_clarity_project_id'] ?? ''));

        $consentDefaults = $gtm['consent'] ?? null;

        if ($consentDefaults === null && $cookieConsent !== null) {
            $consentDefaults = [
                ...array_fill_keys(self::GOOGLE_CONSENT_TYPES, 'denied'),
                'security_storage' => 'granted',
                'wait_for_update' => 500,
            ];
        }

        $gtmSettings = is_array($seo['google_tag_manager'] ?? null) ? $seo['google_tag_manager'] : [];
        $gtmEvent = (string) ($gtmSettings['lead_event_name'] ?? '');
        $metaPixel = self::metaPixelConfig($seo);
        $tiktokPixel = self::tiktokPixelConfig($seo);

        return [
            'gtm' => $gtm,
            'ga4_measurement_id' => ($seo['google_analytics_enabled'] ?? false) && preg_match('/^G-[A-Z0-9]+$/', $measurementId) === 1 ? $measurementId : null,
            'clarity_project_id' => ($seo['microsoft_clarity_enabled'] ?? false) && preg_match('/^[a-z0-9]{8,32}$/', $clarityProjectId) === 1 ? $clarityProjectId : null,
            'meta_pixel' => $metaPixel,
            'tiktok_pixel' => $tiktokPixel,
            'cookie_consent' => $cookieConsent,
            'data_layer_name' => $dataLayerName,
            'consent_defaults' => $consentDefaults,
            'lead_tracking' => [
                'data_layer_name' => $dataLayerName,
                'gtm_event' => $gtm !== null && ($gtmSettings['lead_event_enabled'] ?? false) && preg_match('/^[A-Za-z0-9_]{1,40}$/', $gtmEvent) === 1 ? $gtmEvent : null,
                'meta_event' => $metaPixel['lead_event'] ?? null,
                'tiktok_event' => $tiktokPixel['lead_event'] ?? null,
            ],
        ];
    }
}
