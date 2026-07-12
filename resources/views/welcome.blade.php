<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Jorge Thomas — Software Engineer</title>

        <link rel="icon" href="/favicon.png" type="image/png">
        <link rel="apple-touch-icon" href="/apple-touch-icon.png">

        @fonts
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @fluxAppearance

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

            /* Reduce Motion */
            @media (prefers-reduced-motion: reduce) {
                .transition-theme,
                .transition-all,
                .segmented-pill {
                    transition: none !important;
                    animation: none !important;
                }
            }
        </style>
    </head>
    <body class="antialiased">
        <div
            x-data="{
                mode: 'business',
                language: (navigator.language || navigator.userLanguage || 'en').startsWith('es') ? 'es' : 'en',
                theme: localStorage.getItem('theme') || 'system',
                systemDark: window.matchMedia('(prefers-color-scheme: dark)').matches,
                attrIndex: 0,
                showDonations: false,
                attributes: {
                    en: ['tigerbeetle lover', 'opensource fan', 'laravel enjoyer', 'gamer', 'creator of bizkit'],
                    es: ['amante de tigerbeetle', 'fan del código abierto', 'entusiasta de laravel', 'gamer', 'creador de bizkit']
                },
                init() {
                    setInterval(() => {
                        this.attrIndex = (this.attrIndex + 1) % this.attributes[this.language].length;
                    }, 2500);

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
                }
            }"
            :class="{ 'business': mode === 'business', 'light': getResolvedTheme() === 'light' }"
            class="min-h-screen bg-[var(--bg)] text-[var(--ink)] transition-theme duration-300 flex flex-col justify-between p-6 md:p-12 font-sans"
        >
            <!-- TOP BAR NAVIGATION -->
            <header class="w-full max-w-6xl mx-auto flex flex-col sm:flex-row justify-between items-center sm:items-center gap-6 mb-12 sm:mb-16">
                <!-- Brand Mark -->
                <a href="/" class="flex items-center gap-3 group focus-ring-signature rounded-md">
                    <img src="{{ asset('logo-circle.png') }}" class="size-10 object-contain rounded-full border border-[var(--border)] transition-theme" alt="Logo">
                    <div class="flex flex-col items-center sm:items-start">
                        <span class="text-xl font-bold tracking-tight text-[var(--primary)] font-sans">nk.</span>
                        <span class="text-mono text-xs text-[var(--muted)]">notakrista.com</span>
                    </div>
                </a>

                <!-- Control Switchers -->
                <div class="flex items-center justify-center sm:justify-end gap-4 w-full sm:w-auto">
                    <!-- Language Switcher (text style) -->
                    <button 
                        type="button"
                        @click="language = language === 'en' ? 'es' : 'en'"
                        class="font-mono text-xs uppercase tracking-wider transition-colors focus-ring-signature rounded px-2 py-1 flex items-center gap-1 text-[var(--muted)] hover:text-[var(--ink)]"
                        :aria-label="language === 'en' ? 'Switch to Spanish' : 'Cambiar a Inglés'"
                    >
                        <span :class="language === 'en' ? 'text-[var(--accent)] font-bold' : ''">en</span>
                        <span class="text-[var(--border)] font-normal">|</span>
                        <span :class="language === 'es' ? 'text-[var(--accent)] font-bold' : ''">es</span>
                    </button>

                    <!-- Mode Switcher -->
                    <div class="segmented-control" role="group" aria-label="Theme view selection">
                        <button
                            type="button"
                            @click="mode = 'personal'"
                            :class="mode === 'personal' ? 'active' : ''"
                            class="segmented-pill focus-ring-signature"
                        >personal</button>
                        <button
                            type="button"
                            @click="mode = 'business'"
                            :class="mode === 'business' ? 'active' : ''"
                            class="segmented-pill focus-ring-signature"
                            x-text="language === 'en' ? 'business' : 'negocios'"
                        >business</button>
                    </div>

                    <!-- Theme Switcher (Cycle Single Button) -->
                    <button 
                        type="button"
                        @click="cycleTheme()"
                        class="interactive-link focus-ring-signature rounded-full p-2 text-[var(--muted)] hover:text-[var(--ink)] hover:bg-[var(--surface-raised)] transition-all flex items-center justify-center shrink-0 border border-[var(--border)]"
                        :aria-label="language === 'en' ? 'Toggle theme' : 'Cambiar tema'"
                    >
                        <!-- Sun Icon (if theme is light) -->
                        <svg x-show="theme === 'light'" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58a.996.996 0 0 0-1.41 0 .996.996 0 0 0 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37a.996.996 0 0 0-1.41 0 .996.996 0 0 0 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41l-1.06-1.06zm1.06-10.96a.996.996 0 0 0 0-1.41.996.996 0 0 0-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.01a.996.996 0 0 0 0-1.41.996.996 0 0 0-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z"/></svg>
                        
                        <!-- Moon Icon (if theme is dark) -->
                        <svg x-show="theme === 'dark'" class="w-4.5 h-4.5 fill-current" viewBox="0 0 24 24"><path d="M12.3 22h-.1c-5.5 0-10-4.5-10-10 0-4.8 3.5-9 8.3-9.9.5-.1.9.3.8.8-.4 1.5-.2 3.2.7 4.7 1 1.7 2.8 2.7 4.7 2.7.5 0 .9.2 1 .7.4 1.8 1.4 3.4 2.8 4.5.3.3.4.6.3 1-.9 3.1-3.7 5.4-7.2 5.5z"/></svg>
                        
                        <!-- Monitor Icon (if theme is system) -->
                        <svg x-show="theme === 'system'" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M20 18c1.1 0 1.99-.9 1.99-2L22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z"/></svg>
                    </button>
                </div>
            </header>

            <!-- MAIN BENCH LAYOUT -->
            <main class="w-full max-w-6xl mx-auto flex-1 flex flex-col lg:flex-row gap-12 lg:gap-16 items-start">

                <!-- LEFT COLUMN: Identity & Info -->
                <section class="w-full lg:w-5/12 flex flex-col gap-8">
                    <!-- Hero Header -->
                    <div class="flex flex-col gap-4">
                        <h1 class="text-display text-[var(--ink)]">Jorge Thomas</h1>

                        <!-- Dynamic Subheading -->
                        <div class="h-8 flex items-center">
                            <p class="text-mono text-[var(--muted)] flex gap-1.5 items-center">
                                <span x-text="language === 'en' ? 'an engineer who is a' : 'un ingeniero que es'"></span>
                                <span
                                    x-text="attributes[language][attrIndex]"
                                    class="text-[var(--primary)] border-b border-[var(--primary)] font-semibold transition-all duration-200"
                                ></span>
                            </p>
                        </div>
                    </div>

                    <!-- Status Indicator -->
                    <div>
                        <span class="badge-chip bg-[var(--surface-raised)] text-[var(--ink)] border border-[var(--border)]">
                            <span class="w-2.5 h-2.5 rounded-full bg-[var(--accent)] inline-block"></span>
                            <span class="text-xs text-[var(--ink)] font-mono" x-text="language === 'en' ? 'Available for projects' : 'Disponible para proyectos'"></span>
                        </span>
                    </div>

                    <!-- Mode Specific Section -->
                    <div class="min-h-[140px]">
                        <!-- Personal Mode Details -->
                        <div x-show="mode === 'personal'" x-transition class="flex flex-col gap-4">
                            <p class="text-body text-[var(--muted)]" x-text="language === 'en'
                                ? 'Welcome to my digital workbench. This view showcases the personal side of my work—my projects, my gaming logs, my references, and the real life backing up the code.'
                                : 'Bienvenido a mi banco de trabajo digital. Esta vista muestra el lado personal de mi labor—mis proyectos, registros de juego, referencias y la vida real que respalda el código.'"></p>

                            <!-- Personal Stats Mockup -->
                            <div class="flex flex-wrap gap-3">
                                <a href="https://wakatime.com/@akrista" target="_blank" class="badge-chip bg-[var(--surface-raised)] border border-[var(--border)] focus-ring-signature hover:border-[var(--primary)] transition-colors">
                                    <span class="text-[var(--primary)]" x-text="language === 'en' ? 'Wakatime' : 'Wakatime'"></span>
                                    <span class="text-[var(--ink)]">1,423 hrs ↗</span>
                                </a>
                                <a href="https://steamcommunity.com/id/akrista/" target="_blank" class="badge-chip bg-[var(--surface-raised)] border border-[var(--border)] focus-ring-signature hover:border-[var(--primary)] transition-colors">
                                    <span class="text-[var(--primary)]" x-text="language === 'en' ? 'Steam' : 'Steam'"></span>
                                    <span class="text-[var(--ink)]">LVL 48 ↗</span>
                                </a>
                                <a href="https://retroachievements.org/user/akrista" target="_blank" class="badge-chip bg-[var(--surface-raised)] border border-[var(--border)] focus-ring-signature hover:border-[var(--primary)] transition-colors">
                                    <span class="text-[var(--primary)]" x-text="language === 'en' ? 'RetroAchievements' : 'RetroAchievements'"></span>
                                    <span class="text-[var(--ink)]">12,450 pts ↗</span>
                                </a>
                            </div>
                        </div>

                        <!-- Business Mode Details -->
                        <div x-show="mode === 'business'" x-transition class="flex flex-col gap-4">
                            <p class="text-body text-[var(--muted)]" x-text="language === 'en'
                                ? 'I build robust, production-grade applications with Laravel, Livewire, Filament, and Tailwind CSS. I prioritize clean architectures, database performance, and highly intuitive interfaces.'
                                : 'Desarrollo aplicaciones robustas de nivel de producción con Laravel, Livewire, Filament y Tailwind CSS. Priorizo las arquitecturas limpias, el rendimiento de base de datos e interfaces intuitivas.'"></p>

                            <!-- Tech Stack Chips -->
                            <div class="flex flex-wrap gap-2 text-mono text-xs">
                                <span class="px-2.5 py-1 rounded bg-[var(--surface-raised)] text-[var(--ink)] border border-[var(--border)]">PHP 8.5</span>
                                <span class="px-2.5 py-1 rounded bg-[var(--surface-raised)] text-[var(--ink)] border border-[var(--border)]">Laravel 13</span>
                                <span class="px-2.5 py-1 rounded bg-[var(--surface-raised)] text-[var(--ink)] border border-[var(--border)]">Filament v5</span>
                                <span class="px-2.5 py-1 rounded bg-[var(--surface-raised)] text-[var(--ink)] border border-[var(--border)]">Livewire v4</span>
                                <span class="px-2.5 py-1 rounded bg-[var(--surface-raised)] text-[var(--ink)] border border-[var(--border)]">Tailwind CSS v4</span>
                            </div>
                        </div>
                    </div>

                    <!-- Recruiter CTA / Contact Channels -->
                    <div class="card-bench flex flex-col gap-4">
                        <h2 class="text-title text-[var(--ink)]" x-text="language === 'en' ? 'Get in Touch' : 'Contacto'"></h2>
                        <p class="text-mono text-xs text-[var(--muted)]" x-text="language === 'en' ? 'Reach out directly through these channels:' : 'Escríbeme directamente:'"></p>

                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                            <a href="mailto:info@notakrista.com" class="interactive-link focus-ring-signature font-mono text-[var(--accent)] hover:underline text-sm inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M20 4H4c-1.1 0-1.99.9-1.99 2L2 18c0 1.1.9 2 2 2h16c1.1 0 2-.9 2-2V6c0-1.1-.9-2-2-2zm0 4l-8 5-8-5V6l8 5 8-5v2z"/></svg>
                                <span>EMAIL ↗</span>
                            </a>
                            <a href="tel:+584142034875" class="interactive-link focus-ring-signature font-mono text-[var(--accent)] hover:underline text-sm inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M6.62 10.79c1.44 2.83 3.76 5.14 6.59 6.59l2.2-2.2c.27-.27.67-.36 1.02-.24 1.12.37 2.33.57 3.57.57.55 0 1 .45 1 1V20c0 .55-.45 1-1 1-9.39 0-17-7.61-17-17 0-.55.45-1 1-1h3.5c.55 0 1 .45 1 1 0 1.25.2 2.45.57 3.57.11.35.03.74-.25 1.02l-2.2 2.2z"/></svg>
                                <span>PHONE ↗</span>
                            </a>
                            <a href="https://t.me/Akrista" target="_blank" class="interactive-link focus-ring-signature font-mono text-[var(--accent)] hover:underline text-sm inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M9.78 18.65l.28-4.28 7.76-7.01c.34-.3-.07-.47-.52-.18L7.79 12.25l-4.13-1.29c-.9-.28-.92-.9.19-1.34l16.14-6.22c.75-.28 1.4.17 1.15 1.25l-2.74 12.94c-.21 1.02-.81 1.27-1.66.79l-4.22-3.11-2.04 1.96c-.23.23-.42.42-.86.42z"/></svg>
                                <span>TELEGRAM ↗</span>
                            </a>
                            <a href="https://linkedin.com/in/akrista" target="_blank" class="interactive-link focus-ring-signature font-mono text-[var(--accent)] hover:underline text-sm inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.32 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.79M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77z"/></svg>
                                <span>LINKEDIN ↗</span>
                            </a>
                            <a href="https://github.com/akrista" target="_blank" class="interactive-link focus-ring-signature font-mono text-[var(--accent)] hover:underline text-sm inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M12 2A10 10 0 0 0 2 12c0 4.42 2.87 8.17 6.84 9.5.5.08.66-.23.66-.5v-1.69c-2.77.6-3.36-1.34-3.36-1.34-.46-1.16-1.11-1.47-1.11-1.47-.9-.62.07-.6.07-.6 1 .07 1.53 1.03 1.53 1.03.9 1.52 2.34 1.07 2.91.83.09-.65.35-1.09.63-1.34-2.22-.25-4.55-1.11-4.55-4.92 0-1.11.38-2 1.03-2.71-.1-.25-.45-1.29.1-2.64 0 0 .84-.27 2.75 1.02.79-.22 1.65-.33 2.5-.33.85 0 1.71.11 2.5.33 1.91-1.29 2.75-1.02 2.75-1.02.55 1.35.2 2.39.1 2.64.65.71 1.03 1.6 1.03 2.71 0 3.82-2.34 4.66-4.57 4.91.36.31.69.92.69 1.85V21c0 .27.16.59.67.5C19.14 20.16 22 16.42 22 12A10 10 0 0 0 12 2z"/></svg>
                                <span>GITHUB ↗</span>
                            </a>
                            <a href="https://instagram.com/notakrista" target="_blank" class="interactive-link focus-ring-signature font-mono text-[var(--accent)] hover:underline text-sm inline-flex items-center gap-1.5">
                                <svg class="w-4 h-4 fill-current shrink-0" viewBox="0 0 24 24"><path d="M7.8 2h8.4C19.4 2 22 4.6 22 7.8v8.4a5.8 5.8 0 0 1-5.8 5.8H7.8C4.6 22 2 19.4 2 16.2V7.8A5.8 5.8 0 0 1 7.8 2m-.2 2A3.6 3.6 0 0 0 4 7.6v8.8A3.6 3.6 0 0 0 7.6 20h8.8a3.6 3.6 0 0 0 3.6-3.6V7.6A3.6 3.6 0 0 0 16.4 4H7.6m9.65 1.5a1.25 1.25 0 1 1 0 2.5 1.25 1.25 0 0 1 0-2.5M12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10m0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>
                                <span>INSTAGRAM ↗</span>
                            </a>
                        </div>
                    </div>
                </section>

                <!-- RIGHT COLUMN: Projects, Relief Support & Content -->
                <section class="w-full lg:w-7/12 flex flex-col gap-8">

                    <!-- VENEZUELA EARTHQUAKE RELIEF SECTION -->
                    <div class="alert-box flex flex-col gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-2.5 h-2.5 rounded-full bg-[var(--red)] animate-pulse"></span>
                            <h3 class="font-mono text-sm uppercase text-[var(--red)] font-semibold" x-text="language === 'en' ? 'Emergency Relief Appeal' : 'Llamado de Emergencia'"></h3>
                        </div>
                        <p class="text-body text-sm" x-text="language === 'en'
                            ? 'On June 24, 2026, a severe earthquake struck Venezuela, deeply affecting my family and our living situation. I am raising support to cover structural repairs and urgent recovery expenses.'
                            : 'El 24 de junio de 2026, un fuerte terremoto sacudió Venezuela, afectando gravemente a mi familia y nuestra vivienda. Estoy recaudando fondos para reparaciones estructurales y gastos urgentes de recuperación.'">
                        </p>

                        <div>
                            <button
                                type="button"
                                @click="showDonations = !showDonations"
                                class="button-cta bg-[var(--primary)] text-[var(--primary-ink)] focus-ring-signature font-bold text-xs"
                                x-text="showDonations ? (language === 'en' ? 'Hide Options' : 'Ocultar Opciones') : (language === 'en' ? 'Support / Donate' : 'Apoyar / Donar')"
                            ></button>
                        </div>

                        <!-- Expandable Donation Details -->
                        <div x-show="showDonations" x-transition class="mt-4 pt-4 border-t border-[var(--border)] flex flex-col gap-4">
                            <p class="text-mono text-xs text-[var(--muted)]" x-text="language === 'en' ? 'Direct Crypto and PayPal channels:' : 'Canales directos de Crypto y PayPal:'"></p>
                            <div class="grid gap-3 font-mono text-xs">
                                <div class="flex flex-col gap-1 bg-[var(--surface-raised)] p-3 rounded-md">
                                    <span class="text-[var(--primary)]">USDT (TRC-20)</span>
                                    <span class="text-[var(--ink)] select-all font-mono text-[10px] break-all sm:text-xs">TX5zV1K2Y5tP1W1E1L1I1E1F1A1C1H1T1R1U1S1T...</span>
                                </div>
                                <div class="flex flex-col gap-1 bg-[var(--surface-raised)] p-3 rounded-md">
                                    <span class="text-[var(--primary)]">PayPal</span>
                                    <span class="text-[var(--ink)] select-all">paypal.me/notakrista</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-col gap-6 w-full">
                        <h2 class="text-headline text-[var(--ink)]" x-text="language === 'en' ? 'Projects & Collaborations' : 'Proyectos y Colaboraciones'"></h2>

                        <div class="flex flex-col gap-4">
                            <!-- Project 1: Todoticket Calculator -->
                            <a href="https://todoticket.notakrista.com" target="_blank" class="card-bench flex flex-col gap-2 focus-ring-signature hover:border-[var(--primary)] hover:bg-[var(--surface-raised)] transition-all">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-title text-[var(--ink)]">todoticket calculator ↗</h3>
                                    <span class="text-mono text-xs text-[var(--accent)] font-semibold uppercase" x-text="language === 'en' ? 'Tool' : 'Herramienta'"></span>
                                </div>
                                <p class="text-body text-sm text-[var(--muted)]" x-text="language === 'en'
                                    ? 'A local voucher and balance calculator tailored for Venezuelan employees, maximizing efficiency for salary ticket cards.'
                                    : 'Calculadora local de vales y saldos diseñada para empleados venezolanos, optimizando el uso de tarjetas de ticket alimentario.'"></p>
                            </a>

                            <!-- Project 2: Bizkit Starter Kit -->
                            <a href="https://github.com/akrista/bizkit" target="_blank" class="card-bench flex flex-col gap-2 focus-ring-signature hover:border-[var(--primary)] hover:bg-[var(--surface-raised)] transition-all">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-title text-[var(--ink)]">bizkit starter kit ↗</h3>
                                    <span class="text-mono text-xs text-[var(--accent)] font-semibold uppercase" x-text="language === 'en' ? 'Open Source' : 'Código Abierto'"></span>
                                </div>
                                <p class="text-body text-sm text-[var(--muted)]" x-text="language === 'en'
                                    ? 'A premium, opinionated starter kit for Laravel applications including workspace structures, preset test harnesses, and code generators.'
                                    : 'Un kit de inicio premium para aplicaciones Laravel, que incluye estructuras de espacio de trabajo, arneses de prueba preestablecidos y generadores de código.'"></p>
                            </a>

                            <!-- Project 3: Rockery -->
                            <a href="https://rockery.notakrista.com" target="_blank" class="card-bench flex flex-col gap-2 focus-ring-signature hover:border-[var(--primary)] hover:bg-[var(--surface-raised)] transition-all">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-title text-[var(--ink)]">rockery ↗</h3>
                                    <span class="text-mono text-xs text-[var(--accent)] font-semibold uppercase" x-text="language === 'en' ? 'Knowledge Base' : 'Base de Conocimientos'"></span>
                                </div>
                                <p class="text-body text-sm text-[var(--muted)]" x-text="language === 'en'
                                    ? 'My personal knowledge base and blog built with Quartz, mapping technical notes, thoughts, and writings.'
                                    : 'Mi base de conocimientos y blog personal desarrollado con Quartz, recopilando notas técnicas, pensamientos y escritos.'"></p>
                            </a>

                            <!-- Project 4: Bytebase Contribution -->
                            <a href="https://github.com/bytebase/bytebase/releases/tag/1.17.0" target="_blank" class="card-bench flex flex-col gap-2 focus-ring-signature hover:border-[var(--primary)] hover:bg-[var(--surface-raised)] transition-all">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-title text-[var(--ink)]">bytebase contributions ↗</h3>
                                    <span class="text-mono text-xs text-[var(--accent)] font-semibold uppercase" x-text="language === 'en' ? 'Collaboration' : 'Colaboración'"></span>
                                </div>
                                <p class="text-body text-sm text-[var(--muted)]">
                                    <span x-text="language === 'en'
                                        ? 'Contributed code to Bytebase, an open-source database schema change management tool, during the launch cycle for v1.17.0.'
                                        : 'Aportaciones de código a Bytebase, una herramienta de código abierto para la gestión de cambios en bases de datos, durante el ciclo v1.17.0.'"></span>
                                </p>
                            </a>

                            <!-- Backgrounds Sharing -->
                            <a href="https://img.notakrista.com/share/t5lqpc6yCwbEwD8GRfa9r9Ed8CbCTXsJ12HkLeRCckSUhuz_mWCw2Y3HnKyMvG2qmjg" target="_blank" class="card-bench flex flex-col gap-2 focus-ring-signature hover:border-[var(--primary)] hover:bg-[var(--surface-raised)] transition-all">
                                <div class="flex justify-between items-start">
                                    <h3 class="text-title text-[var(--ink)]" x-text="language === 'en' ? 'Immich Backgrounds Gallery ↗' : 'Galería de Fondos de Immich ↗'"></h3>
                                    <span class="text-mono text-xs text-[var(--accent)] font-semibold uppercase" x-text="language === 'en' ? 'backgrounds' : 'fondos'"></span>
                                </div>
                                <p class="text-body text-sm text-[var(--muted)]">
                                    <span x-text="language === 'en'
                                        ? 'A shared Immich photo gallery containing backgrounds, setup logs, and curated photography from my personal workshop setup.'
                                        : 'Galería fotográfica compartida en Immich que contiene fondos de pantalla, registros de configuraciones y fotografía de mi taller.'"></span>
                                </p>
                            </a>
                        </div>
                    </div>
                </section>
            </main>

            <!-- FOOTER -->
            <footer class="w-full max-w-6xl mx-auto border-t border-[var(--border)] mt-16 pt-6 flex flex-col sm:flex-row justify-between items-center gap-4 text-mono text-xs text-[var(--muted)]">
                <div>
                    <span>Jorge Thomas &copy; 2026</span>
                </div>
                <div class="flex items-center gap-3">
                    <span x-text="language === 'en' ? 'Built with Laravel & Alpine.js' : 'Construido con Laravel y Alpine.js'"></span>
                    <span>&middot;</span>
                    <a href="https://github.com/akrista" class="hover:text-[var(--ink)] focus-ring-signature">[GitHub]</a>
                </div>
            </footer>
        </div>
    </body>
</html>
