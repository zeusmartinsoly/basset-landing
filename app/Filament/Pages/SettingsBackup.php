<?php

namespace App\Filament\Pages;

use App\Models\Redirect;
use App\Models\SeoSetting;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Symfony\Component\HttpFoundation\StreamedResponse;
use UnitEnum;

class SettingsBackup extends Page
{
    public const FORMAT = 'bassetlanding-settings';

    public const FORMAT_VERSION = 1;

    public const DIRECTORY = 'settings-backups';

    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-archive-box-arrow-down';

    protected string $view = 'filament.pages.settings-backup';

    protected static ?string $navigationLabel = 'Backup & Restore';

    protected static ?string $title = 'Settings Backup & Restore';

    protected static ?string $slug = 'settings-backup';

    protected static string|UnitEnum|null $navigationGroup = 'Settings';

    protected static ?int $navigationSort = 52;

    /**
     * Stored backups, newest first.
     *
     * @return list<array{name: string, size: int, created_at: string}>
     */
    public function getBackups(): array
    {
        return collect(Storage::disk('local')->files(self::DIRECTORY))
            ->filter(fn (string $path): bool => str_ends_with($path, '.json'))
            ->map(fn (string $path): array => [
                'name' => basename($path),
                'size' => Storage::disk('local')->size($path),
                'created_at' => date('Y-m-d H:i:s', Storage::disk('local')->lastModified($path)),
            ])
            ->sortByDesc('name')
            ->values()
            ->all();
    }

    /**
     * Snapshot of all settings and redirects.
     *
     * @return array{format: string, version: int, exported_at: string, settings: array<string, mixed>, redirects: list<array<string, mixed>>}
     */
    public static function snapshot(): array
    {
        SeoSetting::getSettings();

        $settings = SeoSetting::query()->firstOrFail();

        return [
            'format' => self::FORMAT,
            'version' => self::FORMAT_VERSION,
            'exported_at' => now()->toIso8601String(),
            'settings' => Arr::only($settings->attributesToArray(), $settings->getFillable()),
            'redirects' => Redirect::query()
                ->orderBy('from_path')
                ->get()
                ->map(fn (Redirect $redirect): array => $redirect->only(['from_path', 'to_url', 'status_code', 'is_active', 'preserve_query_string', 'notes']))
                ->all(),
        ];
    }

