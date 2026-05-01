@props([
    'icon',
    'theme',
])

@php
    $label = __("filament-panels::layout.actions.theme_switcher.{$theme}.label");
@endphp

<button
    aria-label="{{ $label }}"
    type="button"
    x-bind:class="
        theme === @js($theme)
            ? 'bg-gray-100 text-primary-600 dark:bg-white/10 dark:text-primary-400'
            : 'text-gray-500 hover:text-gray-700 hover:bg-gray-100 dark:text-gray-400 dark:hover:text-gray-200 dark:hover:bg-white/5'
    "
    x-on:click="(theme = @js($theme))"
    x-tooltip="{
        content: @js($label),
        theme: $store.theme,
    }"
    class="flex min-h-11 min-w-11 items-center justify-center rounded-lg p-2.5 outline-none transition duration-150 ease-out focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2 dark:focus-visible:ring-offset-gray-900"
>
    <x-filament::icon
        :alias="'panels::theme-switcher.' . $theme . '-button'"
        :icon="$icon"
        class="h-5 w-5"
        aria-hidden="true"
    />
</button>
