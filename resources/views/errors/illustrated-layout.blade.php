<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    dir="{{ in_array(app()->getLocale(), ['ar', 'he', 'fa', 'ur']) ? 'rtl' : 'ltr' }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="color-scheme" content="light dark">
    <meta name="robots" content="noindex, nofollow">

    <title>@yield('title') - {{ config('app.name') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net" crossorigin>
    <link href="https://fonts.bunny.net/css?family=albert-sans:400,500,600,700" rel="stylesheet">

    @vite(['resources/css/app.css'])

    <style>
        .fallback-hidden {
            display: none;
        }

        .js-loaded .fallback-hidden {
            display: block;
        }

        .js-loaded .error-code {
            animation: fadeSlideUp 400ms var(--ease-out-expo) both;
        }

        .js-loaded .error-divider {
            animation: fadeSlideUp 400ms var(--ease-out-expo) 60ms both;
        }

        .js-loaded .error-message {
            animation: fadeSlideUp 400ms var(--ease-out-expo) 120ms both;
        }

        .js-loaded .error-actions {
            animation: fadeSlideUp 400ms var(--ease-out-expo) 180ms both;
        }

        .js-loaded .error-illustration {
            animation: fadeIn 600ms var(--ease-out-expo) 200ms both;
        }

        @keyframes fadeSlideUp {
            from {
                opacity: 0;
                transform: translateY(12px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        @media (prefers-reduced-motion: reduce) {
            .js-loaded .error-code,
            .js-loaded .error-divider,
            .js-loaded .error-message,
            .js-loaded .error-actions,
            .js-loaded .error-illustration {
                animation: none;
                opacity: 1;
                transform: none;
            }
        }

        @supports not (background: oklch(0% 0 0)) {
            :root {
                --color-brand-primary: #00CE7C;
                --color-brand-secondary: #001F60;
            }
        }
    </style>

    <script>
        document.documentElement.classList.add('js-loaded');

        try {
            if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        } catch (e) {
            console.warn('Theme detection failed:', e);
        }
    </script>
</head>

<body class="h-full font-sans antialiased surface-light text-primary-surface">
    <a href="#main-content" class="skip-link">
        {{ __('app.skip_to_content') }}
    </a>

    <main id="main-content" class="flex min-h-full flex-col md:flex-row">
        <div class="flex w-full shrink-0 items-center justify-center px-6 py-16 sm:py-20 md:min-h-full md:w-1/2 md:px-12 lg:px-16">
            <div class="w-full max-w-md">
                <h1 class="error-code text-5xl font-bold tracking-tight text-primary-surface break-word-safe sm:text-6xl md:text-7xl lg:text-8xl"
                    aria-label="{{ __('Error') }} @yield('code')">
                    @yield('code', __('Oh no'))
                </h1>

                <div class="error-divider my-5 h-1 w-16 rounded-full bg-[--color-brand-primary] sm:my-6 sm:w-20 md:my-8 md:w-24" aria-hidden="true">
                </div>

                <p class="error-message text-base font-normal leading-relaxed text-secondary-surface break-word-safe sm:text-lg md:text-xl lg:text-2xl">
                    @yield('message')
                </p>

                <div class="error-actions mt-6 flex flex-wrap gap-3 sm:mt-8 md:mt-10">
                    <a href="{{ app('router')->has('home') ? route('home') : url('/') }}" class="btn-outline-surface">
                        {{ __('errors.go_home') }}
                    </a>

                    <button type="button" onclick="history.back()" class="btn-ghost-surface">
                        {{ __('errors.back') }}
                    </button>
                </div>

                <div id="offline-indicator"
                    class="error-illustration mt-6 hidden rounded-lg border border-[oklch(80%_0.14_90)] bg-[oklch(97%_0.03_90)] p-4 text-sm text-[oklch(35%_0.12_90)] dark:border-[oklch(50%_0.10_90)] dark:bg-[oklch(25%_0.06_90)] dark:text-[oklch(85%_0.08_90)]"
                    role="alert">
                    <p class="flex items-center gap-2">
                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24"
                            aria-hidden="true">
                            <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                        <span>{{ __('app.offline_message') }}</span>
                    </p>
                </div>
            </div>
        </div>

        <div class="error-illustration relative flex min-h-48 w-full flex-1 items-center justify-center bg-subtle sm:min-h-64 md:min-h-full md:w-1/2"
            role="presentation" aria-hidden="true">
            @yield('image')
        </div>
    </main>

    <script>
        (function () {
            var indicator = document.getElementById('offline-indicator');
            if (!indicator) return;

            function updateOnlineStatus() {
                if (navigator.onLine) {
                    indicator.classList.add('hidden');
                } else {
                    indicator.classList.remove('hidden');
                }
            }

            window.addEventListener('online', updateOnlineStatus);
            window.addEventListener('offline', updateOnlineStatus);
            updateOnlineStatus();
        })();
    </script>
</body>

</html>