<?php

use App\Filament\Pages\CookieConsentSettings;
use App\Filament\Pages\MetaPixelSettings;
use App\Filament\Pages\RobotsTxtSettings;
use App\Filament\Pages\SettingsBackup;
use App\Filament\Pages\TikTokPixelSettings;
use App\Models\Redirect;
use App\Models\SeoSetting;
use App\Models\User;
use Filament\Forms\Components\Repeater;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Livewire;

beforeEach(function () {
    SeoSetting::getSettings();
});

function headOf(string $html): string
{
    return Str::before($html, '</head>');
}

it('renders nothing for pixels and consent when disabled', function () {
    $html = $this->get('/')->getContent();

    expect($html)
        ->not->toContain('connect.facebook.net')
        ->not->toContain('analytics.tiktok.com')
        ->not->toContain('id="cc-root"');
});

it('renders the Meta Pixel for every valid pixel id with noscript fallbacks', function () {
    SeoSetting::query()->first()->update([
        'meta_pixel_enabled' => true,
        'meta_pixel' => ['pixels' => [['pixel_id' => '123456789012345'], ['pixel_id' => '987654321098765'], ['pixel_id' => 'nope']], 'lead_event_enabled' => true, 'lead_event_name' => 'Lead'],
    ]);

    $html = $this->get('/')->getContent();

    expect(headOf($html))
        ->toContain('https://connect.facebook.net/en_US/fbevents.js')
        ->toContain('fbq(\'init\', "123456789012345")')
        ->toContain('fbq(\'init\', "987654321098765")')
        ->toContain("fbq('track', 'PageView')")
        ->toContain('"meta_event":"Lead"')
        ->not->toContain("fbq('consent', 'revoke')")
        ->not->toContain('nope');

    expect($html)->toContain('https://www.facebook.com/tr?id=123456789012345&ev=PageView&noscript=1');
});

it('renders the TikTok Pixel for every valid pixel id', function () {
    SeoSetting::query()->first()->update([
        'tiktok_pixel_enabled' => true,
        'tiktok_pixel' => ['pixels' => [['pixel_id' => 'C1A2B3C4D5E6F7G8H9I0']], 'lead_event_enabled' => true, 'lead_event_name' => 'Lead'],
    ]);

    expect(headOf($this->get('/')->getContent()))
        ->toContain('https://analytics.tiktok.com/i18n/pixel/events.js')
        ->toContain('ttq.load("C1A2B3C4D5E6F7G8H9I0")')
        ->toContain('ttq.page()')
        ->toContain('"tiktok_event":"Lead"')
        ->not->toContain('ttq.holdConsent()');
});

it('saves Meta and TikTok pixel settings and validates ids', function (string $page, string $column, string $validId, string $invalidId, string $event) {
    $this->actingAs(User::factory()->create());

    $undoRepeaterFake = Repeater::fake();

    Livewire::test($page)
        ->fillForm(['enabled' => true, 'pixels' => [['pixel_id' => $invalidId]]])
        ->call('save')
        ->assertHasFormErrors(['pixels.0.pixel_id']);

    Livewire::test($page)
        ->fillForm(['enabled' => true, 'pixels' => [], 'lead_event_enabled' => true, 'lead_event_name' => $event])
        ->call('save')
        ->assertHasFormErrors(['pixels']);

    Livewire::test($page)
        ->fillForm(['enabled' => true, 'pixels' => [['pixel_id' => ' '.strtolower($validId).' ']], 'lead_event_enabled' => true, 'lead_event_name' => $event])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $undoRepeaterFake();

    $settings = SeoSetting::query()->first();

    expect($settings->{"{$column}_enabled"})->toBeTrue()
        ->and($settings->{$column}['pixels'][0]['pixel_id'])->toBe($validId)
        ->and($settings->{$column}['lead_event_name'])->toBe($event);
})->with([
    'meta' => [MetaPixelSettings::class, 'meta_pixel', '123456789012345', '12ab', 'CompleteRegistration'],
    'tiktok' => [TikTokPixelSettings::class, 'tiktok_pixel', 'C1A2B3C4D5E6F7G8H9I0', 'short!', 'SubmitForm'],
]);

