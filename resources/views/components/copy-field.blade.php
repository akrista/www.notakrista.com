@props([
    'value',
    'display' => null,
    'label' => null,
    'url' => null,
    'monoSize' => 'text-sm',
])

@php
    $shown = $display ?? $value;
@endphp

<div x-data="{ copied: false }" {{ $attributes->merge(['class' => 'flex flex-col gap-1']) }}>
    @if ($label)
        <span class="text-mono text-[9px] uppercase text-[var(--muted)] tracking-wider">
            @if (is_array($label))
                <span x-show="language === 'en'">{{ $label[0] }}</span>
                <span x-show="language === 'es'">{{ $label[1] }}</span>
            @else
                {{ $label }}
            @endif
        </span>
    @endif
    <div class="flex items-center justify-between gap-3">
        @if ($url)
            <a href="{{ $url }}" target="_blank" rel="noopener"
                class="font-mono {{ $monoSize }} text-[var(--accent)] hover:underline break-all focus-ring-signature rounded-sm">
                {{ $shown }} <span aria-hidden="true">↗</span>
            </a>
        @else
            <span
                class="font-mono {{ $monoSize }} text-[var(--ink)] select-all break-all">{{ $shown }}</span>
        @endif
        <button type="button"
            @click="navigator.clipboard.writeText(@js($value)).then(() => { copied = true; setTimeout(() => copied = false, 1500); })"
            :aria-label="copied ? (language === 'en' ? 'Copied' : 'Copiado') : (language === 'en' ? 'Copy' : 'Copiar')"
            class="font-mono text-[9px] uppercase tracking-wider text-[var(--muted)] hover:text-[var(--primary)] focus-ring-signature px-2 py-1 rounded border border-[var(--border)] hover:border-[var(--primary)] transition-all shrink-0">
            <span x-show="!copied" x-text="language === 'en' ? 'Copy' : 'Copiar'"></span>
            <span x-show="copied" class="text-[var(--accent)]" aria-hidden="true">✓</span>
        </button>
    </div>
</div>
