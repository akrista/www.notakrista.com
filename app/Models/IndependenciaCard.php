<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\IndependenciaCardFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Mattiverse\Userstamps\Traits\Userstamps;

/**
 * @property string|null $id
 * @property string $deck
 * @property string $card_id
 * @property string $name
 * @property string $type
 * @property int $stars
 * @property ?string $monster_type
 * @property ?string $new_monster_type
 * @property int $attack
 * @property int $defense
 * @property ?string $description
 * @property ?string $effect
 *
 * @mixin Model
 */
#[Fillable([
    'deck',
    'card_id',
    'name',
    'type',
    'stars',
    'monster_type',
    'new_monster_type',
    'attack',
    'defense',
    'description',
    'effect',
])]
final class IndependenciaCard extends Model
{
    /** @use HasFactory<IndependenciaCardFactory> */
    use HasFactory;

    use HasUuids;
    use SoftDeletes;
    use Userstamps;

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'stars' => 'integer',
            'attack' => 'integer',
            'defense' => 'integer',
        ];
    }
}
