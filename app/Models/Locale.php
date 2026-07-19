<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\LocaleFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;
use Override;

/**
 * @property int|string|null $id
 * @property string $code
 * @property string $name
 * @property string $native_name
 * @property string $direction
 * @property bool $is_active
 * @property bool $is_default
 * @property int $position
 *
 * @mixin Model
 */
#[Fillable([
    'code',
    'name',
    'native_name',
    'direction',
    'is_active',
    'is_default',
    'position',
])]
final class Locale extends Model
{
    /** @use HasFactory<LocaleFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;
    use Userstamps;

    #[Override]
    protected $attributes = [
        'direction' => 'ltr',
        'is_active' => true,
        'is_default' => false,
        'position' => 0,
    ];

    public function getRouteKeyName(): string
    {
        return 'code';
    }

    public function isRtl(): bool
    {
        return $this->direction === 'rtl';
    }

    /**
     * @param  Builder<Locale>  $query
     * @return Builder<Locale>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * @param  Builder<Locale>  $query
     * @return Builder<Locale>
     */
    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query
            ->orderBy('position')
            ->orderBy('name');
    }

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'is_default' => 'boolean',
            'position' => 'integer',
        ];
    }
}
