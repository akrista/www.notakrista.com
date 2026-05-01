<div
    x-data="{
        open: false,
        trigger: null,
        toggle() {
            this.open = !this.open;
            if (this.open) {
                this.trigger = document.activeElement;
            }
        },
        close() {
            this.open = false;
            if (this.trigger) {
                this.trigger.focus();
            }
        }
    }"
    @click.outside="close()"
    @keydown.escape.window="close()"
    class="relative"
>
    <button
        type="button"
        @click="toggle()"
        class="inline-flex items-center gap-1.5 rounded-lg bg-[oklch(95%_0.005_260)] dark:bg-[oklch(25%_0.08_260)] px-2.5 py-2 sm:px-3 sm:py-2 text-xs font-semibold uppercase tracking-wider text-secondary-surface border border-border-light transition-colors hover:text-primary-surface"
        aria-haspopup="true"
        :aria-expanded="open.toString()"
        aria-label="{{ __('welcome.language') }}"
    >
        @if(app()->getLocale() === 'es')
            <span class="text-sm" aria-hidden="true">&#x1F1EA;&#x1F1F8;</span>
            <span>ES</span>
        @else
            <span class="text-sm" aria-hidden="true">&#x1F1FA;&#x1F1F8;</span>
            <span>EN</span>
        @endif
        <svg class="w-3 h-3 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    <div
        x-show="open"
        x-transition
        x-cloak
        class="absolute right-0 top-full mt-1"
        role="menu"
        aria-orientation="vertical"
    >
        <div class="rounded-lg bg-[oklch(95%_0.005_260)] dark:bg-[oklch(25%_0.08_260)] border border-border-light shadow-lg overflow-hidden">
            @if(app()->getLocale() !== 'en')
            <a
                href="{{ route('language-switcher.switch', ['code' => 'en']) }}"
                class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-secondary-surface hover:bg-brand-primary hover:text-primary-surface transition-colors"
                role="menuitem"
            >
                <span class="text-sm" aria-hidden="true">&#x1F1FA;&#x1F1F8;</span>
                {{ __('welcome.english') }}
            </a>
            @endif
            @if(app()->getLocale() !== 'es')
            <a
                href="{{ route('language-switcher.switch', ['code' => 'es']) }}"
                class="flex items-center gap-2 px-4 py-2.5 text-xs font-semibold text-secondary-surface hover:bg-brand-primary hover:text-primary-surface transition-colors"
                role="menuitem"
            >
                <span class="text-sm" aria-hidden="true">&#x1F1EA;&#x1F1F8;</span>
                {{ __('welcome.spanish') }}
            </a>
            @endif
        </div>
    </div>
</div>
