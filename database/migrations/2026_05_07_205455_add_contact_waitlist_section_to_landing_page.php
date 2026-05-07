<?php

use App\Models\LandingPageSection;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (LandingPageSection::query()->where('key', 'contact_waitlist')->exists()) {
            return;
        }

        LandingPageSection::query()->create([
            'key' => 'contact_waitlist',
            'name' => 'Contact / waitlist block',
            'content' => [
                'visible' => true,
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        LandingPageSection::query()->where('key', 'contact_waitlist')->delete();
    }
};
