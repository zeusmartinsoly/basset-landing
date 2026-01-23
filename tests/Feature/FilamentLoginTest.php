<?php

use App\Models\User;
use Filament\Auth\Pages\Login;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Livewire;

it('can access filament login page', function () {
    $response = $this->get('/admin/login');

    $response->assertStatus(200);
});

it('can login via filament livewire component', function () {
    $user = User::create([
        'name' => 'Baseet Admin',
        'email' => 'livewire@test.com',
        'password' => Hash::make('testpassword'),
        'email_verified_at' => now(),
    ]);

    Livewire::test(Login::class)
        ->fillForm([
            'email' => 'livewire@test.com',
            'password' => 'testpassword',
        ])
        ->call('authenticate')
        ->assertHasNoFormErrors();

    $this->assertAuthenticated();
});

it('password hash works correctly', function () {
    $password = 'Loza@464**##';
    $hashedPassword = Hash::make($password);

    expect(Hash::check($password, $hashedPassword))->toBeTrue();
    expect(Hash::check('wrongpassword', $hashedPassword))->toBeFalse();
});

it('can authenticate user with correct credentials', function () {
    $user = User::create([
        'name' => 'Baseet Admin',
        'email' => 'mbaseet95@gmail.com',
        'password' => Hash::make('Loza@464**##'),
        'email_verified_at' => now(),
    ]);

    // Verify the password hash is correct
    expect(Hash::check('Loza@464**##', $user->password))->toBeTrue();

    // Test authentication using Auth facade
    $authenticated = Auth::attempt([
        'email' => 'mbaseet95@gmail.com',
        'password' => 'Loza@464**##',
    ]);

    expect($authenticated)->toBeTrue();
    expect(Auth::user()->email)->toBe('mbaseet95@gmail.com');
});

it('cannot authenticate with wrong password', function () {
    User::create([
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => Hash::make('correctpassword'),
        'email_verified_at' => now(),
    ]);

    $authenticated = Auth::attempt([
        'email' => 'test@example.com',
        'password' => 'wrongpassword',
    ]);

    expect($authenticated)->toBeFalse();
});

it('authenticated user can access filament dashboard', function () {
    $user = User::create([
        'name' => 'Baseet Admin',
        'email' => 'admin@example.com',
        'password' => Hash::make('password123'),
        'email_verified_at' => now(),
    ]);

    $response = $this->actingAs($user)->get('/admin');

    $response->assertStatus(200);
});
