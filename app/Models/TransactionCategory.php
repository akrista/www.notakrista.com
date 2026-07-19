<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\TransactionCategoryFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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
 * @property string $name
 * @property ?string $icon
 * @property ?string $color_token
 * @property int $position
 * @property bool $is_archived
 *
 * @mixin Model
 */
#[Fillable([
    'slug',
    'name',
    'icon',
    'color_token',
    'position',
    'is_archived',
])]
final class TransactionCategory extends Model
{
    /** @use HasFactory<TransactionCategoryFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;
    use Userstamps;

    #[Override]
    protected $attributes = [
        'position' => 0,
        'is_archived' => false,
    ];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * @return HasMany<Transaction, $this>
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, 'category_id');
    }

    /**
     * @return HasMany<Schedule, $this>
     */
    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'category_id');
    }

    public function archive(): bool
    {
        $this->is_archived = true;

        return $this->save();
    }

    /**
     * @param  Builder<TransactionCategory>  $query
     * @return Builder<TransactionCategory>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_archived', false);
    }

    /**
     * @return array<string, mixed>
     */
    #[Override]
    protected function casts(): array
    {
        return [
            'position' => 'integer',
            'is_archived' => 'boolean',
        ];
    }
}
