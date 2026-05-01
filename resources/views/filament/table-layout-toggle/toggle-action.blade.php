@php
    $isGrid = $this->isGridLayout();
    $icon = $isGrid ? $listIcon : $gridIcon;
    $label = $isGrid ? __('Switch to list view') : __('Switch to grid view');
    $color = 'gray';
@endphp

<div>
    <x-filament::icon-button
        :icon="$icon"
        :color="$color"
        :label="$label"
        size="lg"
        class="min-h-11 min-w-11"
        wire:click="changeLayoutView"
    />
</div>
