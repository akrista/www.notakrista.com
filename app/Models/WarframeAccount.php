<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable([
    'user_id',
    'account_name',
    'active_avatar',
    'credits',
    'platinum',
    'void_traces',
    'endo',
    'total_warframes',
    'total_weapons',
    'total_mods',
    'total_relics',
    'mastery_rank',
    'boosters',
    'last_imported_at',
])]
final class WarframeAccount extends Model
{
    use HasFactory;

    /**
     * Get the user that owns the Warframe account.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * User's extracted inventory items (Warframes, Weapons, Mods, Gear).
     */
    public function userItems(): HasMany
    {
        return $this->hasMany(WarframeUserItem::class);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'credits' => 'integer',
            'platinum' => 'integer',
            'void_traces' => 'integer',
            'endo' => 'integer',
            'total_warframes' => 'integer',
            'total_weapons' => 'integer',
            'total_mods' => 'integer',
            'total_relics' => 'integer',
            'mastery_rank' => 'integer',
            'boosters' => 'array',
            'last_imported_at' => 'datetime',
        ];
    }
}
