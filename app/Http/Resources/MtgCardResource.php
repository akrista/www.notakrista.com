<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\MtgCard;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read MtgCard $resource
 */
final class MtgCardResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'name' => $this->resource->name,
            'set' => $this->resource->set,
            'number' => $this->resource->number,
            'quantity' => $this->resource->quantity,
            'mana_cost' => $this->resource->mana_cost,
            'type_line' => $this->resource->type_line,
            'rarity' => $this->resource->rarity,
            'price' => $this->resource->price,
            'image_url' => $this->resource->image_url,
            'is_sold' => (bool) $this->resource->is_sold,
        ];
    }
}
