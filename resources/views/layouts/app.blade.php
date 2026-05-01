<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    @hasSection('title')
        <title>@yield('title')</title>
    @else
        <title>{{ __('welcome.title') }}</title>
    @endif

    <link rel="icon" href="/favicon.ico" sizes="any">
    <link rel="icon" href="/favicon.png" type="image/png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link rel="preload" href="https://fonts.bunny.net/bricolage-grotesque/files/bricolage-grotesque-latin-600-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="https://fonts.bunny.net/bricolage-grotesque/files/bricolage-grotesque-latin-700-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="https://fonts.bunny.net/bricolage-grotesque/files/bricolage-grotesque-latin-800-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="https://fonts.bunny.net/albert-sans/files/albert-sans-latin-400-normal.woff2" as="font" type="font/woff2" crossorigin>
    <link href="https://fonts.bunny.net/css?family=bricolage-grotesque:600,700,800|albert-sans:400,500,600&display=swap"
        rel="stylesheet" />

    @laravelPWA
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app-islands.js'])

    @stack('head')
</head>

<body class="bg-surface-light dark:bg-surface-dark text-primary-surface flex flex-col min-h-screen font-body antialiased">

    <a href="#main" class="skip-link">{{ __('app.skip_to_content') }}</a>

    <x-layout.header />

    <main id="main" class="flex-1">
        @yield('content', $slot ?? '')
    </main>

    <x-layout.footer />

</body>

</html>
