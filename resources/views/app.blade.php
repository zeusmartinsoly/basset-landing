<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" @class(['dark' => ($appearance ?? 'system') == 'dark'])>
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        {{-- Inline script to detect system dark mode preference and apply it immediately --}}
        <script>
            (function() {
                const appearance = '{{ $appearance ?? "system" }}';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        {{-- Inline style to set the HTML background color based on our theme in app.css --}}
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <title inertia>{{ config('app.name', 'Laravel') }}</title>

        <link rel="icon" href="/favicon.ico" sizes="any">
        <link rel="icon" href="/favicon.svg" type="image/svg+xml">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        {{-- Preload critical fonts for faster loading --}}
        @production
            @php
                $manifest = json_decode(file_get_contents(public_path('build/manifest.json')), true);
                $iranSansRegular = $manifest['resources/fonts/iran-sans/IRANSansX-RegularD4.ttf']['file'] ?? null;
                $iranSansMedium = $manifest['resources/fonts/iran-sans/IRANSansX-MediumD4.ttf']['file'] ?? null;
                $iranSansBold = $manifest['resources/fonts/iran-sans/IRANSansX-BoldD4.ttf']['file'] ?? null;
                $terminaRegular = $manifest['resources/fonts/termina/TerminaTest-Regular.otf']['file'] ?? null;
                $terminaBold = $manifest['resources/fonts/termina/TerminaTest-Bold.otf']['file'] ?? null;
            @endphp
            @if($iranSansRegular)
                <link rel="preload" href="/build/{{ $iranSansRegular }}" as="font" type="font/ttf" crossorigin>
            @endif
            @if($iranSansMedium)
                <link rel="preload" href="/build/{{ $iranSansMedium }}" as="font" type="font/ttf" crossorigin>
            @endif
            @if($iranSansBold)
                <link rel="preload" href="/build/{{ $iranSansBold }}" as="font" type="font/ttf" crossorigin>
            @endif
            @if($terminaRegular)
                <link rel="preload" href="/build/{{ $terminaRegular }}" as="font" type="font/opentype" crossorigin>
            @endif
            @if($terminaBold)
                <link rel="preload" href="/build/{{ $terminaBold }}" as="font" type="font/opentype" crossorigin>
            @endif
        @else
            <link rel="preload" href="/fonts/iran-sans/IRANSansX-RegularD4.ttf" as="font" type="font/ttf" crossorigin>
            <link rel="preload" href="/fonts/iran-sans/IRANSansX-MediumD4.ttf" as="font" type="font/ttf" crossorigin>
            <link rel="preload" href="/fonts/iran-sans/IRANSansX-BoldD4.ttf" as="font" type="font/ttf" crossorigin>
            <link rel="preload" href="/fonts/termina/TerminaTest-Regular.otf" as="font" type="font/opentype" crossorigin>
            <link rel="preload" href="/fonts/termina/TerminaTest-Bold.otf" as="font" type="font/opentype" crossorigin>
        @endproduction

        @viteReactRefresh
        @vite(['resources/css/app.css', 'resources/js/app.tsx', "resources/js/pages/{$page['component']}.tsx"])
        @inertiaHead
    </head>
    <body class="font-sans antialiased">
        @inertia
    </body>
</html>
