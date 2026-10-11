<?php

use App\Filament\Pages\CustomScriptsSettings;
use App\Filament\Pages\GoogleAnalyticsSettings;
use App\Filament\Pages\GoogleSearchConsoleSettings;
use App\Filament\Pages\GoogleTagManagerSettings;
use App\Models\SeoSetting;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function () {
    // Create SEO settings for tests
    SeoSetting::create([
        'site_title' => 'Test Site Title',
        'site_description' => 'Test site description for SEO',
        'keywords' => 'test, seo, keywords',
        'author' => 'Test Author',
        'og_title' => 'Test OG Title',
        'og_description' => 'Test OG Description',
        'twitter_handle' => 'testhandle',
        'twitter_card_type' => 'summary_large_image',
        'organization_name' => 'Test Organization',
        'course_name' => 'Test Course',
        'course_description' => 'Test course description',
        'canonical_url' => 'https://example.com',
        'google_site_verification' => 'test-verification-code',
        'google_search_console_enabled' => true,
    ]);
});

it('renders Open Graph meta tags server-side in HTML', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    // Check Open Graph tags are in the raw HTML
    expect($content)->toContain('property="og:title"');
    expect($content)->toContain('property="og:description"');
    expect($content)->toContain('property="og:type"');
    expect($content)->toContain('property="og:url"');
    expect($content)->toContain('property="og:locale"');
});

it('renders Twitter Card meta tags server-side in HTML', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    // Check Twitter Card tags are in the raw HTML
    expect($content)->toContain('name="twitter:card"');
    expect($content)->toContain('name="twitter:title"');
    expect($content)->toContain('name="twitter:description"');
});

it('renders basic SEO meta tags server-side in HTML', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    // Check basic meta tags are in the raw HTML
    expect($content)->toContain('name="description"');
    expect($content)->toContain('name="keywords"');
    expect($content)->toContain('name="author"');
    expect($content)->toContain('name="robots"');
    expect($content)->toContain('rel="canonical"');
});

it('renders correct OG title content from database', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    expect($content)->toContain('content="Test OG Title"');
});

it('renders correct OG description content from database', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    expect($content)->toContain('content="Test OG Description"');
});

it('renders correct site description from database', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    expect($content)->toContain('content="Test site description for SEO"');
});

it('renders Google site verification when set', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    expect($content)->toContain('name="google-site-verification"');
    expect($content)->toContain('content="test-verification-code"');
});

it('renders Twitter handle correctly', function () {
    $response = $this->get('/');
    $content = $response->getContent();

    expect($content)->toContain('name="twitter:site"');
    expect($content)->toContain('@testhandle');
});

it('falls back to site title when OG title is empty', function () {
    SeoSetting::query()->delete();
    SeoSetting::create([
        'site_title' => 'Fallback Site Title',
        'site_description' => 'Fallback description',
        'og_title' => null,
        'og_description' => null,
    ]);

    $response = $this->get('/');
    $content = $response->getContent();

    // OG title should fall back to site title
    expect($content)->toContain('content="Fallback Site Title"');
});

it('does not render Google Analytics gtag when disabled or Measurement ID unset', function () {
    SeoSetting::query()->first()->update([
        'google_analytics_enabled' => false,
        'google_analytics_measurement_id' => null,
    ]);

    $content = $this->get('/')->getContent();

    expect($content)->not->toContain('googletagmanager.com/gtag/js');
});

it('renders GA4 gtag snippet when enabled with a valid Measurement ID', function () {
    SeoSetting::query()->first()->update([
        'google_analytics_enabled' => true,
        'google_analytics_measurement_id' => 'G-TESTABCDEF12',
    ]);

    $content = $this->get('/')->getContent();

    expect($content)->toContain('https://www.googletagmanager.com/gtag/js?id=G-TESTABCDEF12')
        ->and($content)->toContain("gtag('config', 'G-TESTABCDEF12')");
});

it('does not render Microsoft Clarity when disabled or Project ID unset', function () {
    SeoSetting::query()->first()->update([
        'microsoft_clarity_enabled' => false,
        'microsoft_clarity_project_id' => null,
    ]);

    $content = $this->get('/')->getContent();

    expect($content)->not->toContain('clarity.ms/tag');
});

it('renders Microsoft Clarity snippet when enabled with a valid Project ID', function () {
    SeoSetting::query()->first()->update([
        'microsoft_clarity_enabled' => true,
        'microsoft_clarity_project_id' => 'abcd123456',
    ]);

    $content = $this->get('/')->getContent();

    expect($content)->toContain('https://www.clarity.ms/tag/')
        ->and($content)->toContain('})(window, document, "clarity", "script", "abcd123456")');
});

