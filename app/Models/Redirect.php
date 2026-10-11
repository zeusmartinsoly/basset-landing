<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Redirect extends Model
{
    /** @use HasFactory<\Database\Factories\RedirectFactory> */
    use HasFactory;

    public const CACHE_KEY = 'redirects.active';

    /** @var list<string> */
    public const PROTECTED_PREFIXES = ['/admin', '/livewire', '/filament', '/storage', '/build', '/up'];

    protected $fillable = [
        'from_path',
        'to_url',
        'status_code',
        'is_active',
        'preserve_query_string',
        'hits',
        'last_hit_at',
        'notes',
    ];

    protected static function booted(): void
    {
        $flush = static fn () => Cache::forget(self::CACHE_KEY);

        static::saved($flush);
        static::deleted($flush);
    }

    /** @return array<string, string> */
    protected function casts(): array
    {
        return [
            'status_code' => 'integer',
            'is_active' => 'boolean',
            'preserve_query_string' => 'boolean',
            'hits' => 'integer',
            'last_hit_at' => 'datetime',
        ];
    }

    public static function normalizePath(string $path): string
    {
        $path = '/'.ltrim(strtolower(trim($path)), '/');

        return $path === '/' ? $path : rtrim($path, '/');
    }

    public static function isProtectedPath(string $path): bool
    {
        $path = self::normalizePath($path);

        foreach (self::PROTECTED_PREFIXES as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix.'/')) {
                return true;
            }
        }

        return false;
    }

    /** @return array<string, array{id: int, to_url: string, status_code: int, preserve_query_string: bool}> */
    public static function activeMap(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, fn (): array => self::query()
            ->where('is_active', true)
            ->get(['id', 'from_path', 'to_url', 'status_code', 'preserve_query_string'])
            ->mapWithKeys(fn (self $redirect): array => [$redirect->from_path => [
                'id' => $redirect->id,
                'to_url' => $redirect->to_url,
                'status_code' => $redirect->status_code,
                'preserve_query_string' => $redirect->preserve_query_string,
            ]])
            ->all());
    }

    protected function setFromPathAttribute(string $value): void
    {
        $this->attributes['from_path'] = self::normalizePath($value);
    }
}
