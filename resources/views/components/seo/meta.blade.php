{{-- PWA meta tags --}}
@laravelPWA

{{-- Preconnect to critical third-party origins --}}
<link rel="preconnect" href="https://fonts.bunny.net" crossorigin>

{{-- DNS prefetch for less critical origins --}}
<link rel="dns-prefetch" href="https://fonts.bunny.net">

{{-- Preload critical fonts for faster LCP --}}
<link
    rel="preload"
    href="https://fonts.bunny.net/instrument-sans/files/instrument-sans-latin-400-normal.woff2"
    as="font"
    type="font/woff2"
    crossorigin
>

{{-- Theme color for better mobile experience --}}
<meta name="theme-color" content="#00CE7C" media="(prefers-color-scheme: light)">
<meta name="theme-color" content="#001F60" media="(prefers-color-scheme: dark)">

{{-- Mobile optimizations --}}
<meta name="mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="default">