it('renders custom header scripts before </head> and footer scripts before </body>', function () {
    SeoSetting::query()->first()->update([
        'custom_head_scripts' => '<script>window.headMarker = 1;</script>',
        'custom_footer_scripts' => '<script>window.footerMarker = 1;</script>',
    ]);

    $content = $this->get('/')->getContent();

    $headPosition = strpos($content, '<script>window.headMarker = 1;</script>');
    $footerPosition = strpos($content, '<script>window.footerMarker = 1;</script>');

    expect($headPosition)->toBeInt()->toBeLessThan(strpos($content, '</head>'))
        ->and($footerPosition)->toBeInt()->toBeGreaterThan(strpos($content, '<body'))
        ->and($footerPosition)->toBeLessThan(strpos($content, '</body>'));
});

it('renders nothing extra when custom scripts are empty', function () {
    $content = $this->get('/')->getContent();

    expect($content)->not->toContain('headMarker')
        ->and($content)->not->toContain('footerMarker');
});

it('saves custom scripts from the Filament settings page', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CustomScriptsSettings::class)
        ->set('data.custom_head_scripts', '<script>head()</script>')
        ->set('data.custom_footer_scripts', '<script>footer()</script>')
        ->call('save')
        ->assertNotified();

    expect(SeoSetting::query()->first())
        ->custom_head_scripts->toBe('<script>head()</script>')
        ->custom_footer_scripts->toBe('<script>footer()</script>');

    Livewire::test(CustomScriptsSettings::class)
        ->set('data.custom_head_scripts', '')
        ->set('data.custom_footer_scripts', '')
        ->call('save');

    expect(SeoSetting::query()->first())
        ->custom_head_scripts->toBeNull()
        ->custom_footer_scripts->toBeNull();
});

it('does not render Google site verification when Search Console is disabled', function () {
    SeoSetting::query()->first()->update(['google_search_console_enabled' => false]);

    expect($this->get('/')->getContent())->not->toContain('name="google-site-verification"');
});

it('serves the Google Search Console HTML verification file only when configured and enabled', function () {
    SeoSetting::query()->first()->update(['google_search_console_html_file' => 'google1a2b3c4d.html']);

    $this->get('/google1a2b3c4d.html')
        ->assertSuccessful()
        ->assertContent('google-site-verification: google1a2b3c4d.html');

    $this->get('/google9999.html')->assertNotFound();

    SeoSetting::query()->first()->update(['google_search_console_enabled' => false]);

    $this->get('/google1a2b3c4d.html')->assertNotFound();
});

it('saves Google Search Console settings and extracts the code from a pasted meta tag', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(GoogleSearchConsoleSettings::class)
        ->assertSuccessful()
        ->set('data.google_search_console_enabled', true)
        ->set('data.google_site_verification', '<meta name="google-site-verification" content="AbC_123-xyz987" />')
        ->set('data.google_search_console_html_file', 'google1a2b3c4d.html')
        ->call('save')
        ->assertHasNoErrors()
        ->assertNotified();

    expect(SeoSetting::query()->first())
        ->google_search_console_enabled->toBeTrue()
        ->google_site_verification->toBe('AbC_123-xyz987')
        ->google_search_console_html_file->toBe('google1a2b3c4d.html');
});

it('requires a verification method when enabling Google Search Console', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(GoogleSearchConsoleSettings::class)
        ->set('data.google_search_console_enabled', true)
        ->set('data.google_site_verification', '')
        ->set('data.google_search_console_html_file', '')
        ->call('save')
        ->assertHasErrors(['google_site_verification']);
});

it('rejects an invalid Google Search Console HTML file name', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(GoogleSearchConsoleSettings::class)
        ->set('data.google_search_console_html_file', '../.env')
        ->call('save')
        ->assertHasErrors(['google_search_console_html_file']);
});

it('renders brand icons in the admin navigation and a code editor for custom scripts', function () {
    $this->actingAs(User::factory()->create());

    $this->get('/admin/custom-scripts')
        ->assertSuccessful()
        ->assertSee('fill="#f9ab00"', false)
        ->assertSee('radialGradient', false)
        ->assertSee('m32.656 20.58', false)
        ->assertSee('codeEditorFormComponent', false);
});

it('does not render Google Tag Manager when disabled', function () {
    SeoSetting::query()->first()->update([
        'google_tag_manager_enabled' => false,
        'google_tag_manager' => ['containers' => [['container_id' => 'GTM-ABC1234']]],
    ]);

    expect($this->get('/')->getContent())
        ->not->toContain("'gtm.start'")
        ->not->toContain('ns.html?id=GTM-ABC1234');
});

