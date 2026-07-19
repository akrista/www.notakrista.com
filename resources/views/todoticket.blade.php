<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Todoticket Calculator — Jorge Thomas</title>

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

            /* Monospace Terminal Ledger styles */
            .terminal-window {
                background-color: var(--bg);
                border: 1px solid var(--border);
                border-radius: 12px;
                padding: 24px;
                font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace;
                transition: background-color 0.3s ease, border-color 0.3s ease;
            }
            .light .terminal-window {
                background-color: var(--surface);
            }

            .terminal-header {
                border-bottom: 1px dashed var(--border);
                padding-bottom: 12px;
                margin-bottom: 16px;
            }

            .terminal-row {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 8px 0;
                border-bottom: 1px solid var(--border);
            }
            .terminal-row:last-child {
                border-bottom: none;
            }

            /* Blinking cursor for empty state */
            @keyframes blink {
                0%, 100% { opacity: 1; }
                50% { opacity: 0; }
            }
            .cursor-blink {
                animation: blink 1s step-end infinite;
            }

            /* Reduce Motion */
            @media (prefers-reduced-motion: reduce) {
                .transition-theme,
                .transition-all,
                .cursor-blink {
                    transition: none !important;
                    animation: none !important;
                }
            }
        </style>
    </head>
    <body class="antialiased">
        <div
            x-data="{
                mode: localStorage.getItem('mode') || 'business',
                language: (navigator.language || navigator.userLanguage || 'en').startsWith('es') ? 'es' : 'en',
                theme: localStorage.getItem('theme') || 'system',
                systemDark: window.matchMedia('(prefers-color-scheme: dark)').matches,
                init() {
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
                <a href="{{ route('home') }}" class="flex items-center gap-3 group focus-ring-signature rounded-md">
                    <img src="{{ asset('logo-circle.png') }}" class="size-10 object-contain rounded-full border border-[var(--border)] transition-theme" alt="Logo">
                    <div class="flex flex-col items-center sm:items-start">
                        <span class="text-xl font-bold tracking-tight text-[var(--primary)] font-sans">nk.</span>
                        <span class="text-mono text-xs text-[var(--muted)]">notakrista.com</span>
                    </div>
                </a>

                <!-- Control Switchers -->
                <div class="flex items-center justify-center sm:justify-end gap-4 w-full sm:w-auto">
                    <!-- Language Switcher -->
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

                    <!-- Theme Switcher -->
                    <button 
                        type="button"
                        @click="cycleTheme()"
                        class="interactive-link focus-ring-signature rounded-full p-2 text-[var(--muted)] hover:text-[var(--ink)] hover:bg-[var(--surface-raised)] transition-all flex items-center justify-center shrink-0 border border-[var(--border)]"
                        :aria-label="language === 'en' ? 'Toggle theme' : 'Cambiar tema'"
                    >
                        <svg x-show="theme === 'light'" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M12 7c-2.76 0-5 2.24-5 5s2.24 5 5 5 5-2.24 5-5-2.24-5-5-5zM2 13h2c.55 0 1-.45 1-1s-.45-1-1-1H2c-.55 0-1 .45-1 1s.45 1 1 1zm18 0h2c.55 0 1-.45 1-1s-.45-1-1-1h-2c-.55 0-1 .45-1 1s.45 1 1 1zM11 2v2c0 .55.45 1 1 1s1-.45 1-1V2c0-.55-.45-1-1-1s-1 .45-1 1zm0 18v2c0 .55.45 1 1 1s1-.45 1-1v-2c0-.55-.45-1-1-1s-1 .45-1 1zM5.99 4.58a.996.996 0 0 0-1.41 0 .996.996 0 0 0 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41L5.99 4.58zm12.37 12.37a.996.996 0 0 0-1.41 0 .996.996 0 0 0 0 1.41l1.06 1.06c.39.39 1.03.39 1.41 0s.39-1.03 0-1.41l-1.06-1.06zm1.06-10.96a.996.996 0 0 0 0-1.41.996.996 0 0 0-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06zM7.05 18.01a.996.996 0 0 0 0-1.41.996.996 0 0 0-1.41 0l-1.06 1.06c-.39.39-.39 1.03 0 1.41s1.03.39 1.41 0l1.06-1.06z"/></svg>
                        <svg x-show="theme === 'dark'" class="w-4.5 h-4.5 fill-current" viewBox="0 0 24 24"><path d="M12.3 22h-.1c-5.5 0-10-4.5-10-10 0-4.8 3.5-9 8.3-9.9.5-.1.9.3.8.8-.4 1.5-.2 3.2.7 4.7 1 1.7 2.8 2.7 4.7 2.7.5 0 .9.2 1 .7.4 1.8 1.4 3.4 2.8 4.5.3.3.4.6.3 1-.9 3.1-3.7 5.4-7.2 5.5z"/></svg>
                        <svg x-show="theme === 'system'" class="w-4 h-4 fill-current" viewBox="0 0 24 24"><path d="M20 18c1.1 0 1.99-.9 1.99-2L22 6c0-1.1-.9-2-2-2H4c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2H0v2h24v-2h-4zM4 6h16v10H4V6z"/></svg>
                    </button>
                </div>
            </header>

            <!-- MAIN BENCH INSTRUMENT LAYOUT -->
            <main class="w-full max-w-6xl mx-auto flex-1 py-4" x-data="{
                total: null,
                retiroOptimo: 0,
                comision: 0,
                restante: 0,
                rawInput: '',

                handleInput(e) {
                    let inputValue = e.target.value.trim();
                    inputValue = inputValue.replace(/[^\d.,]/g, '');
                    let normalizedValue = inputValue;

                    const dots = (normalizedValue.match(/\./g) || []).length;
                    const commas = (normalizedValue.match(/,/g) || []).length;

                    if (dots > 1 && normalizedValue.includes(',')) {
                        normalizedValue = normalizedValue.replace(/\./g, '').replace(',', '.');
                    } else if (commas > 1 && normalizedValue.includes('.')) {
                        normalizedValue = normalizedValue.replace(/,/g, '');
                    } else if (normalizedValue.includes(',') && !normalizedValue.includes('.')) {
                        normalizedValue = normalizedValue.replace(',', '.');
                    }

                    const val = parseFloat(normalizedValue);
                    this.total = isNaN(val) || val < 0 ? null : val;
                    this.calculate();
                },

                applyPreset(amount) {
                    this.total = amount;
                    this.calculate();
                    this.$refs.totalInput.value = amount;
                },

                clearAll() {
                    this.total = null;
                    this.retiroOptimo = 0;
                    this.comision = 0;
                    this.restante = 0;
                    this.$refs.totalInput.value = '';
                },

                calculate() {
                    if (this.total === null || this.total <= 0) {
                        this.retiroOptimo = 0;
                        this.comision = 0;
                        this.restante = 0;
                        return;
                    }

                    let retiroOptimo = Math.floor((this.total / 1.006) * 100) / 100;
                    let comision = 0;
                    let restante = 0;

                    let iterations = 0;
                    const maxIterations = 10;

                    while (iterations < maxIterations) {
                        comision = Math.round(retiroOptimo * 0.006 * 100) / 100;
                        restante = this.total - (comision + retiroOptimo);
                        restante = Math.round(restante * 100) / 100;

                        if (restante <= 0 || Math.abs(restante) < 0.001) {
                            restante = 0;
                            break;
                        }
                        if (Object.is(restante, -0)) {
                            restante = 0;
                            break;
                        }

                        const nuevoRetiroOptimo = retiroOptimo + restante;
                        let nuevoRetiroOptimoRedondeado = Math.floor(nuevoRetiroOptimo * 100) / 100;

                        if (nuevoRetiroOptimoRedondeado <= retiroOptimo && restante > 0) {
                            nuevoRetiroOptimoRedondeado = Math.ceil(nuevoRetiroOptimo * 100) / 100;
                        }

                        retiroOptimo = nuevoRetiroOptimoRedondeado;
                        iterations++;
                    }

                    comision = Math.round(retiroOptimo * 0.006 * 100) / 100;
                    restante = this.total - (comision + retiroOptimo);
                    restante = Math.round(restante * 100) / 100;

                    if (retiroOptimo + comision > this.total) {
                        retiroOptimo = Math.round((retiroOptimo - 0.01) * 100) / 100;
                        comision = Math.round(retiroOptimo * 0.006 * 100) / 100;
                        restante = this.total - (comision + retiroOptimo);
                        restante = Math.round(restante * 100) / 100;
                    }

                    if (restante <= 0 || Math.abs(restante) < 0.001) {
                        restante = 0;
                    }
                    if (Object.is(restante, -0)) {
                        restante = 0;
                    }

                    this.retiroOptimo = retiroOptimo;
                    this.comision = comision;
                    this.restante = restante;
                },

                formatCurrency(value) {
                    const normalized = value === 0 ? 0 : value;
                    return new Intl.NumberFormat(language === 'es' ? 'es-VE' : 'en-US', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    }).format(normalized);
                }
            }">
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12 items-start">
                    
                    <!-- HEADER -->
                    <div class="col-span-1 lg:col-span-12 order-1 flex flex-col gap-2">
                        <h1 class="text-headline text-[var(--ink)]" x-text="language === 'en' ? 'todoticket calculator' : 'calculadora todoticket'"></h1>
                        <p class="text-body text-sm text-[var(--muted)]" x-text="language === 'en' ? 'Optimize local voucher withdrawals and commissions.' : 'Optimiza retiros y comisiones de tickets alimenticios locales.'"></p>
                    </div>

                    <!-- INPUT DECK -->
                    <div class="col-span-1 lg:col-span-7 order-2 card-bench flex flex-col gap-4">
                        <div class="flex flex-col gap-2">
                            <label for="total" class="text-label text-[var(--ink)]" x-text="language === 'en' ? 'Total Available' : 'Total Disponible'"></label>
                            <div class="relative w-full">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-[var(--muted)] font-mono text-lg select-none">Bs.</span>
                                <input
                                    x-ref="totalInput"
                                    id="total"
                                    type="text"
                                    inputmode="decimal"
                                    placeholder="0,00"
                                    @input="handleInput"
                                    class="w-full bg-[var(--bg)] border border-[var(--border)] rounded-md py-3 pl-12 pr-4 text-lg text-[var(--ink)] focus:outline-none focus:ring-signature focus:border-[var(--primary)] font-mono transition-theme"
                                />
                            </div>
                        </div>

                        <!-- Tactile Preset Keys -->
                        <div class="flex flex-wrap gap-2 pt-2">
                            <template x-for="preset in [100, 500, 1000, 5000]">
                                <button
                                    type="button"
                                    @click="applyPreset(preset)"
                                    class="font-mono text-xs border border-[var(--border)] hover:border-[var(--primary)] hover:bg-[var(--surface-raised)] rounded px-3 py-1.5 transition-all text-[var(--muted)] hover:text-[var(--ink)] cursor-pointer focus-ring-signature"
                                >
                                    +Bs. <span x-text="preset"></span>
                                </button>
                            </template>
                            <button
                                    type="button"
                                    @click="clearAll()"
                                    class="font-mono text-xs border border-[var(--border)] hover:border-[var(--red)] hover:bg-[var(--red)]/10 rounded px-3 py-1.5 transition-all text-[var(--muted)] hover:text-[var(--red)] cursor-pointer focus-ring-signature"
                                    x-text="language === 'en' ? 'Clear' : 'Limpiar'"
                            ></button>
                        </div>
                    </div>

                    <!-- INTERACTIVE LEDGER (RESULTS) -->
                    <div class="col-span-1 lg:col-span-5 lg:row-span-2 order-3 flex flex-col gap-6">
                        <!-- Empty State (No amount entered) -->
                        <div x-show="total === null || total <= 0" class="terminal-window flex flex-col items-center justify-center py-20 text-center text-mono text-sm text-[var(--muted)]">
                            <p class="mb-2 font-bold text-[var(--primary)]">&gt; AWAITING INPUT_</p>
                            <p class="text-xs max-w-xs" x-text="language === 'en' ? 'Enter an available balance in the control deck to fetch statement.' : 'Ingrese el saldo disponible en el panel de control para obtener el estado.'"></p>
                        </div>

                        <!-- Active Ledger State -->
                        <div x-show="total !== null && total > 0" class="terminal-window">
                            <!-- Terminal Header -->
                            <div class="terminal-header flex justify-between items-center text-xs text-[var(--muted)]">
                                <span class="font-bold uppercase tracking-wider text-label" x-text="language === 'en' ? 'Account Statement' : 'Estado de Cuenta'"></span>
                                <span>nk-tty0</span>
                            </div>

                            <!-- Ledger Rows -->
                            <div class="flex flex-col gap-2">
                                <div class="terminal-row">
                                    <span class="text-[var(--muted)] uppercase tracking-wider text-xs" x-text="language === 'en' ? 'Total Balance' : 'Balance Total'"></span>
                                    <span class="text-[var(--ink)] font-bold text-base">Bs. <span x-text="formatCurrency(total)"></span></span>
                                </div>
                                <div class="terminal-row">
                                    <span class="text-[var(--muted)] uppercase tracking-wider text-xs" x-text="language === 'en' ? 'Commission (0.6%)' : 'Comisión (0.6%)'"></span>
                                    <span class="text-[var(--red)] font-semibold">- Bs. <span x-text="formatCurrency(comision)"></span></span>
                                </div>
                                <div class="terminal-row py-3 bg-[var(--surface-raised)] light:bg-[var(--bg)] px-3 rounded-md border-y border-[var(--border)] transition-theme">
                                    <span class="text-[var(--ink)] font-bold uppercase tracking-wider text-xs" x-text="language === 'en' ? 'Optimal Withdrawal' : 'Retiro Óptimo'"></span>
                                    <span class="text-[var(--accent)] font-bold text-lg">Bs. <span x-text="formatCurrency(retiroOptimo)"></span></span>
                                </div>
                                <div class="terminal-row">
                                    <span class="text-[var(--muted)] uppercase tracking-wider text-xs" x-text="language === 'en' ? 'Remaining' : 'Restante'"></span>
                                    <span class="font-bold" :class="restante > 0 ? 'text-[var(--blue)]' : 'text-[var(--muted)]'">
                                        Bs. <span x-text="formatCurrency(restante)"></span>
                                    </span>
                                </div>
                            </div>

                            <!-- Diagnostic/Alert Log Line -->
                            <div class="mt-6" x-show="restante > 0">
                                <div class="border border-[var(--yellow)] text-[var(--yellow)] px-3 py-2 rounded-md flex items-center gap-2 bg-[var(--yellow)]/5 text-xs">
                                    <span class="font-bold">⚠</span>
                                    <span x-text="language === 'en' ? 'Optimized limit hit. Small remainder will be left.' : 'Límite óptimo alcanzado. Quedará saldo restante.'"></span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TECHNICAL SPECS -->
                    <div class="col-span-1 lg:col-span-7 order-4 bg-[var(--surface)] border border-[var(--border)] rounded-lg p-6 transition-theme flex flex-col gap-4">
                        <div>
                            <h3 class="font-semibold text-xs text-[var(--ink)] mb-3 uppercase tracking-wide text-label" x-text="language === 'en' ? 'Specifications' : 'Especificaciones'"></h3>
                            <ul class="text-xs text-[var(--muted)] space-y-2 list-disc list-inside">
                                <li x-text="language === 'en' ? 'Cruises your total balance against the 0.6% card commission.' : 'Cruza tu balance total frente a la comisión del 0.6% de la tarjeta.'"></li>
                                <li x-text="language === 'en' ? 'Runs standard iteration algorithm to minimize leftover pocket balances.' : 'Ejecuta un algoritmo de iteración para minimizar el saldo restante en cuenta.'"></li>
                                <li x-text="language === 'en' ? 'Ensures maximum possible cash-out without exceeding available funds.' : 'Asegura el mayor retiro posible sin exceder los fondos disponibles.'"></li>
                            </ul>
                        </div>

                        <div class="border-t border-[var(--border)] pt-4">
                            <h4 class="font-semibold text-[10px] text-[var(--ink)] mb-2 uppercase tracking-wider text-label" x-text="language === 'en' ? '💡 Decimal Adjustment Tip' : '💡 Consejo de Ajuste Decimal'"></h4>
                            <p class="text-xs text-[var(--muted)] leading-relaxed" x-text="language === 'en' 
                                    ? 'Since 0.6% calculations generate infinite fractions, and the platform only accepts exactly 2 decimal values, minor remaining balances (Bs. 0.01 or Bs. 0.02) are normal. If a tiny remainder is left, try manually adding Bs. 0.01 or more to your Optimal Withdrawal in the Todoticket portal.' 
                                    : 'Dado que los cálculos del 0.6% generan fracciones infinitas y la plataforma solo acepta exactamente 2 decimales, es normal ver saldos restantes de Bs. 0.01 o Bs. 0.02. Si esto ocurre, intenta sumar manualmente Bs. 0.01 o más al Retiro Óptimo en el portal de Todoticket.'"></p>
                        </div>
                    </div>

                    <!-- DISCLOSURE -->
                    <div class="col-span-1 lg:col-span-7 order-5 text-[10px] text-[var(--muted)] leading-relaxed italic px-2">
                        <p x-show="language === 'en'">
                            * This calculator is an independent utility and is not affiliated with, authorized, or endorsed by Todoticket Venezuela. It is created to assist workers with their biweekly pay-out workflow when logging into the <a href="https://mi.todoticketve.com/login" target="_blank" class="text-[var(--primary)] hover:underline">Todoticket Portal (mi.todoticketve.com)</a>.
                        </p>
                        <p x-show="language === 'es'">
                            * Esta calculadora es una herramienta independiente y no está afiliada, autorizada ni respaldada por Todoticket Venezuela. Se creó para ayudar a los trabajadores con su flujo de cobro quincenal al ingresar al <a href="https://mi.todoticketve.com/login" target="_blank" class="text-[var(--primary)] hover:underline">Portal Todoticket (mi.todoticketve.com)</a>.
                        </p>
                    </div>

                </div>
            </main>

            <!-- FOOTER -->
            <footer class="mt-12 py-6 text-center text-xs text-[var(--muted)] border-t border-[var(--border)] max-w-6xl w-full mx-auto flex flex-col sm:flex-row justify-between items-center gap-4">
                <div class="flex flex-col sm:items-start gap-1">
                    <p x-text="language === 'en' ? 'Calculadora Todoticket — Local voucher optimizer' : 'Calculadora Todoticket — Optimizador local de vales'"></p>
                    <p class="text-[10px] text-[var(--muted)] opacity-80">
                        <span x-text="language === 'en' ? 'Originally hosted at' : 'Originalmente alojado en'"></span>
                        <a href="https://github.com/akrista/todoticket.notakrista.com" target="_blank" class="text-[var(--primary)] hover:underline">github.com/akrista/todoticket.notakrista.com</a>
                    </p>
                </div>
                <p>
                    <span x-text="language === 'en' ? 'Made by' : 'Hecho por'"></span>
                    <a href="{{ route('home') }}" class="text-[var(--primary)] hover:underline">Akrista</a>
                </p>
            </footer>
        </div>
    </body>
</html>
