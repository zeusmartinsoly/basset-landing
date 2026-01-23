<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LandingPageSection extends Model
{
    /** @use HasFactory<\Database\Factories\LandingPageSectionFactory> */
    use HasFactory;

    /** @var list<string> */
    protected $fillable = [
        'key',
        'name',
        'content',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'content' => 'array',
        ];
    }

    /**
     * Get a section by its key.
     */
    public static function getByKey(string $key): ?self
    {
        return self::where('key', $key)->first();
    }

    /**
     * Get the content of a section by its key.
     *
     * @return array<string, mixed>|null
     */
    public static function getContent(string $key): ?array
    {
        $section = self::getByKey($key);

        return $section?->content;
    }

    /**
     * Get all sections as a keyed array.
     *
     * @return array<string, array<string, mixed>>
     */
    public static function getAllSections(): array
    {
        return self::all()->pluck('content', 'key')->toArray();
    }
}
