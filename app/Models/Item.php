<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\EquipmentSlot;
use App\Enums\ItemLoadout;
use App\Enums\ItemRarity;
use Database\Factories\ItemFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;
use Mattiverse\Userstamps\Traits\Userstamps;
use Override;

/**
 * @property int|string|null $id
 * @property string $slug
 * @property ?string $category_id
 * @property ItemRarity $rarity
 * @property ?string $icon
 * @property ?string $image_url
 * @property ?array<int, string> $stats
 * @property ?ItemLoadout $loadout
 * @property ?EquipmentSlot $equipment_slot
 * @property int $position
 * @property ?Carbon $acquired_at
 *
 * @mixin Model
 */
#[Fillable([
    'slug',
    'category_id',
    'rarity',
    'icon',
    'image_url',
    'stats',
    'loadout',
    'equipment_slot',
    'position',
    'acquired_at',
])]
final class Item extends Model
{
    /** @use HasFactory<ItemFactory> */
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
        'rarity' => 'common',
        'position' => 0,
        'stats' => '[]',
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function name(string $locale = 'en'): string
    {
        return $this->translation('name', $locale);
    }

    public function description(string $locale = 'en'): string
    {
        return $this->translation('desc', $locale);
    }

    public function typeLabel(string $locale = 'en'): string
    {
        return $this->translation('type', $locale);
    }

    /**
     * @return array<int, string>
     */
    public function statList(): array
    {
        return is_array($this->stats) ? array_values($this->stats) : [];
    }

    public function isEquipped(): bool
    {
        return $this->loadout !== null && $this->equipment_slot !== null;
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Strip virtual translation fields and metadata used by the factory
     * before persisting; the real translation values are written to the
     * `language_lines` table by the Filament resource or factory hook.
     */
    #[Override]
    protected static function booted(): void
    {
        self::creating(static function (self $item): void {
            $attributes = $item->getAttributes();

            if (array_key_exists('_translation_overrides', $attributes)) {
                $raw = $attributes['_translation_overrides'];
                if (is_array($raw)) {
                    $item->factoryTranslationOverrides = array_filter(
                        $raw,
                        fn (mixed $value): bool => is_string($value) && $value !== '',
                    );
                }

                $item->offsetUnset('_translation_overrides');
            }
        });
    }

    /**
     * @param  Builder<Item>  $query
     * @return Builder<Item>
     */
    #[Scope]
    protected function equipped(Builder $query): Builder
    {
        return $query->whereNotNull('loadout')->whereNotNull('equipment_slot');
    }

    /**
     * @param  Builder<Item>  $query
     * @return Builder<Item>
     */
    #[Scope]
    protected function forLoadout(Builder $query, ItemLoadout $loadout): Builder
    {
        $cases = collect(EquipmentSlot::cases())
            ->map(fn (EquipmentSlot $slot): string => sprintf("WHEN '%s' THEN %d", $slot->value, $slot->position()))
            ->implode(' ');

        return $query->where('loadout', $loadout->value)
            ->whereNotNull('equipment_slot')
            ->orderByRaw(sprintf('CASE equipment_slot %s END', $cases));
    }

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'rarity' => ItemRarity::class,
            'loadout' => ItemLoadout::class,
            'equipment_slot' => EquipmentSlot::class,
            'stats' => 'array',
            'position' => 'integer',
            'acquired_at' => 'datetime',
        ];
    }

    private function translation(string $field, string $locale): string
    {
        if (empty($this->slug)) {
            return '';
        }

        $transKey = sprintf('%s.%s.%s', LanguageLine::ITEMS_GROUP, $this->slug, $field);
        $value = trans($transKey, [], $locale);

        if (($value === $transKey || $value === '') && $locale !== app()->getFallbackLocale()) {
            $value = trans($transKey, [], app()->getFallbackLocale());
        }

        if ($value === $transKey) {
            $line = LanguageLine::query()
                ->where('group', LanguageLine::ITEMS_GROUP)
                ->where('key', sprintf('%s.%s', $this->slug, $field))
                ->first();

            $text = is_array($line?->text) ? $line->text : [];
            $value = $text[$locale] ?? $text[app()->getFallbackLocale()] ?? null;
        }

        return is_string($value) && $value !== '' ? $value : '';
    }
}
