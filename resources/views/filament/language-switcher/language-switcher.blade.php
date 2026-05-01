@if ($shouldShow ?? false)
    <x-filament::dropdown placement="bottom-end">
        <x-slot name="trigger">
            <x-filament::dropdown.list.item icon="heroicon-o-language">
                <div class="flex items-center justify-between w-full">
                    <span>{{ __('Language') }}</span>
                    <span class="text-sm text-gray-500 dark:text-gray-400">
                        {{ $currentLanguage['name'] ?? '' }}
                    </span>
                </div>
            </x-filament::dropdown.list.item>
        </x-slot>

        <x-filament::dropdown.list>
            @foreach ($otherLanguages as $language)
                @php
                    $isCurrent = ($currentLanguage['code'] ?? '') === $language['code'];
                @endphp
                <x-filament::dropdown.list.item :href="route('language-switcher.switch', ['code' => $language['code']])" tag="a"
                    :icon="$isCurrent ? 'heroicon-o-check' : null">
                    {{ $language['name'] }}
                </x-filament::dropdown.list.item>
            @endforeach
        </x-filament::dropdown.list>
    </x-filament::dropdown>
@endif
