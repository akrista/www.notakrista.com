@php
    $alignment = 'top-right';
    $currentRoute = request()->route()?->getName();
    $panelId = filament()->getCurrentPanel()->getId();
    $enabledRoutes = [
        "filament.{$panelId}.auth.login",
        "filament.{$panelId}.auth.register",
        "filament.{$panelId}.auth.password-reset.request",
        "filament.{$panelId}.auth.password-reset.reset",
    ];
    $shouldShow = $currentRoute && collect($enabledRoutes)->contains(fn($route) => str_contains($currentRoute, $route));
@endphp

@if (
    filament()->hasDarkMode() &&
    (! filament()->hasDarkModeForced()) &&
    $shouldShow
)
    <div
        @class([
            'fixed flex z-40 auth-theme-switcher',
            'p-2 sm:p-4',
            'safe-area-inset-top safe-area-inset-right',
            'top-0' => str_contains($alignment, 'top'),
            'bottom-0' => str_contains($alignment, 'bottom'),
            'left-0 right-auto' => str_contains($alignment, 'left'),
            'right-0 left-auto' => str_contains($alignment, 'right'),
            'left-1/2 -translate-x-1/2' => str_contains($alignment, 'center'),
        ])
        role="group"
        aria-label="{{ __('Theme selection') }}"
    >
        <div class="rounded-lg bg-gray-50/95 dark:bg-gray-950/95 backdrop-blur-sm shadow-sm ring-1 ring-gray-200 dark:ring-gray-800">
            <div
                x-data="{
                    theme: null,

                    init: function () {
                        this.theme = localStorage.getItem('theme') || @js(filament()->getDefaultThemeMode()->value)

                        $dispatch('theme-changed', theme)

                        $watch('theme', (theme) => {
                            $dispatch('theme-changed', theme)
                        })
                    },
                }"
                class="fi-theme-switcher grid grid-flow-col gap-x-0.5 sm:gap-x-1 p-0.5 sm:p-1"
            >
                @include('filament.switcher.button', ['icon' => 'heroicon-m-sun', 'theme' => 'light'])
                @include('filament.switcher.button', ['icon' => 'heroicon-m-moon', 'theme' => 'dark'])
                @include('filament.switcher.button', ['icon' => 'heroicon-m-computer-desktop', 'theme' => 'system'])
            </div>
        </div>
    </div>
@endif
