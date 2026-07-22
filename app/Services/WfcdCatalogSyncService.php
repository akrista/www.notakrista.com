<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\WarframeItem;
use Illuminate\Support\Facades\Http;

final class WfcdCatalogSyncService
{
    /**
     * Fetch static item catalog from WFCD API and bulk upsert records.
     *
     * @param  array<int, array<string, mixed>>|null  $itemsData
     * @return int Count of synced items
     */
    public function sync(?array $itemsData = null): int
    {
        ini_set('memory_limit', '512M');

        if ($itemsData === null) {
            $response = Http::timeout(60)->get('https://api.warframestat.us/items');

            if (! $response->successful()) {
                return 0;
            }

            $itemsData = $response->json();
        }

        if (! is_array($itemsData)) {
            return 0;
        }

        $count = 0;

        foreach ($itemsData as $item) {
            if (! isset($item['uniqueName'], $item['name'])) {
                continue;
            }

            WarframeItem::query()->updateOrCreate(
                ['unique_name' => $item['uniqueName']],
                [
                    'name' => $item['name'],
                    'category' => $item['category'] ?? 'Miscellaneous',
                    'type' => $item['type'] ?? null,
                    'relic_era' => $item['relicEra'] ?? null,
                    'vaulted' => (bool) ($item['vaulted'] ?? false),
                    'tradeable' => (bool) ($item['tradable'] ?? true),
                    'description' => $item['description'] ?? null,
                    'image_name' => $item['imageName'] ?? null,
                    'stats' => [
                        'health' => $item['health'] ?? null,
                        'shield' => $item['shield'] ?? null,
                        'armor' => $item['armor'] ?? null,
                        'power' => $item['power'] ?? null,
                        'productCategory' => $item['productCategory'] ?? null,
                    ],
                ]
            );

            $count++;
        }

        return $count;
    }
}
