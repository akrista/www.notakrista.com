<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Models\Item;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class CharacterController extends Controller
{
    public function __invoke(Request $request): View
    {
        $items = Item::query()
            ->with('category')
            ->equipped()
            ->get()
            ->mapWithKeys(fn (Item $item): array => [
                $item->loadout->value . '_' . $item->equipment_slot->value => new ItemResource($item)->resolve($request),
            ])
            ->all();

        return view('character', [
            'items' => $items,
            'initialSlot' => array_key_first($items),
        ]);
    }
}
