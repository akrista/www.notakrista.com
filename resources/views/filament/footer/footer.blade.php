@php
    use Filament\Support\Enums\Width;

    $footerPosition = 'footer';
    $borderTopEnabled = true;
    $maxContentWidth = filament()->getMaxContentWidth() ?? Width::SevenExtraLarge;

    $copyrightText = '© ' . now()->format('Y') . ' Akrista';
    $links = [];
@endphp

<footer role="contentinfo" @class([
    'fi-footer flex flex-wrap items-center justify-center gap-x-3 gap-y-1.5 text-xs sm:text-sm text-gray-500 dark:text-gray-400',
    'border-t border-gray-200 pt-4 sm:pt-6 mt-4 sm:mt-6 dark:border-white/10' => $borderTopEnabled,
    'mx-auto w-full px-4 pb-4 sm:px-6 sm:pb-6 lg:px-8' => $footerPosition === 'footer',
    'safe-area-inset-bottom' => true,
    match ($maxContentWidth) {
        Width::ExtraSmall, 'xs' => 'max-w-xs',
        Width::Small, 'sm' => 'max-w-sm',
        Width::Medium, 'md' => 'max-w-md',
        Width::Large, 'lg' => 'max-w-lg',
        Width::ExtraLarge, 'xl' => 'max-w-xl',
        Width::TwoExtraLarge, '2xl' => 'max-w-2xl',
        Width::ThreeExtraLarge, '3xl' => 'max-w-3xl',
        Width::FourExtraLarge, '4xl' => 'max-w-4xl',
        Width::FiveExtraLarge, '5xl' => 'max-w-5xl',
        Width::SixExtraLarge, '6xl' => 'max-w-6xl',
        Width::SevenExtraLarge, '7xl' => 'max-w-7xl',
        Width::Full, 'full' => 'max-w-full',
        default => $maxContentWidth,
    } => $footerPosition === 'footer',
])>
    <span class="text-center">{{ $copyrightText }}</span>

    @if (count($links) > 0)
        <span class="text-gray-300 dark:text-gray-700 select-none hidden sm:inline" aria-hidden="true">·</span>

        <nav aria-label="{{ __('Footer navigation') }}" class="w-full sm:w-auto">
            <ul class="flex flex-col sm:flex-row flex-wrap items-center justify-center gap-x-3 gap-y-2 sm:gap-y-1">
                @foreach ($links as $link)
                    <li>
                        <a href="{{ $link['url'] }}"
                            class="inline-flex items-center min-h-11 sm:min-h-0 px-2 transition-colors hover:text-gray-700 focus:text-gray-700 focus:outline-none focus-visible:underline focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 rounded dark:hover:text-gray-200 dark:focus:text-gray-200"
                            @if ($link['external'] ?? false) target="_blank" rel="noopener noreferrer" @endif>
                            {{ $link['title'] }}
                            @if ($link['external'] ?? false)
                                <span class="sr-only">({{ __('Opens in new tab') }})</span>
                            @endif
                        </a>
                    </li>
                @endforeach
            </ul>
        </nav>
    @endif
</footer>