it('renders every Google Tag Manager container with environments, server url, consent and variables', function () {
    SeoSetting::query()->first()->update([
        'google_tag_manager_enabled' => true,
        'google_tag_manager' => [
            'containers' => [
                ['container_id' => 'GTM-ABC1234'],
                ['container_id' => 'GTM-XYZ9876', 'environment_auth' => 'aBc_12-3', 'environment_preview' => 'env-5'],
                ['container_id' => 'not-a-container'],
            ],
            'data_layer_name' => 'siteLayer',
            'server_container_url' => 'https://sgtm.example.com/',
            'consent_mode_enabled' => true,
            'consent' => ['analytics_storage' => 'granted'],
            'consent_wait_for_update' => 500,
            'ads_data_redaction' => true,
            'url_passthrough' => true,
            'data_layer_variables' => ['page_type' => 'landing', 'bad key!' => 'x'],
        ],
    ]);

    $content = $this->get('/')->getContent();

    expect($content)
        ->toContain('"GTM-ABC1234"')
        ->toContain('"GTM-XYZ9876"')
        ->toContain('"\\u0026gtm_auth=aBc_12-3\\u0026gtm_preview=env-5\\u0026gtm_cookies_win=x"')
        ->toContain('"https:\/\/sgtm.example.com"')
        ->toContain('https://sgtm.example.com/ns.html?id=GTM-ABC1234"')
        ->toContain('https://sgtm.example.com/ns.html?id=GTM-XYZ9876&amp;gtm_auth=aBc_12-3')
        ->toContain('"siteLayer"')
        ->toContain('"analytics_storage":"granted"')
        ->toContain('"ad_storage":"denied"')
        ->toContain('"wait_for_update":500')
        ->toContain("g('set','ads_data_redaction',true)")
        ->toContain("g('set','url_passthrough',true)")
        ->toContain('{"page_type":"landing"}')
        ->and(Str::before($content, '</head>'))
        ->not->toContain('not-a-container')
        ->not->toContain('bad key!');

    expect(strpos($content, "g('consent','default'"))->toBeLessThan(strpos($content, '"GTM-ABC1234"'));
});

it('saves Google Tag Manager settings from the Filament page', function () {
    $this->actingAs(User::factory()->create());

    $undoRepeaterFake = Repeater::fake();

    Livewire::test(GoogleTagManagerSettings::class)
        ->assertSuccessful()
        ->fillForm([
            'enabled' => true,
            'containers' => [
                ['container_id' => 'gtm-abc1234', 'environment_auth' => null, 'environment_preview' => null],
            ],
            'data_layer_name' => 'dataLayer',
            'lead_event_enabled' => true,
            'lead_event_name' => 'contact_lead',
            'data_layer_variables' => ['site' => 'baseet'],
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $undoRepeaterFake();

    $settings = SeoSetting::query()->first();

    expect($settings->google_tag_manager_enabled)->toBeTrue()
        ->and($settings->google_tag_manager['containers'][0]['container_id'])->toBe('GTM-ABC1234')
        ->and($settings->google_tag_manager['lead_event_name'])->toBe('contact_lead')
        ->and($settings->google_tag_manager['data_layer_variables'])->toBe(['site' => 'baseet']);
});

it('validates Google Tag Manager settings', function (array $data, string $error) {
    $this->actingAs(User::factory()->create());

    $undoRepeaterFake = Repeater::fake();

    Livewire::test(GoogleTagManagerSettings::class)
        ->fillForm(['enabled' => true, 'containers' => [['container_id' => 'GTM-ABC1234']], ...$data])
        ->call('save')
        ->assertHasFormErrors([$error]);

    $undoRepeaterFake();
})->with([
    'missing containers' => [['containers' => []], 'containers'],
    'invalid container id' => [['containers' => [['container_id' => 'UA-12345']]], 'containers.0.container_id'],
    'environment auth without preview' => [['containers' => [['container_id' => 'GTM-ABC1234', 'environment_auth' => 'abc']]], 'containers.0.environment_preview'],
    'invalid data layer name' => [['data_layer_name' => '1 bad'], 'data_layer_name'],
    'non-https server url' => [['server_container_url' => 'http://sgtm.example.com'], 'server_container_url'],
    'invalid variable key' => [['data_layer_variables' => ['bad key' => 'x']], 'data_layer_variables'],
]);

it('only accepts GA4 measurement IDs on the Google Analytics page', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(GoogleAnalyticsSettings::class)
        ->set('data.google_analytics_enabled', true)
        ->set('data.google_analytics_measurement_id', 'GTM-ABC1234')
        ->call('save')
        ->assertHasErrors(['google_analytics_measurement_id']);
});
