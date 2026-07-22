<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\WarframeAccount;
use App\Models\WarframeUserItem;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin WarframeAccount
 */
final class WarframeAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'account_name' => $this->account_name,
            'active_avatar' => $this->active_avatar,
            'mastery_rank' => $this->mastery_rank,
            'credits' => $this->credits,
            'platinum' => $this->platinum,
            'void_traces' => $this->void_traces,
            'endo' => $this->endo,
            'total_warframes' => $this->total_warframes,
            'total_weapons' => $this->total_weapons,
            'total_mods' => $this->total_mods,
            'total_relics' => $this->total_relics,
            'boosters' => $this->boosters ?? [],
            'last_imported_at' => $this->last_imported_at?->toIso8601String(),
            'last_imported_human' => $this->last_imported_at?->diffForHumans(),
            'items' => $this->userItems->map(fn (WarframeUserItem $item): array => [
                'id' => $item->id,
                'item_type' => $item->item_type,
                'category' => $item->category,
                'xp' => $item->xp,
                'level' => $item->level,
                'formas' => $item->formas,
                'refinement' => $item->refinement,
                'fusion_rank' => $item->fusion_rank,
                'max_fusion_rank' => $item->max_fusion_rank,
                'riven_stats' => $item->riven_stats,
                'item_count' => $item->item_count,
                'name' => $item->catalogItem?->name ?? basename($item->item_type),
                'description' => $item->catalogItem?->description,
                'image_url' => $item->catalogItem?->image_url,
                'relic_era' => $item->catalogItem?->relic_era,
            ])->values()->all(),
        ];
    }
}
