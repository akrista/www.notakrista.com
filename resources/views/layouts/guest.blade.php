<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ $title ?? 'Jorge Thomas | Software Engineer' }}</title>

    <link rel="icon" href="/favicon.png" type="image/png">
    <link rel="apple-touch-icon" href="/apple-touch-icon.png">

    @fonts
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @fluxAppearance
    @include('partials.analytics')

    <!-- Alpine.js CDN for standalone static interactivity -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        :root {
            --bg: oklch(0.270 0.005 65);
            --surface: oklch(0.320 0.005 65);
            --surface-raised: oklch(0.380 0.008 60);
            --ink: oklch(0.910 0.030 88);
            --muted: oklch(0.650 0.020 80);
            --primary: oklch(0.700 0.180 50);
            --primary-ink: oklch(0.270 0.005 65);
            --accent: oklch(0.760 0.090 145);
            --border: oklch(0.380 0.008 60);

            --yellow: oklch(0.810 0.150 80);
            --red: oklch(0.640 0.220 25);
            --blue: oklch(0.680 0.050 200);
        }

        .light {
            --bg: oklch(0.955 0.030 95);
            --surface: oklch(0.905 0.030 90);
            --surface-raised: oklch(0.905 0.030 90);
            --ink: oklch(0.230 0.005 70);
            --muted: oklch(0.530 0.013 65);
            --primary: oklch(0.490 0.180 45);
            --primary-ink: oklch(0.955 0.030 95);
            --accent: oklch(0.510 0.080 150);
            --border: oklch(0.810 0.040 85);

            --yellow: oklch(0.620 0.140 70);
            --red: oklch(0.380 0.180 28);
            --blue: oklch(0.450 0.090 230);
        }

        /* Custom Transitions */
        .transition-theme {
            transition: background-color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                border-color 0.3s cubic-bezier(0.4, 0, 0.2, 1),
                fill 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        /* Typographic Elements from DESIGN.md */
        .text-display {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 600;
            line-height: 1.05;
            letter-spacing: -0.025em;
            text-wrap: balance;
        }

        .text-headline {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            font-size: clamp(1.75rem, 3vw, 2.5rem);
            font-weight: 600;
            line-height: 1.15;
            letter-spacing: -0.02em;
        }

        .text-title {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            font-size: 1.25rem;
            font-weight: 500;
            line-height: 1.3;
            letter-spacing: -0.01em;
        }

        .text-body {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            font-size: 1rem;
            font-weight: 400;
            line-height: 1.6;
            letter-spacing: 0;
            max-width: 72ch;
        }

        .text-label {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            font-size: 0.75rem;
            font-weight: 600;
            line-height: 1.2;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }

        .text-mono {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.875rem;
            font-weight: 400;
            line-height: 1.55;
        }

        /* Interactive Elements */
        .focus-ring-signature:focus-visible {
            outline: none;
            box-shadow: 0 0 0 2px var(--bg), 0 0 0 4px var(--primary);
        }

        /* Custom Components */
        .badge-chip {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 9999px;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }

        /* Switcher segmented control */
        .segmented-control {
            display: inline-flex;
            background-color: var(--surface);
            border: 1px solid var(--border);
            border-radius: 9999px;
            padding: 4px;
        }

        .light .segmented-control {
            background-color: var(--bg);
        }

        .segmented-pill {
            padding: 6px 14px;
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border-radius: 9999px;
            color: var(--muted);
            background-color: transparent;
            cursor: pointer;
            border: 1px solid transparent;
            transition: all 0.2s ease;
        }

        .segmented-pill.active {
            color: var(--ink);
            background-color: var(--surface-raised);
            border-color: var(--primary);
        }

        .light .segmented-pill.active {
            background-color: var(--surface);
        }

        /* Card implementation */
        .card-bench {
            background-color: var(--surface);
            border-radius: 12px;
            padding: 24px;
            border: none;
            transition: background-color 0.3s ease, border-color 0.3s ease;
        }

        .light .card-bench {
            background-color: var(--bg);
            border: 1px solid var(--border);
        }

        /* Flat-by-default buttons */
        .button-cta {
            font-family: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            border-radius: 8px;
            padding: 12px 24px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: background-color 0.2s ease, filter 0.2s ease;
        }

        /* Alert / relief box */
        .alert-box {
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 24px;
            background-color: var(--surface);
            transition: background-color 0.3s ease;
        }

        .light .alert-box {
            background-color: var(--bg);
        }

        /* Interactive links */
        .interactive-link {
            transition: color 0.2s ease;
        }

        /* Glitch/Hack Text Effect */
        .glitch-hack-active {
            display: inline-block;
            position: relative;
            animation: glitch-anim 0.22s linear infinite alternate-reverse, glitch-flicker 0.11s steps(2, end) infinite;
            text-shadow:
                -3px 0 var(--red),
                3px 0 var(--blue),
                -1px 1px 0 oklch(0.700 0.180 50 / 0.5),
                0 0 14px oklch(0.700 0.180 50 / 0.35);
            color: var(--primary) !important;
            will-change: clip-path, transform, opacity;
        }

        @keyframes glitch-anim {
            0% {
                clip-path: inset(25% 0 35% 0);
                transform: translate(0, 0) skew(0.6deg);
            }

            14% {
                clip-path: inset(70% 0 10% 0);
                transform: translate(-4px, 1px) skew(-2deg);
            }

            28% {
                clip-path: inset(10% 0 60% 0);
                transform: translate(4px, -1px) skew(2deg);
            }

            42% {
                clip-path: inset(50% 0 20% 0);
                transform: translate(-3px, 0) skew(-1.2deg);
            }

            56% {
                clip-path: inset(30% 0 40% 0);
                transform: translate(3px, 1px) skew(1.5deg);
            }

            70% {
                clip-path: inset(60% 0 15% 0);
                transform: translate(-2px, -1px) skew(-0.8deg);
            }

            84% {
                clip-path: inset(15% 0 55% 0);
                transform: translate(2px, 1px) skew(1deg);
            }

            100% {
                clip-path: inset(40% 0 30% 0);
                transform: translate(0, 0) skew(0);
            }
        }

        @keyframes glitch-flicker {

            0%,
            100% {
                opacity: 1;
            }

            50% {
                opacity: 0.82;
            }
        }

        /* Reduce Motion — keep the effect, drop the motion */
        @media (prefers-reduced-motion: reduce) {

            .transition-theme,
            .transition-all,
            .segmented-pill {
                transition: none !important;
            }

            .glitch-hack-active {
                animation: none !important;
                transition: color 0.15s ease, text-shadow 0.15s ease;
                text-shadow:
                    -2px 0 var(--red),
                    2px 0 var(--blue);
            }
        }
    </style>
</head>

<body class="antialiased">
    <div x-data="{
                mobileMenuOpen: false,
                language: localStorage.getItem('language') || ((navigator.language || navigator.userLanguage || 'en').startsWith('es') ? 'es' : 'en'),
                theme: localStorage.getItem('theme') || 'system',
                systemDark: window.matchMedia('(prefers-color-scheme: dark)').matches,
                tooltipText: '',
                attributes: {{ Js::from(\App\Models\LanguageLine::getActivePhrasesByLocaleForGroup(\App\Models\LanguageLine::HOME_PHRASES_GROUP)) }},
                currentAttribute: '',
                pickRandomAttribute() {
                    const list = this.attributes[this.language] ?? [];
                    if (list.length === 0) {
                        this.currentAttribute = '';
                        return;
                    }
                    this.currentAttribute = list[Math.floor(Math.random() * list.length)];
                },
                init() {
                    this.pickRandomAttribute();
                    setInterval(() => this.pickRandomAttribute(), 2500);

                    window.matchMedia('(prefers-color-scheme: dark)').addEventListener('change', (e) => {
                        this.systemDark = e.matches;
                    });
                },
                getResolvedTheme() {
                    if (this.theme === 'system') {
                        return this.systemDark ? 'dark' : 'light';
                    }
                    return this.theme;
                },
                setTheme(val) {
                    this.theme = val;
                    localStorage.setItem('theme', val);
                },
                cycleTheme() {
                    const order = ['light', 'dark', 'system'];
                    const next = order[(order.indexOf(this.theme) + 1) % order.length];
                    this.setTheme(next);
                },
                setLanguage(val) {
                    this.language = val;
                    localStorage.setItem('language', val);
                },
                isActive(path) {
                    return window.location.pathname === path;
                },
                privacyBannerOpen: !localStorage.getItem('privacy_dismissed'),
                privacyOpen: false,
                dismissPrivacy() {
                    this.privacyBannerOpen = false;
                    this.privacyOpen = false;
                    localStorage.setItem('privacy_dismissed', 'true');
                }
            }" :class="{ 'light': getResolvedTheme() === 'light' }"
        class="min-h-screen bg-[var(--bg)] text-[var(--ink)] transition-theme duration-300 flex flex-col justify-between p-6 md:p-12 font-sans">
        <!-- TOP BAR NAVIGATION -->
        <header class="w-full max-w-6xl mx-auto flex justify-between items-center gap-6 mb-12 sm:mb-16">
            <!-- Brand Mark -->
            <a href="/" class="flex items-center gap-3 group focus-ring-signature rounded-md shrink-0">
                <img src="{{ asset('logo-circle.png') }}"
                    class="size-10 object-contain rounded-full border border-[var(--border)] transition-theme"
                    alt="Logo">
                <div class="flex flex-col items-start">
                    <span class="text-xl font-bold tracking-tight text-[var(--primary)] font-sans">nk.</span>
                    <span class="text-mono text-xs text-[var(--muted)]">notakrista.com</span>
                </div>
            </a>

            <!-- Navigation Links (Desktop only) -->
            <nav
                class="hidden md:flex flex-wrap justify-center items-center gap-4 md:gap-6 text-label">
                <a href="/"
                    :class="isActive('/') ? 'text-[var(--primary)] font-bold border-b border-[var(--primary)]' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                    class="transition-colors pb-0.5">
                    <span x-text="language === 'es' ? 'Inicio' : 'Home'"></span>
                </a>
                <a href="/foundry"
                    :class="isActive('/foundry') ? 'text-[var(--primary)] font-bold border-b border-[var(--primary)]' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                    class="transition-colors pb-0.5">
                    <span x-text="language === 'es' ? 'Fundición' : 'Foundry'"></span>
                </a>
                <a href="/character"
                    :class="isActive('/character') ? 'text-[var(--primary)] font-bold border-b border-[var(--primary)]' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                    class="transition-colors pb-0.5">
                    <span x-text="language === 'es' ? 'Personaje' : 'Character'"></span>
                </a>
                <a href="/inventory"
                    :class="isActive('/inventory') ? 'text-[var(--primary)] font-bold border-b border-[var(--primary)]' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                    class="transition-colors pb-0.5">
                    <span x-text="language === 'es' ? 'Inventario' : 'Inventory'"></span>
                </a>
                <a href="/skills"
                    :class="isActive('/skills') ? 'text-[var(--primary)] font-bold border-b border-[var(--primary)]' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                    class="transition-colors pb-0.5">
                    <span x-text="language === 'es' ? 'Habilidades' : 'Skills'"></span>
                </a>
                <a href="/stats"
                    :class="isActive('/stats') ? 'text-[var(--primary)] font-bold border-b border-[var(--primary)]' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                    class="transition-colors pb-0.5">
                    <span x-text="language === 'es' ? 'Estadísticas' : 'Stats'"></span>
                </a>
                <a href="/donations"
                    :class="isActive('/donations') ? 'text-[var(--primary)] font-bold border-b border-[var(--primary)]' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                    class="transition-colors pb-0.5"
                    x-data="{
                        currentText: '',
                        isGlitching: false,
                        scrambleInterval: null,
                        flickerTimeout: null,
                        chars: '!@#$%^&*()_+-=[]{}|;:,./<>?░▒▓█▌▐▀▄01',
                        getOriginal() {
                            return language === 'es' ? 'Donaciones' : 'Donations';
                        },
                        getGlitchTarget() {
                            return language === 'es' ? 'Mendicidad' : 'E-Begging';
                        },
                        cleanup() {
                            if (this.scrambleInterval) { clearInterval(this.scrambleInterval); this.scrambleInterval = null; }
                            if (this.flickerTimeout) { clearTimeout(this.flickerTimeout); this.flickerTimeout = null; }
                        },
                        scramble(toText) {
                            if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                                this.cleanup();
                                this.isGlitching = true;
                                const length = Math.max(this.currentText.length, toText.length);
                                const half = Math.ceil(length / 2);
                                this.currentText = toText.split('').map((char, index) => {
                                    if (index < half) return char;
                                    return this.chars[Math.floor(Math.random() * this.chars.length)];
                                }).join('');
                                this.flickerTimeout = setTimeout(() => { this.currentText = toText; this.isGlitching = false; this.flickerTimeout = null; }, 100);
                                return;
                            }
                            this.cleanup();
                            this.isGlitching = true;
                            let length = Math.max(this.currentText.length, toText.length);
                            let count = 0;
                            this.scrambleInterval = setInterval(() => {
                                this.currentText = toText.split('').map((char, index) => {
                                    if (index < count) return char;
                                    return this.chars[Math.floor(Math.random() * this.chars.length)];
                                }).join('');
                                count += 1.2;
                                if (count >= length) {
                                    this.currentText = toText;
                                    this.isGlitching = false;
                                    clearInterval(this.scrambleInterval);
                                    this.scrambleInterval = null;
                                }
                            }, 20);
                        },
                        triggerGlitch() {
                            this.scramble(this.getGlitchTarget());
                        },
                        revertGlitch() {
                            this.scramble(this.getOriginal());
                        },
                        init() {
                            this.currentText = this.getOriginal();
                            this.$watch('language', () => { this.cleanup(); this.currentText = this.getOriginal(); });
                        }
                    }"
                    @mouseenter="triggerGlitch()"
                    @mouseleave="revertGlitch()"
                    :class="isGlitching ? 'glitch-hack-active' : ''">
                    <span x-text="currentText"></span>
                </a>
            </nav>

            <!-- Control Switchers (Desktop only) -->
            <div class="hidden md:flex items-center justify-end gap-4 shrink-0">
                <!-- Language Switcher (text style) -->
                <button type="button" @click="setLanguage(language === 'en' ? 'es' : 'en')"
                    class="font-mono text-xs uppercase tracking-wider transition-colors focus-ring-signature rounded px-2 py-1 flex items-center gap-1 text-[var(--muted)] hover:text-[var(--ink)]"
                    :aria-label="language === 'es' ? 'Cambiar a Inglés' : 'Switch to Spanish'">
                    <span :class="language === 'en' ? 'text-[var(--accent)] font-bold' : ''">en</span>
                    <span class="text-[var(--border)] font-normal">|</span>
                    <span :class="language === 'es' ? 'text-[var(--accent)] font-bold' : ''">es</span>
                </button>

                <!-- Theme Switcher (Cycle Single Button) -->
                <button type="button" @click="cycleTheme()"
                    class="interactive-link focus-ring-signature rounded-full p-2 text-[var(--muted)] hover:text-[var(--ink)] hover:bg-[var(--surface-raised)] transition-all flex items-center justify-center shrink-0 border border-[var(--border)]"
                    :aria-label="language === 'es' ? 'Tema activo: ' + theme + '. Cambiar tema' : 'Active theme: ' + theme + '. Toggle theme'">
                    <!-- Sun Icon (if theme is light) -->
                    <svg x-show="theme === 'light'" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path
                            d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58a.996.996 0 0 0-1.41 0 .996.996 0 0 0 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37a.996.996 0 0 0-1.41 0 .996.996 0 0 0 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41l-1.06-1.06zm1.06-10.96a.996.996 0 0 0 0-1.41.996.996 0 0 0-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.01a.996.996 0 0 0 0-1.41.996.996 0 0 0-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z" />
                    </svg>

                    <!-- Moon Icon (if theme is dark) -->
                    <svg x-show="theme === 'dark'" class="w-4.5 h-4.5 fill-current" viewBox="0 0 24 24">
                        <path
                            d="M12.3 22h-.1c-5.5 0-10-4.5-10-10 0-4.8 3.5-9 8.3-9.9.5-.1.9.3.8.8-.4 1.5-.2 3.2.7 4.7 1 1.7 2.8 2.7 4.7 2.7.5 0 .9.2 1 .7.4 1.8 1.4 3.4 2.8 4.5.3.3.4.6.3 1-.9 3.1-3.7 5.4-7.2 5.5z" />
                    </svg>

                    <!-- Monitor Icon (if theme is system) -->
                    <svg x-show="theme === 'system'" class="w-4 h-4 fill-current" viewBox="0 0 24 24">
                        <path
                            d="M20 18c1.1 0 1.99-.9 1.99-2L22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z" />
                    </svg>
                </button>
            </div>

            <!-- Mobile Actions & Hamburger -->
            <div class="flex md:hidden items-center gap-3 shrink-0">
                <!-- Mobile Language Switcher -->
                <button type="button" @click="setLanguage(language === 'en' ? 'es' : 'en')"
                    class="font-mono text-xs uppercase tracking-wider transition-colors focus-ring-signature rounded px-2 py-1 flex items-center gap-1 text-[var(--muted)] hover:text-[var(--ink)]"
                    :aria-label="language === 'es' ? 'Cambiar a Inglés' : 'Switch to Spanish'">
                    <span :class="language === 'en' ? 'text-[var(--accent)] font-bold' : ''">en</span>
                    <span class="text-[var(--border)] font-normal">|</span>
                    <span :class="language === 'es' ? 'text-[var(--accent)] font-bold' : ''">es</span>
                </button>

                <!-- Hamburger Button -->
                <button type="button" @click="mobileMenuOpen = true"
                    class="interactive-link focus-ring-signature rounded-full p-2 text-[var(--muted)] hover:text-[var(--ink)] hover:bg-[var(--surface-raised)] transition-all flex items-center justify-center shrink-0 border border-[var(--border)]"
                    :aria-label="language === 'es' ? 'Abrir menú' : 'Open menu'">
                    <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 24 24">
                        <path d="M3 18h18v-2H3v2zm0-5h18v-2H3v2zm0-7v2h18V6H3z" />
                    </svg>
                </button>
            </div>
        </header>

        <!-- MOBILE MENU DRAWER -->
        <div x-show="mobileMenuOpen" x-cloak class="fixed inset-0 z-50 flex md:hidden" role="dialog" aria-modal="true">
            <!-- Backdrop -->
            <div x-show="mobileMenuOpen" x-transition:enter="transition-opacity ease-out duration-200"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" @click="mobileMenuOpen = false"
                class="fixed inset-0 bg-black/60 backdrop-blur-sm"></div>

            <!-- Sheet Panel -->
            <div x-show="mobileMenuOpen" x-transition:enter="transition-transform ease-out duration-240"
                x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0"
                x-transition:leave="transition-transform ease-in duration-240" x-transition:leave-start="translate-x-0"
                x-transition:leave-end="translate-x-full"
                class="fixed inset-y-0 right-0 w-full max-w-xs bg-[var(--bg)] border-l border-[var(--border)] p-6 flex flex-col gap-8 transition-theme z-10">
                <!-- Header in Sheet -->
                <div class="flex justify-between items-center">
                    <div class="flex items-center gap-3">
                        <span class="text-xl font-bold tracking-tight text-[var(--primary)] font-sans">nk.</span>
                    </div>
                    <!-- Close Button -->
                    <button type="button" @click="mobileMenuOpen = false"
                        class="interactive-link focus-ring-signature rounded-full p-2 text-[var(--muted)] hover:text-[var(--ink)] hover:bg-[var(--surface-raised)] flex items-center justify-center shrink-0 border border-[var(--border)]"
                        :aria-label="language === 'es' ? 'Cerrar menú' : 'Close menu'">
                        <svg class="w-4.5 h-4.5 fill-current" viewBox="0 0 24 24">
                            <path
                                d="M19 6.41L17.59 5 12 10.59 6.41 5 5 6.41 10.59 12 5 17.59 6.41 19 12 13.41 17.59 19 19 17.59 13.41 12z" />
                        </svg>
                    </button>
                </div>

                <!-- Navigation Links Stack -->
                <nav class="flex flex-col gap-6 text-label pt-4">
                    <a href="/" @click="mobileMenuOpen = false"
                        :class="isActive('/') ? 'text-[var(--primary)] font-bold border-b border-[var(--primary)] self-start' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                        class="transition-colors pb-0.5">
                        <span x-text="language === 'es' ? 'Inicio' : 'Home'"></span>
                    </a>
                    <a href="/foundry" @click="mobileMenuOpen = false"
                        :class="isActive('/foundry') ? 'text-[var(--primary)] font-bold border-b border-[var(--primary)] self-start' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                        class="transition-colors pb-0.5">
                        <span x-text="language === 'es' ? 'Fundición' : 'Foundry'"></span>
                    </a>
                    <a href="/character" @click="mobileMenuOpen = false"
                        :class="isActive('/character') ? 'text-[var(--primary)] font-bold border-b border-[var(--primary)] self-start' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                        class="transition-colors pb-0.5">
                        <span x-text="language === 'es' ? 'Personaje' : 'Character'"></span>
                    </a>
                    <a href="/inventory" @click="mobileMenuOpen = false"
                        :class="isActive('/inventory') ? 'text-[var(--primary)] font-bold border-b border-[var(--primary)] self-start' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                        class="transition-colors pb-0.5">
                        <span x-text="language === 'es' ? 'Inventario' : 'Inventory'"></span>
                    </a>
                    <a href="/skills" @click="mobileMenuOpen = false"
                        :class="isActive('/skills') ? 'text-[var(--primary)] font-bold border-b border-[var(--primary)] self-start' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                        class="transition-colors pb-0.5">
                        <span x-text="language === 'es' ? 'Habilidades' : 'Skills'"></span>
                    </a>
                    <a href="/stats" @click="mobileMenuOpen = false"
                        :class="isActive('/stats') ? 'text-[var(--primary)] font-bold border-b border-[var(--primary)] self-start' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                        class="transition-colors pb-0.5">
                        <span x-text="language === 'es' ? 'Estadísticas' : 'Stats'"></span>
                    </a>
                    <a href="/donations" @click="mobileMenuOpen = false"
                        :class="isActive('/donations') ? 'text-[var(--primary)] font-bold border-b border-[var(--primary)] self-start' : 'text-[var(--muted)] hover:text-[var(--ink)]'"
                        class="transition-colors pb-0.5"
                        x-data="{
                            currentText: '',
                            isGlitching: false,
                            scrambleInterval: null,
                            flickerTimeout: null,
                            chars: '!@#$%^&*()_+-=[]{}|;:,./<>?░▒▓█▌▐▀▄01',
                            getOriginal() {
                                return language === 'es' ? 'Donaciones' : 'Donations';
                            },
                            getGlitchTarget() {
                                return language === 'es' ? 'Mendicidad' : 'E-Begging';
                            },
                            cleanup() {
                                if (this.scrambleInterval) { clearInterval(this.scrambleInterval); this.scrambleInterval = null; }
                                if (this.flickerTimeout) { clearTimeout(this.flickerTimeout); this.flickerTimeout = null; }
                            },
                            scramble(toText) {
                                if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                                    this.cleanup();
                                    this.isGlitching = true;
                                    const length = Math.max(this.currentText.length, toText.length);
                                    const half = Math.ceil(length / 2);
                                    this.currentText = toText.split('').map((char, index) => {
                                        if (index < half) return char;
                                        return this.chars[Math.floor(Math.random() * this.chars.length)];
                                    }).join('');
                                    this.flickerTimeout = setTimeout(() => { this.currentText = toText; this.isGlitching = false; this.flickerTimeout = null; }, 100);
                                    return;
                                }
                                this.cleanup();
                                this.isGlitching = true;
                                let length = Math.max(this.currentText.length, toText.length);
                                let count = 0;
                                this.scrambleInterval = setInterval(() => {
                                    this.currentText = toText.split('').map((char, index) => {
                                        if (index < count) return char;
                                        return this.chars[Math.floor(Math.random() * this.chars.length)];
                                    }).join('');
                                    count += 1.2;
                                    if (count >= length) {
                                        this.currentText = toText;
                                        this.isGlitching = false;
                                        clearInterval(this.scrambleInterval);
                                        this.scrambleInterval = null;
                                    }
                                }, 20);
                            },
                            triggerGlitch() {
                                this.scramble(this.getGlitchTarget());
                            },
                            revertGlitch() {
                                this.scramble(this.getOriginal());
                            },
                            init() {
                                this.currentText = this.getOriginal();
                                this.$watch('language', () => { this.cleanup(); this.currentText = this.getOriginal(); });
                            }
                        }"
                        @mouseenter="triggerGlitch()"
                        @mouseleave="revertGlitch()"
                        :class="isGlitching ? 'glitch-hack-active' : ''">
                        <span x-text="currentText"></span>
                    </a>
                </nav>

                <!-- Theme Switcher at bottom of sheet -->
                <div class="flex flex-col gap-4 border-t border-[var(--border)] pt-6 mt-auto">
                    <button type="button" @click="cycleTheme()"
                        class="interactive-link focus-ring-signature rounded-md py-2.5 px-4 text-[var(--muted)] hover:text-[var(--ink)] hover:bg-[var(--surface-raised)] transition-all flex items-center justify-center gap-2 border border-[var(--border)]">
                        <span class="text-mono text-xs uppercase tracking-wider"
                            x-text="language === 'es' ? 'Tema: ' + theme : 'Theme: ' + theme"></span>
                    </button>
                </div>
            </div>
        </div>

        <!-- MAIN BENCH LAYOUT -->
        <main class="w-full max-w-6xl mx-auto flex-1 flex flex-col gap-12 items-start py-4">
            {{ $slot }}
        </main>

        <!-- FOOTER -->
        <footer
            class="w-full max-w-6xl mx-auto border-t border-[var(--border)] mt-16 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-mono text-xs text-[var(--muted)]">
            <div class="flex flex-wrap items-center gap-2">
                <span>Jorge Thomas &copy; 2026</span>
                <span>&middot;</span>
                <a href="https://github.com/akrista/notakrista.com" target="_blank" rel="noopener noreferrer"
                    class="text-[var(--accent)] hover:text-[var(--ink)] focus-ring-signature rounded-sm px-1 py-0.5 transition-colors">
                    <span x-text="language === 'es' ? '[Código Abierto / MIT]' : '[Open Source / MIT]'"></span>
                </a>
                <span>&middot;</span>
                <button type="button" @click="privacyOpen = true; privacyBannerOpen = true;"
                    class="text-[var(--muted)] hover:text-[var(--ink)] focus-ring-signature rounded-sm px-1 py-0.5 transition-colors cursor-pointer">
                    <span x-text="language === 'es' ? '[Privacidad]' : '[Privacy]'"></span>
                </button>
            </div>
            <div class="flex items-center gap-3">
                <span
                    x-text="language === 'es' ? 'Construido con Laravel, Livewire y Alpine.js' : 'Built with Laravel, Livewire & Alpine.js'"></span>
                <span>&middot;</span>
                <a href="https://github.com/akrista" target="_blank" rel="noopener noreferrer" class="hover:text-[var(--ink)] focus-ring-signature rounded-sm px-1 py-0.5 transition-colors">[GitHub]</a>
            </div>
        </footer>

        <!-- PRIVACY DISCLAIMER BANNER & MODAL -->
        <div x-show="privacyBannerOpen || privacyOpen"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-2"
            x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 translate-y-2"
            class="fixed bottom-4 right-4 left-4 sm:left-auto sm:max-w-md z-50 p-5 rounded-lg bg-[var(--surface-raised)] border border-[var(--border)] shadow-xl text-xs text-[var(--ink)] space-y-3"
            style="display: none;"
            role="region"
            aria-label="Privacy Disclaimer">
            <div class="flex items-center justify-between border-b border-[var(--border)] pb-2">
                <div class="flex items-center gap-2">
                    <span class="inline-block w-2.5 h-2.5 rounded-full bg-[var(--accent)]"></span>
                    <h3 class="font-semibold text-mono text-xs uppercase tracking-wider text-[var(--ink)]" x-text="language === 'es' ? 'Privacidad y Analítica' : 'Privacy & Analytics'"></h3>
                </div>
                <button type="button" @click="dismissPrivacy()"
                    class="text-[var(--muted)] hover:text-[var(--ink)] focus-ring-signature rounded p-1 transition-colors"
                    aria-label="Close">
                    ✕
                </button>
            </div>
            <p class="text-body text-xs text-[var(--muted)] leading-relaxed"
                x-text="language === 'es'
                    ? 'Este sitio utiliza Google Analytics y Microsoft Clarity para medir de forma anónima el rendimiento y la interacción de los visitantes. No se venden ni comparten datos personales.'
                    : 'This site uses Google Analytics and Microsoft Clarity to anonymously measure visitor interactions and performance. No personal data is sold or shared.'">
            </p>
            <div class="flex items-center justify-end gap-2 pt-1">
                <button type="button" @click="dismissPrivacy()"
                    class="px-3 py-1.5 rounded-md bg-[var(--primary)] text-[var(--primary-ink)] font-semibold text-mono text-xs uppercase tracking-wider focus-ring-signature hover:opacity-90 transition-opacity cursor-pointer">
                    <span x-text="language === 'es' ? 'Entendido' : 'Got It'"></span>
                </button>
            </div>
        </div>
    </div>
</body>

</html>