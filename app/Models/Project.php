<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[Fillable([
    'name',
    'description',
    'url',
    'icon',
    'status',
    'tech_tags',
])]
final class Project extends Model
{
    use HasFactory;

    public static function statuses(): array
    {
        return self::query()
            ->distinct()
            ->orderBy('status')
            ->pluck('status')
            ->all();
    }

    #[Scope]
    protected function withStatus(Builder $query, ?string $status): Builder
    {
        if ($status && $status !== 'all') {
            return $query->where('status', $status);
        }

        return $query;
    }

    #[Scope]
    protected function sortedBy(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'recent' => $query->latest(),
            'name' => $query->orderBy('name'),
            default => $query->latest(),
        };
    }

    protected function casts(): array
    {
        return [
            'tech_tags' => 'array',
        ];
    }
}
