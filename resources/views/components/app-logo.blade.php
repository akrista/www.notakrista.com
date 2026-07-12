@props([
    'sidebar' => false,
])

@if($sidebar)
    <flux:sidebar.brand :name="__('app.app_logo_name')" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-full border border-border bg-transparent">
            <img src="{{ asset('logo-circle.png') }}" class="size-8 object-contain rounded-full" alt="Logo">
        </x-slot>
    </flux:sidebar.brand>
@else
    <flux:brand :name="__('app.app_logo_name')" {{ $attributes }}>
        <x-slot name="logo" class="flex aspect-square size-8 items-center justify-center rounded-full border border-border bg-transparent">
            <img src="{{ asset('logo-circle.png') }}" class="size-8 object-contain rounded-full" alt="Logo">
        </x-slot>
    </flux:brand>
@endif