it('renders the cookie consent banner with defaults, denies storage and holds pixels until consent', function () {
    SeoSetting::query()->first()->update([
        'cookie_consent_enabled' => true,
        'cookie_consent' => ['title' => 'Cookies <b>here</b>', 'primary_color' => 'red;}body{display:none', 'privacy_link_url' => 'javascript:alert(1)', 'privacy_link_label' => 'Privacy'],
        'meta_pixel_enabled' => true,
        'meta_pixel' => ['pixels' => [['pixel_id' => '123456789012345']]],
        'tiktok_pixel_enabled' => true,
        'tiktok_pixel' => ['pixels' => [['pixel_id' => 'C1A2B3C4D5E6F7G8H9I0']]],
        'microsoft_clarity_enabled' => true,
        'microsoft_clarity_project_id' => 'abcd123456',
    ]);

    $html = $this->get('/')->getContent();
    $head = headOf($html);

    expect($head)
        ->toContain("g('consent','default',")
        ->toContain('"ad_storage":"denied"')
        ->toContain('"analytics_storage":"denied"')
        ->toContain('"security_storage":"granted"')
        ->toContain('"wait_for_update":500')
        ->toContain("fbq('consent', 'revoke')")
        ->toContain('ttq.holdConsent()')
        ->toContain("window.clarity('consentv2'")
        ->and(strpos($head, "g('consent','default',"))->toBeLessThan(strpos($head, 'fbevents.js'));

    expect($html)
        ->toContain('id="cc-root"')
        ->toContain('Cookies &lt;b&gt;here&lt;/b&gt;')
        ->toContain('--cc-primary: '.SeoSetting::COOKIE_CONSENT_DEFAULTS['primary_color'])
        ->toContain(SeoSetting::COOKIE_CONSENT_DEFAULTS['accept_label'])
        ->not->toContain('facebook.com/tr?id=')
        ->and(Str::after($html, 'id="cc-root"'))->not->toContain('javascript:alert(1)');
});

it('uses GTM consent defaults when Consent Mode is configured there', function () {
    SeoSetting::query()->first()->update([
        'cookie_consent_enabled' => true,
        'google_tag_manager_enabled' => true,
        'google_tag_manager' => ['containers' => [['container_id' => 'GTM-ABC1234']], 'consent_mode_enabled' => true, 'consent' => ['analytics_storage' => 'granted']],
    ]);

    expect(headOf($this->get('/')->getContent()))->toContain('"analytics_storage":"granted"');
});

it('saves cookie consent customisation and can ask everyone again', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CookieConsentSettings::class)
        ->assertSuccessful()
        ->fillForm([
            'enabled' => true,
            'title' => 'Custom title',
            'layout' => 'modal',
            'primary_color' => '#123456',
            'functional_enabled' => true,
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    $settings = SeoSetting::query()->first();

    expect($settings->cookie_consent_enabled)->toBeTrue()
        ->and($settings->cookie_consent['title'])->toBe('Custom title')
        ->and($settings->cookie_consent['layout'])->toBe('modal')
        ->and($settings->cookie_consent['primary_color'])->toBe('#123456')
        ->and($settings->cookie_consent['version'])->toBe(1);

    Livewire::test(CookieConsentSettings::class)->callAction('resetConsents');

    expect(SeoSetting::query()->first()->cookie_consent['version'])->toBe(2);
});

it('validates cookie consent settings', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(CookieConsentSettings::class)
        ->fillForm(['title' => '', 'privacy_link_url' => 'javascript:alert(1)', 'expiry_days' => 1000, 'primary_color' => 'red'])
        ->call('save')
        ->assertHasFormErrors(['title', 'privacy_link_url', 'expiry_days', 'primary_color']);
});

