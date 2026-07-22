<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\YugiohCard;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read YugiohCard $resource
 */
final class YugiohCardResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getKey(),
            'name' => $this->resource->name,
            'setcode' => $this->resource->setcode,
            'quantity' => $this->resource->quantity,
            'type' => $this->resource->type,
            'frame_type' => $this->resource->frame_type,
            'rarity' => $this->resource->rarity,
            'price' => $this->resource->price,
            'card_price' => $this->resource->card_price,
            'image_url' => $this->resource->image_url,
            'ygoprodeck_id' => $this->resource->ygoprodeck_id,
            'is_sold' => (bool) $this->resource->is_sold,
        ];
    }
}
