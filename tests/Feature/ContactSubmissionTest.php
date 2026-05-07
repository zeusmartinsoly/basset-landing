<?php

use App\Filament\Resources\ContactSubmissions\Pages\ManageContactSubmissions;
use App\Mail\ContactSubmissionSubmitted;
use App\Models\ContactSubmission;
use App\Models\User;
use Database\Seeders\LandingPageSectionSeeder;
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
