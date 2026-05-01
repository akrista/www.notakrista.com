<!doctype html>
<html
    lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr' }}"
    class="h-full"
>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="color-scheme" content="light dark">
    <meta name="robots" content="noindex, nofollow">

    <title>{{ __('errors.offline.title') }} - {{ config('app.name') }}</title>

    <!-- Fonts with fallback -->
    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700" rel="stylesheet">

    <!-- Styles -->
    @vite(['resources/css/app.css'])

    {{-- Inline critical CSS fallback in case Vite fails (likely when offline) --}}
    <style>
        /* Core fallback styles for offline viewing - using navy-tinted neutrals */
        :root {
            --color-brand-primary: #00CE7C;
            --color-brand-secondary: #001F60;
            --color-focus-ring: #00CE7C;
            /* Light mode tinted grays (navy hue) */
            --color-surface-light: #fafbfc;
            --color-text-primary-light: #1a1f2e;
            --color-text-secondary-light: #4a5568;
            --color-text-muted-light: #718096;
            --color-skeleton-light: #edf0f4;
        }

        /* Fallback font stack */
        body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
        }

        /* Light mode fallback */
        body {
            background-color: var(--color-surface-light);
            color: var(--color-text-primary-light);
        }

        /* Fallback dark mode colors (navy-tinted) */
        @media (prefers-color-scheme: dark) {
            :root:not(.light) {
                --color-surface-dark: #0f1219;
                --color-text-primary-dark: #f0f2f5;
                --color-text-secondary-dark: #a0aec0;
                --color-text-muted-dark: #718096;
                --color-skeleton-dark: #1a202c;
            }

            :root:not(.light) body {
                background-color: var(--color-surface-dark);
                color: var(--color-text-primary-dark);
            }
        }

        /* Reduced motion */
        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after {
                animation-duration: 0.01ms !important;
                animation-iteration-count: 1 !important;
                transition-duration: 0.01ms !important;
            }
        }

        /* Skip link */
        .skip-link {
            position: absolute;
            left: -9999px;
            top: auto;
            width: 1px;
            height: 1px;
            overflow: hidden;
        }

        .skip-link:focus {
            position: fixed;
            top: 1rem;
            left: 1rem;
            width: auto;
            height: auto;
            padding: 0.75rem 1.5rem;
            background: var(--color-brand-primary);
            color: var(--color-text-primary-light);
            border-radius: 0.5rem;
            z-index: 9999;
            font-weight: 600;
        }
    </style>

    <script>
        // Sync theme with system/user preference
        try {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        } catch (e) {
            // localStorage may be unavailable
        }
    </script>
</head>

<body class="h-full font-sans antialiased surface-light text-primary-surface">
    {{-- Skip link for keyboard users --}}
    <a href="#main-content" class="skip-link">
        {{ __('app.skip_to_content') }}
    </a>

    <main id="main-content" class="flex min-h-full flex-col items-center justify-center px-6 py-12">
        <div class="w-full max-w-md text-center">
            {{-- Offline Icon --}}
            <div class="mx-auto mb-8 flex h-24 w-24 items-center justify-center rounded-full bg-subtle" aria-hidden="true">
                <svg
                    class="h-12 w-12 text-muted-surface"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="1.5"
                >
                    {{-- WiFi off icon --}}
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M8.288 15.038a5.25 5.25 0 017.424 0M5.106 11.856c3.807-3.808 9.98-3.808 13.788 0M1.924 8.674c5.565-5.565 14.587-5.565 20.152 0M12.53 18.22l-.53.53-.53-.53a.75.75 0 011.06 0z"
                    />
                    {{-- Diagonal line through --}}
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M3 3l18 18"
                    />
                </svg>
            </div>

            {{-- Title --}}
            <h1 class="text-3xl font-bold tracking-tight text-primary-surface sm:text-4xl">
                {{ __('errors.offline.title') }}
            </h1>

            {{-- Accent Line --}}
            <div
                class="mx-auto my-6 h-1 w-16 rounded-full bg-[--color-brand-primary]"
                aria-hidden="true"
            ></div>

            {{-- Message --}}
            <p class="text-base leading-relaxed text-secondary-surface sm:text-lg">
                {{ __('errors.offline.message') }}
            </p>

            {{-- Tip --}}
            <p class="mt-4 text-sm text-muted-surface">
                {{ __('errors.offline.tip') }}
            </p>

            {{-- Actions --}}
            <div class="mt-8 flex flex-col items-center gap-4 sm:flex-row sm:justify-center">
                {{-- Primary: Retry --}}
                <button
                    type="button"
                    onclick="window.location.reload()"
                    class="btn-primary w-full gap-2 sm:w-auto"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    {{ __('errors.offline.retry') }}
                </button>

                {{-- Secondary: Go Home --}}
                <a
                    href="{{ url('/') }}"
                    class="btn-outline-surface w-full sm:w-auto"
                >
                    {{ __('errors.go_home') }}
                </a>
            </div>

            {{-- Connection status indicator --}}
            <div
                id="connection-status"
                class="mt-8 inline-flex items-center gap-2 rounded-full px-4 py-2 text-sm"
                role="status"
                aria-live="polite"
            >
                {{-- Status will be updated by JavaScript --}}
                <span id="status-dot" class="h-2 w-2 rounded-full bg-red-500" aria-hidden="true"></span>
                <span id="status-text" class="text-secondary-surface">{{ __('errors.offline.title') }}</span>
            </div>
        </div>
    </main>

    {{-- Connection monitoring script --}}
    <script>
        (function() {
            var statusDot = document.getElementById('status-dot');
            var statusText = document.getElementById('status-text');

            function updateConnectionStatus() {
                if (navigator.onLine) {
                    statusDot.classList.remove('bg-red-500');
                    statusDot.classList.add('bg-green-500');
                    statusText.textContent = '{{ __("app.online_message") }}';

                    // Auto-reload after a short delay when connection is restored
                    setTimeout(function() {
                        if (navigator.onLine) {
                            window.location.reload();
                        }
                    }, 1500);
                } else {
                    statusDot.classList.remove('bg-green-500');
                    statusDot.classList.add('bg-red-500');
                    statusText.textContent = '{{ __("errors.offline.title") }}';
                }
            }

            window.addEventListener('online', updateConnectionStatus);
            window.addEventListener('offline', updateConnectionStatus);

            // Initial check
            updateConnectionStatus();
        })();
    </script>
</body>

</html>
