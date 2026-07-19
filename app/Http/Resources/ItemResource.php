<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\Item;
use App\Models\LanguageLine;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property-read Item $resource
 */
final class ItemResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $nameEn = $this->resolveTranslation('name', 'en');
        $nameEs = $this->resolveTranslation('name', 'es');
        $descEn = $this->resolveTranslation('desc', 'en');
        $descEs = $this->resolveTranslation('desc', 'es');
        $typeEn = $this->resolveTranslation('type', 'en');
        $typeEs = $this->resolveTranslation('type', 'es');

        return [
            'id' => $this->resource->getKey(),
            'slug' => $this->resource->slug,
            'name' => $nameEn,
            'name_es' => $nameEs !== '' ? $nameEs : $nameEn,
            'desc' => $descEn,
            'desc_es' => $descEs !== '' ? $descEs : $descEn,
            'type' => $this->formatType($typeEn, $typeEs),
            'type_es' => $typeEs !== '' ? $typeEs : $typeEn,
            'rarity' => $this->resource->rarity?->value,
            'category' => $this->resource->category ? [
                'id' => $this->resource->category->getKey(),
                'slug' => $this->resource->category->slug,
                'name' => $this->resource->category->name('en'),
                'name_es' => $this->resource->category->name('es'),
                'icon' => $this->resource->category->icon,
                'color_token' => $this->resource->category->color_token,
                'position' => $this->resource->category->position,
            ] : null,
            'loadout' => $this->resource->loadout?->value,
            'equipment_slot' => $this->resource->equipment_slot?->value,
            'icon' => $this->resource->icon,
            'image_url' => $this->resource->image_url,
            'stats' => $this->resource->statList(),
            'position' => $this->resource->position,
        ];
    }

    private function resolveTranslation(string $field, string $locale): string
    {
        $line = LanguageLine::query()
            ->where('group', LanguageLine::ITEMS_GROUP)
            ->where('key', sprintf('%s.%s', (string) $this->resource->slug, $field))
            ->first();

        $text = is_array($line?->text) ? $line->text : [];

        $value = $text[$locale] ?? null;
        if (! is_string($value) || $value === '') {
            $fallback = $text[app()->getFallbackLocale()] ?? null;
            $value = is_string($fallback) ? $fallback : '';
        }

        return $value;
    }

    private function formatType(string $enType, string $esType): string
    {
        if ($esType === '' || $esType === $enType) {
            return $enType;
        }

        return $enType . ' / ' . $esType;
    }
}