    public static function storeBackup(string $label = ''): string
    {
        $label = Str::slug($label);
        $name = 'backup-'.now()->format('Y-m-d_His').($label !== '' ? "-{$label}" : '').'.json';

        Storage::disk('local')->put(self::DIRECTORY.'/'.$name, json_encode(self::snapshot(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));

        return $name;
    }

    /**
     * Restore settings and/or redirects from a decoded backup.
     *
     * @param  array<string, mixed>  $backup
     *
     * @throws \InvalidArgumentException
     */
    public static function restore(array $backup, bool $restoreSettings = true, bool $restoreRedirects = true): void
    {
        if (($backup['format'] ?? null) !== self::FORMAT || ! is_int($backup['version'] ?? null) || $backup['version'] > self::FORMAT_VERSION) {
            throw new \InvalidArgumentException('This file is not a settings backup from this site.');
        }

        if (! is_array($backup['settings'] ?? null) || ! is_array($backup['redirects'] ?? null)) {
            throw new \InvalidArgumentException('The backup file is incomplete.');
        }

        self::storeBackup('auto-before-restore');

        DB::transaction(function () use ($backup, $restoreSettings, $restoreRedirects): void {
            if ($restoreSettings) {
                SeoSetting::getSettings();

                $settings = SeoSetting::query()->firstOrFail();
                $settings->update(Arr::only($backup['settings'], $settings->getFillable()));
            }

            if ($restoreRedirects) {
                Redirect::query()->delete();

                collect($backup['redirects'])
                    ->filter(fn (mixed $redirect): bool => is_array($redirect)
                        && is_string($redirect['from_path'] ?? null)
                        && is_string($redirect['to_url'] ?? null)
                        && ! Redirect::isProtectedPath($redirect['from_path']))
                    ->unique(fn (array $redirect): string => Redirect::normalizePath($redirect['from_path']))
                    ->each(fn (array $redirect): Redirect => Redirect::query()->create([
                        'from_path' => $redirect['from_path'],
                        'to_url' => $redirect['to_url'],
                        'status_code' => in_array((int) ($redirect['status_code'] ?? 301), [301, 302, 307, 308], true) ? (int) $redirect['status_code'] : 301,
                        'is_active' => (bool) ($redirect['is_active'] ?? true),
                        'preserve_query_string' => (bool) ($redirect['preserve_query_string'] ?? true),
                        'notes' => is_string($redirect['notes'] ?? null) ? $redirect['notes'] : null,
                    ]));
            }
        });

        Cache::forget(Redirect::CACHE_KEY);

        SeoSetting::writeRobotsTxtFile();
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('createBackup')
                ->label('Create backup')
                ->icon('heroicon-o-plus')
                ->schema([
                    TextInput::make('label')->label('Label (optional)')->maxLength(40)->placeholder('before-campaign'),
                ])
                ->action(function (array $data): void {
                    $name = self::storeBackup((string) ($data['label'] ?? ''));

                    Notification::make()->title("Backup {$name} created")->success()->send();
                }),

            Action::make('downloadCurrent')
                ->label('Download current settings')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(fn (): StreamedResponse => response()->streamDownload(
                    function (): void {
                        echo json_encode(self::snapshot(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                    },
                    'settings-'.now()->format('Y-m-d_His').'.json',
                    ['Content-Type' => 'application/json'],
                )),

            Action::make('importBackup')
                ->label('Import backup file')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->modalDescription('A safety backup of the current settings is created automatically before importing.')
                ->schema([
                    FileUpload::make('file')
                        ->label('Backup file (.json)')
                        ->acceptedFileTypes(['application/json', 'text/plain'])
                        ->maxSize(2048)
                        ->storeFiles(false)
                        ->required(),
                    Toggle::make('restore_settings')->label('Restore settings (SEO, tracking, pixels, consent, scripts, robots.txt)')->default(true),
                    Toggle::make('restore_redirects')->label('Replace all redirects')->default(true),
                ])
                ->action(function (array $data): void {
                    /** @var TemporaryUploadedFile $file */
                    $file = $data['file'];

                    $this->restoreFromJson(
                        (string) file_get_contents($file->getRealPath()),
                        (bool) ($data['restore_settings'] ?? true),
                        (bool) ($data['restore_redirects'] ?? true),
                    );
                }),
        ];
    }

    public function downloadAction(): Action
    {
        return Action::make('download')
            ->label('Download')
            ->icon('heroicon-o-arrow-down-tray')
            ->link()
            ->action(function (array $arguments): ?StreamedResponse {
                $path = $this->backupPath($arguments['name'] ?? '');

                return $path === null ? null : Storage::disk('local')->download($path);
            });
    }

    public function restoreAction(): Action
    {
        return Action::make('restore')
            ->label('Restore')
            ->icon('heroicon-o-arrow-uturn-left')
            ->color('warning')
            ->link()
            ->requiresConfirmation()
            ->modalHeading(fn (array $arguments): string => 'Restore '.($arguments['name'] ?? ''))
            ->modalDescription('Current settings and redirects will be replaced. A safety backup is created first.')
            ->action(function (array $arguments): void {
                $path = $this->backupPath($arguments['name'] ?? '');

                if ($path !== null) {
                    $this->restoreFromJson((string) Storage::disk('local')->get($path));
                }
            });
    }

    public function deleteAction(): Action
    {
        return Action::make('delete')
            ->label('Delete')
            ->icon('heroicon-o-trash')
            ->color('danger')
            ->link()
            ->requiresConfirmation()
            ->action(function (array $arguments): void {
                $path = $this->backupPath($arguments['name'] ?? '');

                if ($path !== null) {
                    Storage::disk('local')->delete($path);

                    Notification::make()->title('Backup deleted')->success()->send();
                }
            });
    }

    protected function restoreFromJson(string $json, bool $restoreSettings = true, bool $restoreRedirects = true): void
    {
        try {
            $backup = json_decode($json, true, 64, JSON_THROW_ON_ERROR);

            self::restore(is_array($backup) ? $backup : [], $restoreSettings, $restoreRedirects);
        } catch (\JsonException|\InvalidArgumentException $exception) {
            Notification::make()
                ->title('Restore failed')
                ->body($exception instanceof \JsonException ? 'The file is not valid JSON.' : $exception->getMessage())
                ->danger()
                ->send();

            return;
        }

        Notification::make()->title('Settings restored')->success()->send();
    }

    /**
     * Resolve a stored backup by file name, notifying when it does not exist.
     */
    protected function backupPath(string $name): ?string
    {
        $path = self::DIRECTORY.'/'.$name;

        if (preg_match('/^backup-[A-Za-z0-9_-]+\.json$/', $name) !== 1 || ! Storage::disk('local')->exists($path)) {
            Notification::make()->title('Backup not found')->danger()->send();

            return null;
        }

        return $path;
    }
}
