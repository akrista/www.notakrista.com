<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Enums\EquipmentSlot;
use App\Enums\ItemLoadout;
use App\Enums\ItemRarity;
use App\Models\Category;
use App\Models\Item;
use App\Models\LanguageLine;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @extends Factory<Item>
 */
final class ItemFactory extends Factory
{
    /**
     * Translation override fields that must never be persisted to `items`;
     * they are intercepted here, written to `language_lines` after the
     * record is created, and applied to the spatie translator.
     *
     * @var list<string>
     */
    private const array TRANSLATION_KEYS = [
        'name_en', 'name_es',
        'type_en', 'type_es',
        'desc_en', 'desc_es',
    ];

    public function definition(): array
    {
        $name = fake()->unique()->words(2, true);
        $category = Category::query()->firstOrCreate(
            ['slug' => 'tech'],
            [
                'icon' => '💻',
                'color_token' => 'blue',
                'position' => 1,
            ]
        );

        $this->seedCategoryTranslations($category);

        return [
            'slug' => Str::slug($name) . '-' . Str::lower(Str::random(4)),
            'category_id' => $category->getKey(),
            'rarity' => ItemRarity::Common,
            'icon' => '📦',
            'image_url' => null,
            'stats' => [
                '+10 ' . fake()->word() . ' / ' . fake()->word(),
            ],
            'loadout' => null,
            'equipment_slot' => null,
            'position' => 0,
            'acquired_at' => null,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Item $item): void {
            $slug = (string) $item->slug;
            if ($slug === '') {
                return;
            }

            $overrides = $item->factoryTranslationOverrides;

            $nameEn = array_key_exists('name_en', $overrides) ? (string) $overrides['name_en'] : Str::title(Str::replace('-', ' ', $slug));
            $nameEs = array_key_exists('name_es', $overrides) ? (string) $overrides['name_es'] : $nameEn;
            $typeEn = array_key_exists('type_en', $overrides) ? (string) $overrides['type_en'] : 'Test Item';
            $typeEs = array_key_exists('type_es', $overrides) ? (string) $overrides['type_es'] : '';
            $descEn = array_key_exists('desc_en', $overrides) ? (string) $overrides['desc_en'] : fake()->sentence();
            $descEs = array_key_exists('desc_es', $overrides) ? (string) $overrides['desc_es'] : '';

            $this->writeLine($slug, 'name', ['en' => $nameEn, 'es' => $nameEs]);
            $this->writeLine($slug, 'type', ['en' => $typeEn, 'es' => $typeEs]);
            $this->writeLine($slug, 'desc', ['en' => $descEn, 'es' => $descEs]);
        });
    }

    public function equipped(ItemLoadout $loadout, EquipmentSlot $slot): static
    {
        return $this->state(fn (array $attributes): array => [
            'loadout' => $loadout,
            'equipment_slot' => $slot,
        ]);
    }

    public function rarity(ItemRarity $rarity): static
    {
        return $this->state(fn (array $attributes): array => [
            'rarity' => $rarity,
        ]);
    }

    public function forCategory(Category $category): static
    {
        return $this->state(fn (array $attributes): array => [
            'category_id' => $category->getKey(),
        ]);
    }

    /**
     * Capture translation overrides from the factory state and strip them
     * from the attribute array that gets passed to the model's `create()`.
     * The captured values are stashed on a `_translation_overrides` virtual
     * attribute that survives the model `save()` and is read in `afterCreating`.
     *
     * @return array<string, mixed>
     */
    protected function getRawAttributes(?Model $parent = null): array
    {
        $attributes = parent::getRawAttributes($parent);

        $overrides = [];
        foreach (self::TRANSLATION_KEYS as $key) {
            if (array_key_exists($key, $attributes)) {
                $value = $attributes[$key];
                if (is_string($value) && $value !== '') {
                    $overrides[$key] = $value;
                }

                unset($attributes[$key]);
            }
        }

        if ($overrides !== []) {
            $attributes['_translation_overrides'] = $overrides;
        }

        return $attributes;
    }

    private function writeLine(string $slug, string $field, array $text): void
    {
        $line = LanguageLine::query()->firstOrNew([
            'group' => LanguageLine::ITEMS_GROUP,
            'key' => sprintf('%s.%s', $slug, $field),
        ]);
        $line->text = $text;
        $line->save();
    }

    private function seedCategoryTranslations(Category $category): void
    {
        $line = LanguageLine::query()->firstOrNew([
            'group' => LanguageLine::CATEGORIES_GROUP,
            'key' => (string) $category->slug,
        ]);
        $existing = is_array($line->text) ? $line->text : [];
        $line->text = array_merge([
            'en' => 'Tech',
            'es' => 'Tecnología',
        ], $existing);
        $line->save();
    }
}
