<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;
use Override;

/**
 * @property int|string|null $id
 * @property string $slug
 * @property ?string $icon
 * @property ?string $color_token
 * @property int $position
 *
 * @mixin Model
 */
#[Fillable([
    'slug',
    'icon',
    'color_token',
    'position',
])]
final class Category extends Model
{
    /** @use HasFactory<CategoryFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;
    use Userstamps;

    /**
     * Factory-injected translation overrides survive the `save()` round-trip
     * so the `afterCreating` factory hook can read them.
     *
     * @var array<string, string>
     */
    public array $factoryTranslationOverrides = [];

    #[Override]
    protected $attributes = [
        'position' => 0,
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function name(string $locale = 'en'): string
    {
        if (empty($this->slug)) {
            return '';
        }

        $transKey = sprintf('%s.%s', LanguageLine::CATEGORIES_GROUP, $this->slug);
        $value = trans($transKey, [], $locale);

        if (($value === $transKey || $value === '') && $locale !== app()->getFallbackLocale()) {
            $value = trans($transKey, [], app()->getFallbackLocale());
        }

        if ($value === $transKey) {
            $line = LanguageLine::query()
                ->where('group', LanguageLine::CATEGORIES_GROUP)
                ->where('key', $this->slug)
                ->first();

            $text = is_array($line?->text) ? $line->text : [];
            $value = $text[$locale] ?? $text[app()->getFallbackLocale()] ?? null;
        }

        return is_string($value) && $value !== '' ? $value : '';
    }

    /**
     * @return HasMany<Item, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(Item::class, 'category_id');
    }

    #[Override]
    protected static function booted(): void
    {
        self::creating(static function (self $category): void {
            $attributes = $category->getAttributes();

            if (array_key_exists('_translation_overrides', $attributes)) {
                $raw = $attributes['_translation_overrides'];
                if (is_array($raw)) {
                    $category->factoryTranslationOverrides = array_filter(
                        $raw,
                        fn (mixed $value): bool => is_string($value) && $value !== '',
                    );
                }

                $category->offsetUnset('_translation_overrides');
            }
        });
    }

    /**
     * @return Attribute<string, never>
     */
    protected function displayName(): Attribute
    {
        return Attribute::make(get: fn (): string => $this->name('en'));
    }

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'position' => 'integer',
        ];
    }
}
