<?php

declare(strict_types=1);

namespace App\Services;

final class AlecaFrameParser
{
    /**
     * Decrypt lastData.dat content in-memory and parse JSON payload.
     *
     * @return array<string, mixed>
     */
    public static function parse(string $fileContents): array
    {
        $key = "LEO-ALEC\tEO-ALEC";
        $iv = "12FGB36-LE3-q=9\0";

        $decrypted = openssl_decrypt($fileContents, 'aes-128-cbc', $key, OPENSSL_RAW_DATA, $iv);

        if ($decrypted === false) {
            return [];
        }

        // Clean control characters/padding if any
        $cleaned = mb_trim(preg_replace('/[\x00-\x1F\x7F-\x9F]/u', '', $decrypted) ?? $decrypted);
        $data = json_decode($cleaned, true);

        if (! is_array($data)) {
            return [];
        }

        if (isset($data['InventoryJson']) && is_string($data['InventoryJson'])) {
            $inventoryData = json_decode($data['InventoryJson'], true);

            if (is_array($inventoryData)) {
                unset($data['InventoryJson']);

                return array_merge($data, $inventoryData);
            }
        }

        return $data;
    }
}
