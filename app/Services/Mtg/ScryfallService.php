<?php

declare(strict_types=1);

namespace App\Services\Mtg;

use App\Models\MtgCard;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Sleep;

final class ScryfallService
{
    /**
     * Sync cards with Scryfall in batches of 75.
     *
     * @param  Collection<int, MtgCard>|array<int, MtgCard>  $cards
     */
    public function syncCards(Collection | array $cards): void
    {
        $cardsCollection = collect($cards);

        if ($cardsCollection->isEmpty()) {
            return;
        }

        // Scryfall allows up to 75 identifiers in POST /cards/collection
        $chunks = $cardsCollection->chunk(75);

        foreach ($chunks as $index => $chunk) {
            if ($index > 0) {
                // Respect rate limit: /cards/collection is limited to 2/second (500ms delay)
                Sleep::usleep(500000);
            }

            $identifiers = $chunk->map(fn (MtgCard $card): array => [
                'set' => mb_strtolower($card->set),
                'collector_number' => $card->number,
            ])->values()->all();

            $response = Http::withHeaders([
                'User-Agent' => 'notakrista.com MTG Tracker/1.0 (contact@notakrista.com)',
                'Accept' => 'application/json',
            ])->post('https://api.scryfall.com/cards/collection', [
                'identifiers' => $identifiers,
            ]);

            if ($response->failed()) {
                continue;
            }

            $data = $response->json('data') ?? [];

            foreach ($data as $cardData) {
                if (! isset($cardData['set'], $cardData['collector_number'])) {
                    continue;
                }

                // Find local cards matching the set and collector number
                $localCards = $chunk->filter(
                    fn (MtgCard $c): bool => mb_strtolower($c->set) === mb_strtolower($cardData['set'])
                        && $c->number === $cardData['collector_number']
                );

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

                foreach ($localCards as $localCard) {
                    $localCard->update([
                        'name' => $cardData['name'] ?? null,
                        'type_line' => $cardData['type_line'] ?? null,
                        'mana_cost' => $cardData['mana_cost'] ?? null,
                        'rarity' => $cardData['rarity'] ?? null,
                        'price' => $price,
                        'image_url' => $imageUrl,
                        'scryfall_id' => $cardData['id'] ?? null,
                    ]);
                }
            }
        }
    }
}
