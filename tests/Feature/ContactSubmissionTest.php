<?php

use App\Filament\Resources\ContactSubmissions\Pages\ManageContactSubmissions;
use App\Filament\Resources\ContactSubmissionTabs\Pages\ManageContactSubmissionTabs;
use App\Mail\ContactSubmissionSubmitted;
use App\Models\ContactSubmission;
use App\Models\ContactSubmissionTab;
use App\Models\User;
use Database\Seeders\LandingPageSectionSeeder;
use Filament\Actions\Testing\TestAction;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;

beforeEach(function () {
    $this->seed(LandingPageSectionSeeder::class);
});

it('loads the welcome page with flash shared data', function () {
    $response = $this->get('/');

    $response->assertSuccessful();
    $response->assertInertia(fn ($page) => $page
        ->component('welcome')
        ->has('flash')
    );
});

it('stores a submission and queues the notification mailable', function () {
    Mail::fake();

    $payload = [
        'name' => 'Test User',
        'email' => 'submit@example.org',
        'phone' => '+20 101 2345678',
        'message' => 'Interested in joining the waitlist.',
        'prefers_whatsapp' => '1',
    ];

    $response = $this->from('/')->post('/contact', $payload);

    $response->assertRedirect();
    $response->assertSessionHas('success', true);

    $this->assertDatabaseHas('contact_submissions', [
        'email' => 'submit@example.org',
        'phone' => '201012345678',
    ]);

    expect(ContactSubmission::query()->where('email', 'submit@example.org')->value('prefers_whatsapp'))->toBeTrue();

    $submission = ContactSubmission::query()->where('email', 'submit@example.org')->first();
    expect($submission)->not->toBeNull()->and($submission->contacted)->toBeFalse();

    Mail::assertSent(ContactSubmissionSubmitted::class);
});

it('accepts eastern arabic numerals and saves western digits', function () {
    Mail::fake();

    $payload = [
        'name' => 'Test',
        'email' => 'arabic.digits@example.org',
        'phone' => '٠١٠٢٠٢٠٠٤٤٤',
        'message' => 'Hi',
        'prefers_whatsapp' => '1',
    ];

    $this->from('/')->post('/contact', $payload)->assertSessionHasNoErrors();

    $this->assertDatabaseHas('contact_submissions', [
        'email' => 'arabic.digits@example.org',
        'phone' => '01020200444',
    ]);
});

it('validates email, phone, and message', function () {
    Mail::fake();

    $payload = [
        'name' => 'Bad',
        'email' => 'not-an-email',
        'phone' => '123',
        'message' => '',
        'prefers_whatsapp' => '0',
    ];

    $this->from('/')->post('/contact', $payload)->assertSessionHasErrors([
        'email',
        'phone',
        'message',
    ]);

    expect(ContactSubmission::query()->count())->toBe(0);
    Mail::assertNothingSent();
});

it('honors prefers_whatsapp when set to false', function () {
    Mail::fake();

    $payload = [
        'name' => 'Someone',
        'email' => 'someone@example.org',
        'phone' => '201122334455',
        'message' => 'Hello!',
        'prefers_whatsapp' => '0',
    ];

    $this->from('/')->post('/contact', $payload)->assertSessionHasNoErrors();

    expect(ContactSubmission::query()->where('email', 'someone@example.org')->value('prefers_whatsapp'))->toBeFalse();
});

it('persists the contacted flag for admin follow-up', function () {
    $submission = ContactSubmission::factory()->create(['contacted' => false]);
    expect($submission->contacted)->toBeFalse();

    $submission->update(['contacted' => true]);

    expect($submission->fresh()->contacted)->toBeTrue();
});

it('allows authenticated users to open the filament contact submissions table', function () {
    $admin = User::factory()->create();

    $this->actingAs($admin);

    Livewire::test(ManageContactSubmissions::class)
        ->assertSuccessful();
});

it('filters the table by the selected tab', function () {
    $this->actingAs(User::factory()->create());

    $tab = ContactSubmissionTab::factory()->create(['name' => 'Round 3 Users']);
    $inTab = ContactSubmission::factory()->create(['contact_submission_tab_id' => $tab->id]);
    $unassigned = ContactSubmission::factory()->create();

    Livewire::test(ManageContactSubmissions::class, ['activeTab' => 'tab-'.$tab->id])
        ->assertCanSeeTableRecords([$inTab])
        ->assertCanNotSeeTableRecords([$unassigned]);

    Livewire::test(ManageContactSubmissions::class, ['activeTab' => 'unassigned'])
        ->assertCanSeeTableRecords([$unassigned])
        ->assertCanNotSeeTableRecords([$inTab]);

    Livewire::test(ManageContactSubmissions::class)
        ->assertCanSeeTableRecords([$inTab, $unassigned]);
});

it('moves selected submissions into a tab and back out again', function () {
    $this->actingAs(User::factory()->create());

    $tab = ContactSubmissionTab::factory()->create(['name' => 'Round 3 Users']);
    $submissions = ContactSubmission::factory()->count(2)->create();

    Livewire::test(ManageContactSubmissions::class)
        ->selectTableRecords($submissions->modelKeys())
        ->callTableBulkAction('moveToTab', $submissions, ['contact_submission_tab_id' => $tab->id]);

    expect(ContactSubmission::query()->where('contact_submission_tab_id', $tab->id)->count())->toBe(2);

    Livewire::test(ManageContactSubmissions::class)
        ->selectTableRecords($submissions->modelKeys())
        ->callTableBulkAction('moveToTab', $submissions, ['contact_submission_tab_id' => null]);

    expect(ContactSubmission::query()->whereNull('contact_submission_tab_id')->count())->toBe(2);
});

it('creates and renames tabs from the tabs resource', function () {
    $this->actingAs(User::factory()->create());

    Livewire::test(ManageContactSubmissionTabs::class)
        ->callAction('create', ['name' => 'Round 3 Users']);

    $tab = ContactSubmissionTab::query()->firstOrFail();
    expect($tab->name)->toBe('Round 3 Users');

    Livewire::test(ManageContactSubmissionTabs::class)
        ->callAction(TestAction::make('edit')->table($tab), ['name' => 'Round 4 Users']);

    expect($tab->fresh()->name)->toBe('Round 4 Users');
});

it('keeps submissions when their tab is deleted', function () {
    $tab = ContactSubmissionTab::factory()->create();
    $submission = ContactSubmission::factory()->create(['contact_submission_tab_id' => $tab->id]);

    $tab->delete();

    expect($submission->fresh())->not->toBeNull()
        ->and($submission->fresh()->contact_submission_tab_id)->toBeNull();
});

it('exports contact submissions to an excel file', function () {
    $this->actingAs(User::factory()->create());

    ContactSubmission::factory()->create(['name' => 'Exported Person']);

    Livewire::test(ManageContactSubmissions::class)
        ->callAction(TestAction::make('exportExcel')->table())
        ->assertFileDownloaded();
});
