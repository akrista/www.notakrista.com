<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\MtgCardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Mattiverse\Userstamps\Traits\Userstamps;
use Throwable;

/**
 * @property string|null $id
 * @property string $set
 * @property string $number
 * @property int $quantity
 * @property bool $is_sold
 * @property string|null $name
 * @property string|null $type_line
 * @property string|null $mana_cost
 * @property string|null $rarity
 * @property float|null $price
 * @property string|null $image_url
 * @property string|null $scryfall_id
 *
 * @mixin Model
 */
#[Fillable([
    'set',
    'number',
    'quantity',
    'is_sold',
    'name',
    'type_line',
    'mana_cost',
    'rarity',
    'price',
    'image_url',
    'scryfall_id',
])]
final class MtgCard extends Model
{
    /** @use HasFactory<MtgCardFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;
    use Userstamps;

    protected static function booted(): void
    {
        self::saving(static function (MtgCard $card): void {
            if ($card->isDirty(['set', 'number'])) {
                try {
                    $response = Http::withHeaders([
                        'User-Agent' => 'notakrista.com MTG Tracker/1.0 (contact@notakrista.com)',
                        'Accept' => 'application/json',
                    ])->get(sprintf(
                        'https://api.scryfall.com/cards/%s/%s',
                        mb_strtolower($card->set),
                        $card->number
                    ));

                    if ($response->successful()) {
                        $cardData = $response->json();

                        $imageUrl = null;
                        if (isset($cardData['image_uris']['normal'])) {
                            $imageUrl = $cardData['image_uris']['normal'];
                        } elseif (isset($cardData['card_faces'][0]['image_uris']['normal'])) {
                            $imageUrl = $cardData['card_faces'][0]['image_uris']['normal'];
                        }

                        $price = null;
                        if (isset($cardData['prices']['usd'])) {
                            $price = (float) $cardData['prices']['usd'];
                        }

                        $card->name = $cardData['name'] ?? null;
                        $card->type_line = $cardData['type_line'] ?? null;
                        $card->mana_cost = $cardData['mana_cost'] ?? null;
                        $card->rarity = $cardData['rarity'] ?? null;
                        $card->price = $price;
                        $card->image_url = $imageUrl;
                        $card->scryfall_id = $cardData['id'] ?? null;
                    }
                } catch (Throwable $e) {
                    Log::warning('Failed to sync MTG card from Scryfall during save: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'is_sold' => 'boolean',
            'price' => 'decimal:2',
        ];
    }
}
