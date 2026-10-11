<?php

use App\Filament\Resources\Redirects\Pages\ManageRedirects;
use App\Models\Redirect;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;

it('redirects with each status code', function (int $status) {
    Redirect::factory()->create(['from_path' => '/old', 'to_url' => '/new', 'status_code' => $status]);

    $this->get('/old')->assertRedirect('/new')->assertStatus($status);
})->with([301, 302, 307, 308]);

it('redirects to absolute urls', function () {
    Redirect::factory()->create(['from_path' => '/ext', 'to_url' => 'https://example.com/x']);

    $this->get('/ext')->assertRedirect('https://example.com/x');
});

it('preserves the query string', function () {
    Redirect::factory()->create(['from_path' => '/old', 'to_url' => '/new']);
    Redirect::factory()->create(['from_path' => '/old2', 'to_url' => '/new?a=1']);

    $this->get('/old?utm=x&y=2')->assertRedirect('/new?utm=x&y=2');
    $this->get('/old2?utm=x')->assertRedirect('/new?a=1&utm=x');
});

it('drops the query string when not preserved', function () {
    Redirect::factory()->create(['from_path' => '/old', 'to_url' => '/new', 'preserve_query_string' => false]);

    $this->get('/old?utm=x')->assertRedirect('/new');
});

it('ignores inactive redirects', function () {
    Redirect::factory()->inactive()->create(['from_path' => '/old']);

    $this->get('/old')->assertNotFound();
});

it('never redirects protected paths', function (string $path) {
    Redirect::query()->insert([
        'from_path' => $path, 'to_url' => '/elsewhere', 'status_code' => 301,
        'is_active' => true, 'preserve_query_string' => true, 'hits' => 0,
    ]);

    $response = $this->get($path);

    expect($response->isRedirect('/elsewhere'))->toBeFalse();
})->with(['/admin/foo', '/livewire/x', '/filament/x', '/storage/a', '/build/a', '/up']);

it('does not redirect non GET requests', function () {
    Redirect::factory()->create(['from_path' => '/old', 'to_url' => '/new']);

    expect($this->post('/old')->isRedirect('/new'))->toBeFalse();
});

it('increments hits and sets last hit', function () {
    $redirect = Redirect::factory()->create(['from_path' => '/old', 'to_url' => '/new']);

    $this->get('/old');
    $this->get('/old');

    $redirect->refresh();
    expect($redirect->hits)->toBe(2)->and($redirect->last_hit_at)->not->toBeNull();
    expect(Cache::has('redirects.active'))->toBeTrue();
});

it('normalizes paths', function () {
    $redirect = Redirect::factory()->create(['from_path' => 'Old-Page/', 'to_url' => '/new']);

    expect($redirect->from_path)->toBe('/old-page');
    $this->get('/OLD-page/')->assertRedirect('/new');
    expect(Redirect::normalizePath('/'))->toBe('/');
});

it('flushes the cache on create, update and delete', function () {
    expect(Redirect::activeMap())->toBeEmpty();

    $redirect = Redirect::factory()->create(['from_path' => '/a']);
    expect(Redirect::activeMap())->toHaveKey('/a');

    $redirect->update(['from_path' => '/b']);
    expect(Redirect::activeMap())->toHaveKey('/b')->not->toHaveKey('/a');

    $redirect->delete();
    expect(Redirect::activeMap())->toBeEmpty();
});

describe('filament', function () {
    beforeEach(fn () => $this->actingAs(User::factory()->create()));

    it('creates a redirect with a normalized path', function () {
        Livewire::test(ManageRedirects::class)
            ->callAction('create', ['from_path' => '/Old/', 'to_url' => '/new', 'status_code' => 302, 'is_active' => true, 'preserve_query_string' => true])
            ->assertHasNoActionErrors();

        expect(Redirect::query()->where('from_path', '/old')->where('status_code', 302)->exists())->toBeTrue();
    });

    it('validates input', function (array $data) {
        Redirect::factory()->create(['from_path' => '/taken']);

        Livewire::test(ManageRedirects::class)
            ->callAction('create', $data + ['status_code' => 301])
            ->assertHasActionErrors(['from_path']);
    })->with([
        'self loop' => [['from_path' => '/loop', 'to_url' => '/loop/']],
        'protected prefix' => [['from_path' => '/admin/x', 'to_url' => '/new']],
        'no leading slash' => [['from_path' => 'nope', 'to_url' => '/new']],
        'duplicate' => [['from_path' => '/Taken/', 'to_url' => '/new']],
    ]);

    it('rejects an absolute self loop', function () {
        Livewire::test(ManageRedirects::class)
            ->callAction('create', ['from_path' => '/loop', 'to_url' => rtrim(config('app.url'), '/').'/loop?x=1', 'status_code' => 301])
            ->assertHasActionErrors(['from_path']);
    });

    it('rejects an invalid target', function () {
        Livewire::test(ManageRedirects::class)
            ->callAction('create', ['from_path' => '/a', 'to_url' => 'ftp://x', 'status_code' => 301])
            ->assertHasActionErrors(['to_url']);
    });
});
