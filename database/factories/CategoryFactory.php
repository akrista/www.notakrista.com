<?php

declare(strict_types=1);

namespace Database\Factories;

use App\Models\Category;
use App\Models\LanguageLine;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

/**
 * @extends Factory<Category>
 */
final class CategoryFactory extends Factory
{
    /**
     * @var list<string>
     */
    private const array TRANSLATION_KEYS = ['name_en', 'name_es'];

    public function definition(): array
    {
        $name = fake()->unique()->word();

        return [
            'slug' => Str::slug($name) . '-' . Str::lower(Str::random(4)),
            'icon' => '📦',
            'color_token' => 'muted',
            'position' => 0,
        ];
    }

    public function configure(): static
    {
        return $this->afterCreating(function (Category $category): void {
            $overrides = $category->factoryTranslationOverrides;
            $nameEn = array_key_exists('name_en', $overrides) ? (string) $overrides['name_en'] : Str::title(Str::replace('-', ' ', (string) $category->slug));
            $nameEs = array_key_exists('name_es', $overrides) ? (string) $overrides['name_es'] : $nameEn;

            $line = LanguageLine::query()->firstOrNew([
                'group' => LanguageLine::CATEGORIES_GROUP,
                'key' => (string) $category->slug,
            ]);
            $line->text = ['en' => $nameEn, 'es' => $nameEs];
            $line->save();
        });
    }

    /**
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
}
