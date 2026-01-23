<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Admin user for Filament
        User::create([
            'name' => 'Baseet Admin',
            'email' => 'mbaseet95@gmail.com',
            'password' => Hash::make('Loza@464**##'),
            'email_verified_at' => now(),
        ]);

        // Landing page sections
        $this->call(LandingPageSectionSeeder::class);
    }
}
