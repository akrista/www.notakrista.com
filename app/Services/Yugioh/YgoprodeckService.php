<?php

declare(strict_types=1);

namespace App\Services\Yugioh;

use App\Models\YugiohCard;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Sleep;
use Throwable;

final class YgoprodeckService
{
    /**
     * Sync cards with YGOPRODeck.
     *
     * @param  Collection<int, YugiohCard>|array<int, YugiohCard>  $cards
     */
    public function syncCards(Collection | array $cards): void
    {
        $cardsCollection = collect($cards);

        if ($cardsCollection->isEmpty()) {
            return;
        }

        foreach ($cardsCollection as $index => $card) {
            if ($index > 0) {
                // Rate limit is 20 req/sec, so 150ms delay is safe and respects the API
                Sleep::usleep(150000);
            }

            try {
                $card->setcode = mb_strtoupper(mb_trim($card->setcode));

                // 1. Fetch print info
                $response = Http::withHeaders([
                    'User-Agent' => 'notakrista.com Yugioh Tracker/1.0 (contact@notakrista.com)',
                    'Accept' => 'application/json',
                ])->get('https://db.ygoprodeck.com/api/v7/cardsetsinfo.php', [
                    'setcode' => $card->setcode,
                ]);

                if ($response->failed()) {
                    continue;
                }

                $printData = $response->json();
                $name = $printData['name'] ?? null;
                $rarity = $printData['set_rarity'] ?? null;
                $price = isset($printData['set_price']) ? (float) $printData['set_price'] : null;
                $ygoprodeckId = isset($printData['id']) ? (int) $printData['id'] : null;

                $type = null;
                $frameType = null;
                $imageUrl = null;
                $cardPrice = null;

                // 2. Fetch detailed card info (images & type)
                if ($ygoprodeckId) {
                    Sleep::usleep(150000); // 150ms delay before second call
                    $detailResponse = Http::withHeaders([
                        'User-Agent' => 'notakrista.com Yugioh Tracker/1.0 (contact@notakrista.com)',
                        'Accept' => 'application/json',
                    ])->get('https://db.ygoprodeck.com/api/v7/cardinfo.php', [
                        'id' => $ygoprodeckId,
                    ]);

                    if ($detailResponse->successful()) {
                        $detailData = $detailResponse->json('data.0');
                        if ($detailData) {
                            $type = $detailData['type'] ?? $detailData['humanReadableCardType'] ?? null;
                            $frameType = $detailData['frameType'] ?? null;
                            if (isset($detailData['card_prices'][0]['tcgplayer_price'])) {
                                $cardPrice = (float) $detailData['card_prices'][0]['tcgplayer_price'];
                            }

                            if (isset($detailData['card_images'][0]['image_url'])) {
                                $remoteUrl = $detailData['card_images'][0]['image_url'];
                                $filename = 'yugioh/cards/' . $ygoprodeckId . '.jpg';

                                if (Storage::disk('public')->exists($filename)) {
                                    $imageUrl = $filename;
                                } else {
                                    $imageResponse = Http::withHeaders([
                                        'User-Agent' => 'notakrista.com Yugioh Tracker/1.0 (contact@notakrista.com)',
                                    ])->get($remoteUrl);

                                    if ($imageResponse->successful()) {
                                        Storage::disk('public')->put($filename, $imageResponse->body());
                                        $imageUrl = $filename;
                                    } else {
                                        $imageUrl = $remoteUrl;
                                    }
                                }
                            }
                        }
                    }
                }

                $card->update([
                    'name' => $name,
                    'rarity' => $rarity,
                    'price' => $price,
                    'card_price' => $cardPrice,
                    'ygoprodeck_id' => $ygoprodeckId,
                    'type' => $type,
                    'frame_type' => $frameType,
                    'image_url' => $imageUrl,
                ]);

            } catch (Throwable $e) {
                Log::warning(sprintf('Failed to sync Yugioh card setcode "%s" during bulk sync: %s', $card->setcode, $e->getMessage()));
            }
        }
    }
}
