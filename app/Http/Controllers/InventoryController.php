<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Resources\ItemResource;
use App\Http\Resources\MtgCardResource;
use App\Http\Resources\WarframeAccountResource;
use App\Http\Resources\YugiohCardResource;
use App\Models\Item;
use App\Models\MtgCard;
use App\Models\WarframeAccount;
use App\Models\YugiohCard;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

final class InventoryController extends Controller
{
    public function __invoke(Request $request): View
    {
        $items = Item::query()
            ->with('category')
            ->get()
            ->sort(fn (Item $a, Item $b): int => [
                $a->category?->position ?? 0,
                $a->position,
            ] <=> [
                $b->category?->position ?? 0,
                $b->position,
            ])
            ->values()
            ->mapWithKeys(fn (Item $item): array => [
                $item->slug => new ItemResource($item)->resolve($request),
            ])
            ->all();

        $mtgCards = MtgCard::query()
            ->orderBy('name')
            ->get();

        $yugiohCards = YugiohCard::query()
            ->orderBy('name')
            ->get();

        $warframeAccount = WarframeAccount::query()
            ->with(['userItems.catalogItem'])
            ->first();

        return view('inventory', [
            'items' => $items,
            'initialItemId' => array_key_first($items),
            'mtgCards' => MtgCardResource::collection($mtgCards)->resolve($request),
            'yugiohCards' => YugiohCardResource::collection($yugiohCards)->resolve($request),
            'warframeData' => $warframeAccount ? new WarframeAccountResource($warframeAccount)->resolve($request) : null,
        ]);
    }
}
