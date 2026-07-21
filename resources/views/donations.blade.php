<x-layouts::guest>
    @php
        $donationAccounts = $donationAccounts ?? collect();
        $snapshot = $snapshot ?? null;
        $targetGoal = $targetGoal ?? 350.00;
        $locales = ['en', 'es'];
        $translations = [];
        foreach ($locales as $loc) {
            $translations[$loc] = trans('donations', [], $loc);
        }
    @endphp

    <div class="w-full flex flex-col gap-10 md:gap-14" x-data="{
        trans: {{ Js::from($translations) }},
        t(key) {
            const keys = key.split('.');
            let val = this.trans[language];
            for (const k of keys) {
                val = val?.[k];
            }
            return val ?? key;
        }
    }">

        <!-- PAGE HEADER WITH GLITCH TITLE -->
        <div class="w-full border-b border-[var(--border)] pb-6">
            <h1 class="text-headline text-[var(--ink)] flex items-baseline gap-2">
                <span x-data="{
                    currentText: '',
                    isGlitching: false,
                    scrambleInterval: null,
                    flickerTimeout: null,
                    chars: '!@#$%^&*()_+-=[]{}|;:,./<>?░▒▓█▌▐▀▄01',
                    getOriginal() {
                        return t('title');
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
                        this.scramble(this.getOriginal());
                    },
                    init() {
                        this.currentText = this.getOriginal();
                        this.$watch('language', () => { this.cleanup(); this.currentText = this.getOriginal(); });
                    }
                }" @mouseenter="triggerGlitch()" :class="isGlitching ? 'glitch-hack-active' : ''"
                    class="inline-block cursor-default" x-text="currentText">{{ __('donations.title') }}</span>
            </h1>
            <p class="text-body text-sm text-[var(--muted)] mt-2" x-text="t('subtitle')">{{ __('donations.subtitle') }}
            </p>
        </div>

        <!-- 1. PEOPLE IN MORE NEED (LA GUAIRA & MORON) - ABSOLUTE TOP OF CONTENT -->
        <section class="w-full">
            <div
                class="alert-box border border-[var(--border)] flex flex-col gap-3 bg-[var(--surface)] relative overflow-hidden">
                <!-- Moss Aqua Accent Top Bar -->
                <div class="absolute top-0 left-0 right-0 h-1 bg-[var(--accent)]"></div>
                <div class="flex items-center gap-2">
                    <span class="text-lg">🤝</span>
                    <h2 class="text-title text-sm font-bold text-[var(--ink)]" x-text="t('others_need_title')">
                        {{ __('donations.others_need_title') }}</h2>
                </div>
                <p class="text-body text-xs text-[var(--muted)] leading-relaxed" x-text="t('others_need_body')">
                    {{ __('donations.others_need_body') }}</p>

                <!-- Tierra Viva bulletin — compact complement (bullets collapsed by default) -->
                <div class="mt-1 flex flex-col gap-1.5">
                    <p class="text-body text-xs text-[var(--ink)] leading-snug">
                        <span class="text-[var(--yellow)] font-bold" aria-hidden="true">●</span>
                        <span x-text="t('tierra_viva_preamble')">{{ __('donations.tierra_viva_preamble') }}</span>
                    </p>
                    <a href="https://www.instagram.com/p/Da5SpUQjiKa/?igsh=MXRjandhMTg3MmR2cA==" target="_blank"
                        rel="noopener"
                        class="text-body text-xs font-semibold text-[var(--accent)] hover:underline focus-ring-signature rounded self-start"
                        x-text="t('tierra_viva_instagram_label')">{{ __('donations.tierra_viva_instagram_label') }}</a>
                    <details
                        class="text-mono text-[11px] text-[var(--muted)] [&>summary]:cursor-pointer [&>summary]:list-none [&>summary]:hover:text-[var(--ink)] [&>summary]:focus-ring-signature [&>summary]:rounded">
                        <summary class="select-none" x-text="t('tierra_viva_toggle')">
                            {{ __('donations.tierra_viva_toggle') }}</summary>
                        <ul
                            class="mt-1.5 text-body text-[11px] text-[var(--muted)] leading-relaxed list-disc list-outside ml-4 space-y-0.5 marker:text-[var(--accent)]">
                            <li x-text="t('tierra_viva_bullets.families')">
                                {{ __('donations.tierra_viva_bullets.families') }}</li>
                            <li x-text="t('tierra_viva_bullets.volunteers')">
                                {{ __('donations.tierra_viva_bullets.volunteers') }}</li>
                            <li x-text="t('tierra_viva_bullets.allies')">
                                {{ __('donations.tierra_viva_bullets.allies') }}</li>
                            <li x-text="t('tierra_viva_bullets.platform')">
                                {{ __('donations.tierra_viva_bullets.platform') }}</li>
                        </ul>
                    </details>
                </div>

                <div class="flex flex-wrap items-center gap-2 mt-1">
                    <a href="https://www.mispidi.com/?id=tierravivavzla" target="_blank" rel="noopener"
                        class="button-cta bg-[var(--surface-raised)] border border-[var(--border)] text-[var(--accent)] hover:border-[var(--accent)] focus-ring-signature font-bold text-xs"
                        x-text="t('tierra_viva_mispidi_label')">{{ __('donations.tierra_viva_mispidi_label') }}</a>
                    <a href="https://unidos2give.org/fundacion-tierra-viva/" target="_blank" rel="noopener"
                        class="button-cta bg-[var(--surface-raised)] border border-[var(--border)] text-[var(--accent)] hover:border-[var(--accent)] focus-ring-signature font-bold text-xs"
                        x-text="t('tierra_viva_unidos2give_label')">{{ __('donations.tierra_viva_unidos2give_label') }}</a>
                    <a href="https://donarseguro.com/" target="_blank" rel="noopener"
                        class="button-cta bg-[var(--surface-raised)] border border-[var(--border)] text-[var(--accent)] hover:border-[var(--accent)] focus-ring-signature font-bold text-xs"
                        x-text="t('others_need_link_label')">{{ __('donations.others_need_link_label') }}</a>
                </div>
            </div>
        </section>

        <!-- 2. EMERGENCY APPEAL & SUPPORT PILLARS -->
        <section class="w-full grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">
            <!-- Left Column: Appeal (7 cols) -->
            <div class="lg:col-span-7">
                <div
                    class="alert-box flex flex-col gap-4 border border-[var(--red)] relative overflow-hidden bg-[var(--surface)] h-full justify-center">
                    <div class="absolute top-0 left-0 right-0 h-1 bg-[var(--red)]"></div>
                    <div class="flex items-center gap-3">
                        <span class="w-2.5 h-2.5 rounded-full bg-[var(--red)] inline-block"></span>
                        <h2 class="font-mono text-xs uppercase text-[var(--red)] font-semibold tracking-wider"
                            x-text="t('appeal_title')">{{ __('donations.appeal_title') }}</h2>
                    </div>
                    <p class="text-body text-sm leading-relaxed text-[var(--ink)]" x-text="t('appeal_body')">
                        {{ __('donations.appeal_body') }}</p>
                </div>
            </div>

            <!-- Right Column: Support Pillars (5 cols) -->
            <div class="lg:col-span-5 flex flex-col gap-4">
                <!-- VPS Server Pillar -->
                <div class="card-bench border border-[var(--border)] p-5 flex flex-col gap-2 bg-[var(--surface)]">
                    <div class="flex items-center gap-2 text-[var(--primary)]">
                        <span class="text-lg">🖥️</span>
                        <h3 class="text-title text-sm font-bold" x-text="t('vps_title')">{{ __('donations.vps_title') }}
                        </h3>
                    </div>
                    <p class="text-body text-xs text-[var(--muted)] leading-relaxed" x-text="t('vps_body')">
                        {{ __('donations.vps_body') }}</p>
                </div>
                <!-- Living Expense Pillar -->
                <div class="card-bench border border-[var(--border)] p-5 flex flex-col gap-2 bg-[var(--surface)]">
                    <div class="flex items-center gap-2 text-[var(--primary)]">
                        <span class="text-lg">🥖</span>
                        <h3 class="text-title text-sm font-bold" x-text="t('living_title')">
                            {{ __('donations.living_title') }}</h3>
                    </div>
                    <p class="text-body text-xs text-[var(--muted)] leading-relaxed" x-text="t('living_body')">
                        {{ __('donations.living_body') }}</p>
                </div>
            </div>
        </section>

        <!-- 3. ACTIVE SUPPORT CHANNELS (FULL-WIDTH 3-COLUMN GRID) -->
        <section class="w-full flex flex-col gap-6">
            <div class="flex flex-col gap-2 border-b border-[var(--border)] pb-4">
                <h2 class="text-headline text-lg font-bold text-[var(--ink)]" x-text="t('active_channels_title')">
                    {{ __('donations.active_channels_title') }}</h2>
                <p class="text-mono text-[10px] text-[var(--muted)]" x-text="t('active_channels_subtitle')">
                    {{ __('donations.active_channels_subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse ($donationAccounts as $account)
                    <div
                        class="card-bench border border-[var(--border)] rounded-lg p-5 flex flex-col justify-between gap-4 bg-[var(--surface)] group">
                        <div class="flex flex-col gap-4">
                            <!-- Card Header -->
                            <div class="flex justify-between items-center border-b border-[var(--border)] pb-3">
                                <span
                                    class="text-mono text-xs font-bold text-[var(--primary)] uppercase tracking-wider flex items-center gap-2">
                                    <!-- General Bank Line Icon -->
                                    <svg class="size-5 shrink-0 fill-none stroke-current text-[var(--muted)]"
                                        stroke-width="2" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z" />
                                    </svg>
                                    {{ $account->name }}
                                </span>
                                <span
                                    class="badge-chip bg-[var(--surface-raised)] border border-[var(--border)] text-mono text-[10px] text-[var(--ink)] uppercase font-semibold">
                                    {{ $account->currency }}
                                </span>
                            </div>

                            <!-- Instructions -->
                            @if ($account->donation_instructions)
                                <p
                                    class="text-body text-xs text-[var(--muted)] leading-relaxed bg-[var(--surface-raised)] p-3 rounded-md border border-[var(--border)]">
                                    {{ $account->donation_instructions }}
                                </p>
                            @endif

                            <!-- QR Code -->
                            @if ($account->donation_qr_image)
                                <div class="flex flex-col items-center gap-2 border-b border-[var(--border)] pb-4">
                                    <img src="{{ asset($account->donation_qr_image) }}" alt="{{ $account->name }} QR"
                                        class="w-40 h-40 rounded-md border border-[var(--border)] bg-white p-1.5 shrink-0"
                                        loading="lazy" />
                                    <span
                                        class="text-mono text-[9px] uppercase text-[var(--muted)] tracking-wider font-semibold"
                                        x-text="t('scan_to_pay')">{{ __('donations.scan_to_pay') }}</span>
                                </div>
                            @endif
                        </div>

                        <!-- Copyable Fields -->
                        <div class="flex flex-col gap-3">
                            <!-- Account Number -->
                            @if ($account->donation_account_number)
                                <div x-data="{ copied: false }" class="flex flex-col gap-1">
                                    <span class="text-mono text-[9px] uppercase tracking-wider text-[var(--muted)]"
                                        x-text="t('fields.account')">{{ __('donations.fields.account') }}</span>
                                    <div
                                        class="flex items-center gap-2 bg-[var(--surface-raised)] border border-[var(--border)] rounded-md px-3 py-1.5">
                                        <span
                                            class="text-mono text-xs text-[var(--ink)] select-all break-all flex-1">{{ $account->donation_account_number }}</span>
                                        <button type="button"
                                            @click="navigator.clipboard.writeText('{{ $account->donation_account_number }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                            class="text-mono text-[10px] px-2.5 py-1 rounded border border-[var(--border)] transition-colors focus-ring-signature flex items-center gap-1 hover:bg-[var(--surface)] shrink-0"
                                            :class="copied ? 'text-[var(--accent)] border-[var(--accent)]' : 'text-[var(--muted)]'">
                                            <span
                                                x-text="copied ? t('copied_button') : t('copy_button')">{{ __('donations.copy_button') }}</span>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- ABA -->
                            @if ($account->donation_aba)
                                <div x-data="{ copied: false }" class="flex flex-col gap-1">
                                    <span class="text-mono text-[9px] uppercase tracking-wider text-[var(--muted)]"
                                        x-text="t('fields.aba')">{{ __('donations.fields.aba') }}</span>
                                    <div
                                        class="flex items-center gap-2 bg-[var(--surface-raised)] border border-[var(--border)] rounded-md px-3 py-1.5">
                                        <span
                                            class="text-mono text-xs text-[var(--ink)] select-all break-all flex-1">{{ $account->donation_aba }}</span>
                                        <button type="button"
                                            @click="navigator.clipboard.writeText('{{ $account->donation_aba }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                            class="text-mono text-[10px] px-2.5 py-1 rounded border border-[var(--border)] transition-colors focus-ring-signature flex items-center gap-1 hover:bg-[var(--surface)] shrink-0"
                                            :class="copied ? 'text-[var(--accent)] border-[var(--accent)]' : 'text-[var(--muted)]'">
                                            <span
                                                x-text="copied ? t('copied_button') : t('copy_button')">{{ __('donations.copy_button') }}</span>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- SWIFT -->
                            @if ($account->donation_swift)
                                <div x-data="{ copied: false }" class="flex flex-col gap-1">
                                    <span class="text-mono text-[9px] uppercase tracking-wider text-[var(--muted)]"
                                        x-text="t('fields.swift')">{{ __('donations.fields.swift') }}</span>
                                    <div
                                        class="flex items-center gap-2 bg-[var(--surface-raised)] border border-[var(--border)] rounded-md px-3 py-1.5">
                                        <span
                                            class="text-mono text-xs text-[var(--ink)] select-all break-all flex-1">{{ $account->donation_swift }}</span>
                                        <button type="button"
                                            @click="navigator.clipboard.writeText('{{ $account->donation_swift }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                            class="text-mono text-[10px] px-2.5 py-1 rounded border border-[var(--border)] transition-colors focus-ring-signature flex items-center gap-1 hover:bg-[var(--surface)] shrink-0"
                                            :class="copied ? 'text-[var(--accent)] border-[var(--accent)]' : 'text-[var(--muted)]'">
                                            <span
                                                x-text="copied ? t('copied_button') : t('copy_button')">{{ __('donations.copy_button') }}</span>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- ID / Cedula -->
                            @if ($account->donation_id_cedula)
                                <div x-data="{ copied: false }" class="flex flex-col gap-1">
                                    <span class="text-mono text-[9px] uppercase tracking-wider text-[var(--muted)]"
                                        x-text="t('fields.id_cedula')">{{ __('donations.fields.id_cedula') }}</span>
                                    <div
                                        class="flex items-center gap-2 bg-[var(--surface-raised)] border border-[var(--border)] rounded-md px-3 py-1.5">
                                        <span
                                            class="text-mono text-xs text-[var(--ink)] select-all break-all flex-1">{{ $account->donation_id_cedula }}</span>
                                        <button type="button"
                                            @click="navigator.clipboard.writeText('{{ $account->donation_id_cedula }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                            class="text-mono text-[10px] px-2.5 py-1 rounded border border-[var(--border)] transition-colors focus-ring-signature flex items-center gap-1 hover:bg-[var(--surface)] shrink-0"
                                            :class="copied ? 'text-[var(--accent)] border-[var(--accent)]' : 'text-[var(--muted)]'">
                                            <span
                                                x-text="copied ? t('copied_button') : t('copy_button')">{{ __('donations.copy_button') }}</span>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- URL -->
                            @if ($account->donation_url)
                                <div x-data="{ copied: false }" class="flex flex-col gap-1">
                                    <span class="text-mono text-[9px] uppercase tracking-wider text-[var(--muted)]"
                                        x-text="t('fields.url')">{{ __('donations.fields.url') }}</span>
                                    <div
                                        class="flex items-center gap-2 bg-[var(--surface-raised)] border border-[var(--border)] rounded-md px-3 py-1.5">
                                        <a href="{{ $account->donation_url }}" target="_blank" rel="noopener"
                                            class="text-mono text-xs text-[var(--accent)] hover:underline break-all flex-1 focus-ring-signature rounded px-1">
                                            {{ $account->donation_url }} ↗
                                        </a>
                                        <button type="button"
                                            @click="navigator.clipboard.writeText('{{ $account->donation_url }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                            class="text-mono text-[10px] px-2.5 py-1 rounded border border-[var(--border)] transition-colors focus-ring-signature flex items-center gap-1 hover:bg-[var(--surface)] shrink-0"
                                            :class="copied ? 'text-[var(--accent)] border-[var(--accent)]' : 'text-[var(--muted)]'">
                                            <span
                                                x-text="copied ? t('copied_button') : t('copy_button')">{{ __('donations.copy_button') }}</span>
                                        </button>
                                    </div>
                                </div>
                            @endif

                            <!-- Address Fallback -->
                            @if ($account->donation_address && !$account->donation_account_number && !$account->donation_aba && !$account->donation_swift && !$account->donation_id_cedula && !$account->donation_url)
                                <div x-data="{ copied: false }" class="flex flex-col gap-1">
                                    <span class="text-mono text-[9px] uppercase tracking-wider text-[var(--muted)]"
                                        x-text="t('fields.address')">{{ __('donations.fields.address') }}</span>
                                    <div
                                        class="flex items-center gap-2 bg-[var(--surface-raised)] border border-[var(--border)] rounded-md px-3 py-1.5">
                                        <span
                                            class="text-mono text-xs text-[var(--ink)] select-all break-all flex-1">{{ $account->donation_address }}</span>
                                        <button type="button"
                                            @click="navigator.clipboard.writeText('{{ $account->donation_address }}'); copied = true; setTimeout(() => copied = false, 1500)"
                                            class="text-mono text-[10px] px-2.5 py-1 rounded border border-[var(--border)] transition-colors focus-ring-signature flex items-center gap-1 hover:bg-[var(--surface)] shrink-0"
                                            :class="copied ? 'text-[var(--accent)] border-[var(--accent)]' : 'text-[var(--muted)]'">
                                            <span
                                                x-text="copied ? t('copied_button') : t('copy_button')">{{ __('donations.copy_button') }}</span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <p class="text-mono text-xs text-[var(--muted)]" x-text="t('no_channels')">
                        {{ __('donations.no_channels') }}</p>
                @endforelse
            </div>
        </section>

        <!-- 4. BUDGET TRANSPARENCY SECTION -->
        <section class="w-full border-t border-[var(--border)] pt-10 flex flex-col gap-6">
            <div class="flex flex-col gap-2">
                <h2 class="text-headline text-base font-bold text-[var(--ink)]" x-text="t('budget_transparency_title')">
                    {{ __('donations.budget_transparency_title') }}</h2>
                <p class="text-body text-xs text-[var(--muted)] leading-relaxed"
                    x-text="t('budget_transparency_subtitle')">{{ __('donations.budget_transparency_subtitle') }}</p>
            </div>

            @php
                $income = $snapshot !== null ? (float) $snapshot['totals']['income'] : 0.0;
                $spent = $snapshot !== null ? (float) $snapshot['totals']['spent'] : 0.0;
                $net = $income - $spent;
                $currency = $snapshot !== null ? $snapshot['display_currency'] : 'USD';
                $pct = $targetGoal > 0 ? min(100, ($income / $targetGoal) * 100) : 100;
                $remaining = max(0, $targetGoal - $income);
            @endphp

            <!-- Goal Tracker Bar -->
            <div class="card-bench flex flex-col gap-4 p-5 bg-[var(--surface)]">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-2">
                    <div class="flex flex-col">
                        <span class="text-title text-sm font-bold text-[var(--ink)]"
                            x-text="t('goal_title')">{{ __('donations.goal_title') }}</span>
                        <span class="text-body text-xs text-[var(--muted)]"
                            x-text="t('goal_subtitle')">{{ __('donations.goal_subtitle') }}</span>
                    </div>
                    <div class="font-mono text-xs text-right">
                        @if ($income >= $targetGoal)
                            <span class="text-[var(--accent)] font-semibold"
                                x-text="t('goal_surplus')">{{ __('donations.goal_surplus') }}</span>
                        @else
                            <span class="text-[var(--ink)]"><strong
                                    class="text-sm font-bold">{{ number_format($remaining, 2) }} {{ $currency }}</strong>
                                <span x-text="t('goal_needed')">{{ __('donations.goal_needed') }}</span></span>
                        @endif
                    </div>
                </div>

                <!-- Progress Track Bar -->
                <div
                    class="w-full bg-[var(--surface-raised)] border border-[var(--border)] rounded-full h-3.5 overflow-hidden p-[2px]">
                    <div class="bg-[var(--accent)] h-full rounded-full transition-all duration-500"
                        style="width: {{ $pct }}%"></div>
                </div>

                <div class="flex justify-between items-center font-mono text-[10px] text-[var(--muted)]">
                    <span>0%</span>
                    <span class="text-[var(--ink)] font-semibold">{{ number_format($income, 2) }} {{ $currency }} <span
                            x-text="t('goal_accomplished')">{{ __('donations.goal_accomplished') }}</span>
                        ({{ round($pct) }}%)</span>
                    <span>Goal: {{ number_format($targetGoal, 2) }} {{ $currency }}</span>
                </div>
            </div>

            @if ($snapshot === null || ($income === 0.0 && $spent === 0.0))
                <p class="text-mono text-xs text-[var(--muted)] italic p-4 border border-[var(--border)] border-dashed rounded-lg text-center"
                    x-text="t('no_transactions')">
                    {{ __('donations.no_transactions') }}
                </p>
            @else
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="card-bench flex flex-col gap-1.5 rounded-lg bg-[var(--surface)] p-5">
                        <span class="text-mono text-[10px] uppercase font-bold text-[var(--muted)]"
                            x-text="t('net')">{{ __('donations.net') }}</span>
                        <span @class([
                            'font-mono text-2xl font-bold',
                            'text-[var(--accent)]' => $net > 0,
                            'text-[var(--red)]' => $net < 0,
                            'text-[var(--ink)]' => $net === 0.0,
                        ])>
                            {{ number_format($net, 2) }} {{ $currency }}
                        </span>
                    </div>
                    <div class="card-bench flex flex-col gap-1.5 rounded-lg bg-[var(--surface)] p-5">
                        <span class="text-mono text-[10px] uppercase font-bold text-[var(--muted)]"
                            x-text="t('income')">{{ __('donations.income') }}</span>
                        <span class="font-mono text-2xl font-bold text-[var(--ink)]">
                            {{ number_format($income, 2) }} {{ $currency }}
                        </span>
                    </div>
                    <div class="card-bench flex flex-col gap-1.5 rounded-lg bg-[var(--surface)] p-5">
                        <span class="text-mono text-[10px] uppercase font-bold text-[var(--muted)]"
                            x-text="t('spent')">{{ __('donations.spent') }}</span>
                        <span class="font-mono text-2xl font-bold text-[var(--ink)]">
                            {{ number_format($spent, 2) }} {{ $currency }}
                        </span>
                    </div>
                </div>

                <!-- Categories Breakdown & Last 3 Months -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Categories -->
                    @if (($snapshot['categories'] ?? collect())->isNotEmpty())
                        <div class="card-bench flex flex-col gap-3 p-5 bg-[var(--surface)]">
                            <h3 class="text-mono text-[10px] uppercase tracking-wider text-[var(--primary)] font-bold"
                                x-text="t('by_category')">{{ __('donations.by_category') }}</h3>
                            <div class="flex flex-col divide-y divide-[var(--border)]">
                                @foreach ($snapshot['categories'] as $cat)
                                    <div class="grid grid-cols-12 items-center gap-2 py-2 font-mono text-xs">
                                        <span class="col-span-6 text-[var(--ink)]">
                                            <span>{{ $cat['icon'] ?? '🏷️' }}</span>
                                            {{ $cat['name'] }}
                                        </span>
                                        <span class="col-span-6 text-right font-semibold text-[var(--ink)]">
                                            {{ number_format((float) $cat['spent'], 2) }} {{ $currency }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- History -->
                    <div class="card-bench flex flex-col gap-3 p-5 bg-[var(--surface)]">
                        <h3 class="text-mono text-[10px] uppercase tracking-wider text-[var(--primary)] font-bold"
                            x-text="t('last_3_months')">{{ __('donations.last_3_months') }}</h3>
                        <div class="flex flex-col gap-2 font-mono text-xs">
                            @foreach ($snapshot['previous_months'] as $prev)
                                <div
                                    class="grid grid-cols-12 gap-2 border-b border-[var(--border)] last:border-0 pb-1.5 last:pb-0">
                                    <span class="col-span-4 text-[var(--muted)]">{{ $prev['label'] }}</span>
                                    <span class="col-span-4 text-[var(--ink)] text-right md:text-left">
                                        <span class="text-[var(--muted)] font-normal"
                                            x-text="t('income')">{{ __('donations.income') }}</span>:
                                        {{ number_format((float) $prev['income'], 2) }} {{ $currency }}
                                    </span>
                                    <span class="col-span-4 text-[var(--ink)] text-right">
                                        <span class="text-[var(--muted)] font-normal"
                                            x-text="t('spent')">{{ __('donations.spent') }}</span>:
                                        {{ number_format((float) $prev['spent'], 2) }} {{ $currency }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </section>

        <!-- 5. GRATITUDE, BLOG & DIRECT CONTACT (FULL-WIDTH 3-COLUMN GRID) -->
        <section class="w-full border-t border-[var(--border)] pt-10 grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Card 1: Blog -->
            <div
                class="card-bench border border-[var(--border)] p-5 flex flex-col justify-between gap-4 bg-[var(--surface)]">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-2 text-[var(--primary)]">
                        <span class="text-lg">📖</span>
                        <h2 class="text-title text-sm font-bold text-[var(--ink)]" x-text="t('blog_title')">
                            {{ __('donations.blog_title') }}</h2>
                    </div>
                    <p class="text-body text-xs text-[var(--muted)] leading-relaxed" x-text="t('blog_body')">
                        {{ __('donations.blog_body') }}</p>
                </div>
                <a href="https://rockery.notakrista.com" target="_blank" rel="noopener"
                    class="font-mono text-xs text-[var(--accent)] hover:underline mt-1 focus-ring-signature rounded self-start">
                    rockery.notakrista.com ↗
                </a>
            </div>

            <!-- Card 2: Gratitude Tiers -->
            <div class="card-bench border border-[var(--border)] p-5 flex flex-col gap-2 bg-[var(--surface)]">
                <div class="flex items-center gap-2 text-[var(--primary)]">
                    <span class="text-lg">🎁</span>
                    <h2 class="text-title text-sm font-bold text-[var(--ink)]" x-text="t('gratitude_title')">
                        {{ __('donations.gratitude_title') }}</h2>
                </div>
                <p class="text-body text-xs text-[var(--muted)] leading-relaxed" x-text="t('gratitude_body')">
                    {{ __('donations.gratitude_body') }}</p>
            </div>

            <!-- Card 3: Direct Contact -->
            <div
                class="card-bench border border-[var(--border)] p-5 flex flex-col justify-between gap-4 bg-[var(--surface)]">
                <div class="flex flex-col gap-2">
                    <div class="flex items-center gap-2 text-[var(--primary)]">
                        <span class="text-lg">✉️</span>
                        <h2 class="text-title text-sm font-bold text-[var(--ink)]" x-text="t('contact_title')">
                            {{ __('donations.contact_title') }}</h2>
                    </div>
                    <p class="text-body text-xs text-[var(--muted)] leading-relaxed" x-text="t('contact_body')">
                        {{ __('donations.contact_body') }}</p>
                </div>
                <div class="flex flex-wrap gap-2 mt-1">
                    <a href="mailto:info@notakrista.com"
                        class="badge-chip bg-[var(--surface-raised)] hover:bg-[var(--surface)] border border-[var(--border)] text-mono text-[9px] text-[var(--accent)] transition-all focus-ring-signature">Email</a>
                    <a href="https://wa.me/584142034875" target="_blank"
                        class="badge-chip bg-[var(--surface-raised)] hover:bg-[var(--surface)] border border-[var(--border)] text-mono text-[9px] text-[var(--accent)] transition-all focus-ring-signature">WhatsApp</a>
                    <a href="https://t.me/Akrista" target="_blank"
                        class="badge-chip bg-[var(--surface-raised)] hover:bg-[var(--surface)] border border-[var(--border)] text-mono text-[9px] text-[var(--accent)] transition-all focus-ring-signature">Telegram</a>
                </div>
            </div>
        </section>

        <!-- 6. WISHLIST SECTION (FULL-WIDTH 3-COLUMN GRID AT ABSOLUTE BOTTOM) -->
        {{-- <section class="w-full border-t border-[var(--border)] pt-10 flex flex-col gap-6 pb-6">
            <div class="flex flex-col gap-2">
                <h2 class="text-headline text-base font-bold text-[var(--ink)]" x-text="t('wishlist_title')">{{
                    __('donations.wishlist_title') }}</h2>
                <p class="text-body text-xs text-[var(--muted)] leading-relaxed" x-text="t('wishlist_subtitle')">{{
                    __('donations.wishlist_subtitle') }}</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Item 1 -->
                <div
                    class="card-bench border border-[var(--border)] p-5 flex flex-col justify-between gap-4 bg-[var(--surface)]">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-[var(--primary)]">
                            <span class="text-base">⌨️</span>
                            <h3 class="text-title text-sm font-bold" x-text="t('wishlist_items.item1_title')">{{
                                __('donations.wishlist_items.item1_title') }}</h3>
                        </div>
                        <p class="text-body text-xs text-[var(--muted)] leading-relaxed"
                            x-text="t('wishlist_items.item1_desc')">{{ __('donations.wishlist_items.item1_desc') }}</p>
                    </div>
                    <a href="https://www.amazon.com/hz/wishlist/ls" target="_blank" rel="noopener"
                        class="text-mono text-[10px] font-bold text-[var(--accent)] hover:underline flex items-center gap-1 focus-ring-signature rounded self-start mt-2">
                        Amazon List ↗
                    </a>
                </div>

                <!-- Item 2 -->
                <div
                    class="card-bench border border-[var(--border)] p-5 flex flex-col justify-between gap-4 bg-[var(--surface)]">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-[var(--primary)]">
                            <span class="text-base">📚</span>
                            <h3 class="text-title text-sm font-bold" x-text="t('wishlist_items.item2_title')">{{
                                __('donations.wishlist_items.item2_title') }}</h3>
                        </div>
                        <p class="text-body text-xs text-[var(--muted)] leading-relaxed"
                            x-text="t('wishlist_items.item2_desc')">{{ __('donations.wishlist_items.item2_desc') }}</p>
                    </div>
                    <a href="https://www.amazon.com/hz/wishlist/ls" target="_blank" rel="noopener"
                        class="text-mono text-[10px] font-bold text-[var(--accent)] hover:underline flex items-center gap-1 focus-ring-signature rounded self-start mt-2">
                        Amazon List ↗
                    </a>
                </div>

                <!-- Item 3 -->
                <div
                    class="card-bench border border-[var(--border)] p-5 flex flex-col justify-between gap-4 bg-[var(--surface)]">
                    <div class="flex flex-col gap-2">
                        <div class="flex items-center gap-2 text-[var(--primary)]">
                            <span class="text-base">💾</span>
                            <h3 class="text-title text-sm font-bold" x-text="t('wishlist_items.item3_title')">{{
                                __('donations.wishlist_items.item3_title') }}</h3>
                        </div>
                        <p class="text-body text-xs text-[var(--muted)] leading-relaxed"
                            x-text="t('wishlist_items.item3_desc')">{{ __('donations.wishlist_items.item3_desc') }}</p>
                    </div>
                    <a href="https://www.amazon.com/hz/wishlist/ls" target="_blank" rel="noopener"
                        class="text-mono text-[10px] font-bold text-[var(--accent)] hover:underline flex items-center gap-1 focus-ring-signature rounded self-start mt-2">
                        Amazon List ↗
                    </a>
                </div>
            </div>

            <!-- Global Wishlist Button -->
            <a href="https://www.amazon.com/hz/wishlist/ls" target="_blank" rel="noopener"
                class="self-center button-cta bg-[var(--surface-raised)] border border-[var(--border)] text-[var(--accent)] hover:border-[var(--accent)] focus-ring-signature font-bold text-xs px-6 py-2.5 mt-2 transition-all"
                x-text="t('wishlist_view_all')">
                {{ __('donations.wishlist_view_all') }}
            </a>
        </section> --}}

    </div>
</x-layouts::guest>