it('saves custom robots.txt rules, writes the file and serves them', function () {
    $this->actingAs(User::factory()->create());

    $robotsPath = public_path('robots.txt');
    $original = file_exists($robotsPath) ? file_get_contents($robotsPath) : null;

    Livewire::test(RobotsTxtSettings::class)
        ->fillForm(['robots_txt' => "User-agent: *\nDisallow: /private", 'robots_txt_append_sitemap' => false])
        ->call('save')
        ->assertNotified();

    expect(file_get_contents($robotsPath))->toBe("User-agent: *\nDisallow: /private\n");

    $this->get('/robots.txt')
        ->assertSuccessful()
        ->assertSee('Disallow: /private', false)
        ->assertDontSee('Sitemap:', false);

    $original === null ? @unlink($robotsPath) : file_put_contents($robotsPath, $original);
});

it('backs up and restores settings and redirects', function () {
    Storage::fake('local');

    SeoSetting::query()->first()->update(['site_title' => 'Before', 'meta_pixel_enabled' => true, 'meta_pixel' => ['pixels' => [['pixel_id' => '123456789012345']]]]);
    Redirect::factory()->create(['from_path' => '/old', 'to_url' => '/new']);

    $name = SettingsBackup::storeBackup('test');

    Storage::disk('local')->assertExists(SettingsBackup::DIRECTORY.'/'.$name);

    SeoSetting::query()->first()->update(['site_title' => 'After', 'meta_pixel_enabled' => false]);
    Redirect::query()->delete();
    Redirect::factory()->create(['from_path' => '/other', 'to_url' => '/x']);

    $this->actingAs(User::factory()->create());

    Livewire::test(SettingsBackup::class)
        ->assertSee($name)
        ->callAction('restore', arguments: ['name' => $name])
        ->assertNotified('Settings restored');

    $settings = SeoSetting::query()->first();

    expect($settings->site_title)->toBe('Before')
        ->and($settings->meta_pixel_enabled)->toBeTrue()
        ->and($settings->meta_pixel['pixels'][0]['pixel_id'])->toBe('123456789012345')
        ->and(Redirect::query()->pluck('from_path')->all())->toBe(['/old'])
        ->and(collect(Storage::disk('local')->files(SettingsBackup::DIRECTORY))->filter(fn (string $file): bool => str_contains($file, 'auto-before-restore')))->toHaveCount(1);
});

it('rejects invalid backup files and unknown backup names', function () {
    Storage::fake('local');

    expect(fn () => SettingsBackup::restore(['format' => 'something-else', 'version' => 1, 'settings' => [], 'redirects' => []]))
        ->toThrow(InvalidArgumentException::class);

    $this->actingAs(User::factory()->create());

    Livewire::test(SettingsBackup::class)
        ->callAction('download', arguments: ['name' => '../../.env'])
        ->assertNotified('Backup not found')
        ->callAction('restore', arguments: ['name' => 'backup-missing.json'])
        ->assertNotified('Backup not found');
});

it('creates and downloads backups from the page', function () {
    Storage::fake('local');

    $this->actingAs(User::factory()->create());

    Livewire::test(SettingsBackup::class)
        ->callAction('createBackup', data: ['label' => 'Launch Day'])
        ->assertNotified();

    expect(Storage::disk('local')->files(SettingsBackup::DIRECTORY))->toHaveCount(1)
        ->and(Storage::disk('local')->files(SettingsBackup::DIRECTORY)[0])->toContain('-launch-day.json');

    Livewire::test(SettingsBackup::class)
        ->callAction('downloadCurrent')
        ->assertFileDownloaded();
});

it('renders all new admin pages', function (string $url) {
    $this->actingAs(User::factory()->create());

    $this->get($url)->assertSuccessful();
})->with(['/admin/meta-pixel', '/admin/tiktok-pixel', '/admin/cookie-consent', '/admin/robots-txt', '/admin/settings-backup']);
