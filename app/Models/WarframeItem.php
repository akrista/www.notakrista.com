<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'unique_name',
    'name',
    'category',
    'type',
    'relic_era',
    'vaulted',
    'tradeable',
    'description',
    'image_name',
    'stats',
])]
final class WarframeItem extends Model
{
    use HasFactory;

    /**
     * User inventory items mapped to this catalog item.
     */
    public function userItems(): HasMany
    {
        return $this->hasMany(WarframeUserItem::class);
    }

    /**
     * Get the full CDN image URL for this item.
     */
    protected function imageUrl(): Attribute
    {
        return Attribute::make(get: fn(): ?string => $this->image_name
            ? sprintf('https://cdn.warframestat.us/img/%s', $this->image_name)
            : null);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'vaulted' => 'boolean',
            'tradeable' => 'boolean',
            'stats' => 'array',
        ];
    }
}
