<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable([
    'warframe_account_id',
    'warframe_item_id',
    'item_type',
    'category',
    'xp',
    'level',
    'formas',
    'refinement',
    'fusion_rank',
    'max_fusion_rank',
    'riven_stats',
    'item_count',
    'item_data',
])]
final class WarframeUserItem extends Model
{
    use HasFactory;

    /**
     * Get the account that owns this inventory item.
     */
    public function account(): BelongsTo
    {
        return $this->belongsTo(WarframeAccount::class, 'warframe_account_id');
    }

    /**
     * Get the catalog item metadata from WFCD database.
     */
    public function catalogItem(): BelongsTo
    {
        return $this->belongsTo(WarframeItem::class, 'warframe_item_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'xp' => 'integer',
            'level' => 'integer',
            'formas' => 'integer',
            'fusion_rank' => 'integer',
            'max_fusion_rank' => 'integer',
            'riven_stats' => 'array',
            'item_count' => 'integer',
            'item_data' => 'array',
        ];
    }
}
