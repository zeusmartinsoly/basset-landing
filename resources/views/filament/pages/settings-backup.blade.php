<x-filament-panels::page>
    <x-filament::section
        icon="heroicon-o-archive-box"
        heading="Stored backups"
        description="Backups include every setting (SEO, Search Console, Tag Manager, Analytics, Clarity, Meta & TikTok pixels, cookie consent, custom scripts, robots.txt) and all redirects. Uploaded images are not included; their file paths are."
    >
        @php($backups = $this->getBackups())

        @if ($backups === [])
            <p style="font-size: .875rem; opacity: .7;">No backups yet. Use "Create backup" to save the current settings.</p>
        @else
            <div style="display: flex; flex-direction: column;">
                @foreach ($backups as $backup)
                    <div style="display: flex; flex-wrap: wrap; align-items: center; justify-content: space-between; gap: .75rem; padding: .75rem 0; border-top: {{ $loop->first ? '0' : '1px solid rgba(127, 127, 127, .2)' }};" wire:key="backup-{{ $backup['name'] }}">
                        <div style="min-width: 0;">
                            <p style="font-family: ui-monospace, monospace; font-size: .875rem; overflow-wrap: anywhere;">{{ $backup['name'] }}</p>
                            <p style="font-size: .75rem; opacity: .65;">{{ $backup['created_at'] }} · {{ number_format($backup['size'] / 1024, 1) }} KB</p>
                        </div>
                        <div style="display: flex; align-items: center; gap: 1rem;">
                            {{ ($this->downloadAction)(['name' => $backup['name']]) }}
                            {{ ($this->restoreAction)(['name' => $backup['name']]) }}
                            {{ ($this->deleteAction)(['name' => $backup['name']]) }}
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </x-filament::section>

</x-filament-panels::page>
