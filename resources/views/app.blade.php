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

        <title inertia>{{ $seo['site_title'] ?? config('app.name', 'Laravel') }}</title>

        {{-- Basic SEO Meta Tags (Server-Side for Crawlers) --}}
        @if(!empty($seo['site_description']))
            <meta name="description" content="{{ $seo['site_description'] }}">
        @endif
        @if(!empty($seo['keywords']))
            <meta name="keywords" content="{{ $seo['keywords'] }}">
        @endif
        @if(!empty($seo['author']))
            <meta name="author" content="{{ $seo['author'] }}">
        @endif
        <meta name="robots" content="index, follow">

        {{-- Canonical URL --}}
        <link rel="canonical" href="{{ $seo['canonical_url'] ?? $appUrl ?? config('app.url') }}">

        {{-- Open Graph Meta Tags (Server-Side for Social Media Crawlers) --}}
        <meta property="og:type" content="website">
        <meta property="og:locale" content="ar_SA">
        <meta property="og:title" content="{{ $seo['og_title'] ?? $seo['site_title'] ?? config('app.name') }}">
        @if(!empty($seo['og_description']) || !empty($seo['site_description']))
            <meta property="og:description" content="{{ $seo['og_description'] ?? $seo['site_description'] }}">
        @endif
        <meta property="og:url" content="{{ $seo['canonical_url'] ?? $appUrl ?? config('app.url') }}">
        @if(!empty($seo['og_image']))
            <meta property="og:image" content="{{ $seo['og_image'] }}">
            <meta property="og:image:width" content="1200">
            <meta property="og:image:height" content="630">
        @endif
        @if(!empty($seo['organization_name']))
            <meta property="og:site_name" content="{{ $seo['organization_name'] }}">
        @endif

        {{-- Twitter Card Meta Tags --}}
        <meta name="twitter:card" content="{{ $seo['twitter_card_type'] ?? 'summary_large_image' }}">
        <meta name="twitter:title" content="{{ $seo['og_title'] ?? $seo['site_title'] ?? config('app.name') }}">
        @if(!empty($seo['og_description']) || !empty($seo['site_description']))
            <meta name="twitter:description" content="{{ $seo['og_description'] ?? $seo['site_description'] }}">
        @endif
        @if(!empty($seo['og_image']))
            <meta name="twitter:image" content="{{ $seo['og_image'] }}">
        @endif
        @if(!empty($seo['twitter_handle']))
            <meta name="twitter:site" content="{{ '@' . ltrim($seo['twitter_handle'], '@') }}">
        @endif

        {{-- Google Site Verification --}}
        @if(!empty($seo['google_site_verification']))
            <meta name="google-site-verification" content="{{ $seo['google_site_verification'] }}">
        @endif

        <link rel="icon" href="/favicon.png" type="image/png">
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
