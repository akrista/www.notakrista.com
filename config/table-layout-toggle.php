<?php

declare(strict_types=1);

use App\Support\TableLayoutToggle\Persisters\SessionPersister;
use Filament\Support\Icons\Heroicon;

return [

    'default_layout' => 'list',

    'toggle_action' => [
        'enabled' => true,
        'position' => 'tables::toolbar.search.after',
        'list_icon' => Heroicon::OutlinedTableCells,
        'grid_icon' => Heroicon::OutlinedSquares2x2,
    ],

    'persist' => [
        'persister' => SessionPersister::class,
        'share_between_pages' => false,
    ],

];
