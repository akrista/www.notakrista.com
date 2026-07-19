<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\YugiohCardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Sleep;
use Mattiverse\Userstamps\Traits\Userstamps;
use Throwable;

/**
 * @property string|null $id
 * @property string $setcode
 * @property int $quantity
 * @property bool $is_sold
 * @property string|null $name
 * @property string|null $type
 * @property string|null $frame_type
 * @property string|null $rarity
 * @property float|null $price
 * @property float|null $card_price
 * @property string|null $image_url
 * @property int|null $ygoprodeck_id
 *
 * @mixin Model
 */
#[Fillable([
    'setcode',
    'quantity',
    'is_sold',
    'name',
    'type',
    'frame_type',
    'rarity',
    'price',
    'card_price',
    'image_url',
    'ygoprodeck_id',
])]
final class YugiohCard extends Model
{
    /** @use HasFactory<YugiohCardFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;
    use Userstamps;

    protected static function booted(): void
    {
        self::saving(static function (YugiohCard $card): void {
            if ($card->isDirty('setcode')) {
                try {
                    $card->setcode = mb_strtoupper(mb_trim($card->setcode));

                    $response = Http::withHeaders([
                        'User-Agent' => 'notakrista.com Yugioh Tracker/1.0 (contact@notakrista.com)',
                        'Accept' => 'application/json',
                    ])->get('https://db.ygoprodeck.com/api/v7/cardsetsinfo.php', [
                        'setcode' => $card->setcode,
                    ]);

                    if ($response->successful()) {
                        $printData = $response->json();
                        $card->name = $printData['name'] ?? null;
                        $card->rarity = $printData['set_rarity'] ?? null;
                        $card->price = isset($printData['set_price']) ? (float) $printData['set_price'] : null;
                        $card->ygoprodeck_id = isset($printData['id']) ? (int) $printData['id'] : null;

                        if ($card->ygoprodeck_id) {
                            Sleep::usleep(100000); // 100ms delay to respect rate limit
                            $detailResponse = Http::withHeaders([
                                'User-Agent' => 'notakrista.com Yugioh Tracker/1.0 (contact@notakrista.com)',
                                'Accept' => 'application/json',
                            ])->get('https://db.ygoprodeck.com/api/v7/cardinfo.php', [
                                'id' => $card->ygoprodeck_id,
                            ]);

                            if ($detailResponse->successful()) {
                                $detailData = $detailResponse->json('data.0');
                                if ($detailData) {
                                    $card->type = $detailData['type'] ?? $detailData['humanReadableCardType'] ?? null;
                                    $card->frame_type = $detailData['frameType'] ?? null;
                                    if (isset($detailData['card_prices'][0]['tcgplayer_price'])) {
                                        $card->card_price = (float) $detailData['card_prices'][0]['tcgplayer_price'];
                                    }

                                    if (isset($detailData['card_images'][0]['image_url'])) {
                                        $remoteUrl = $detailData['card_images'][0]['image_url'];
                                        $filename = 'yugioh/cards/' . $card->ygoprodeck_id . '.jpg';

                                        if (Storage::disk('public')->exists($filename)) {
                                            $card->image_url = $filename;
                                        } else {
                                            $imageResponse = Http::withHeaders([
                                                'User-Agent' => 'notakrista.com Yugioh Tracker/1.0 (contact@notakrista.com)',
                                            ])->get($remoteUrl);

                                            if ($imageResponse->successful()) {
                                                Storage::disk('public')->put($filename, $imageResponse->body());
                                                $card->image_url = $filename;
                                            } else {
                                                $card->image_url = $remoteUrl;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                } catch (Throwable $e) {
                    Log::warning('Failed to sync Yugioh card details from YGOPRODeck during save: ' . $e->getMessage());
                }
            }
        });
    }

    /**
     * Get the card's image URL.
     *
     * @return Attribute<string|null, string|null>
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(
            get: static function (?string $value): ?string {
                if (!$value) {
                    return null;
                }

                if (str_starts_with($value, 'http://') || str_starts_with($value, 'https://')) {
                    return $value;
                }

                return Storage::disk('public')->url($value);
            }
        );
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
            'card_price' => 'decimal:2',
            'ygoprodeck_id' => 'integer',
        ];
    }
}
