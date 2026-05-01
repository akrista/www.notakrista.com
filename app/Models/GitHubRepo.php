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
    'full_name',
    'description',
    'html_url',
    'language',
    'stargazers_count',
    'forks_count',
    'open_issues_count',
    'visibility',
    'last_push_at',
    'synced_at',
])]
final class GitHubRepo extends Model
{
    use HasFactory;

    public static function languages(): array
    {
        return self::query()
            ->whereNotNull('language')
            ->distinct()
            ->orderBy('language')
            ->pluck('language')
            ->all();
    }

    #[Scope]
    protected function withLanguage(Builder $query, ?string $language): Builder
    {
        if ($language && $language !== 'all') {
            return $query->where('language', $language);
        }

        return $query;
    }

    #[Scope]
    protected function sortedBy(Builder $query, ?string $sort): Builder
    {
        return match ($sort) {
            'stars' => $query->orderByDesc('stargazers_count'),
            'recent' => $query->latest('last_push_at'),
            'name' => $query->orderBy('name'),
            default => $query->orderByDesc('stargazers_count'),
        };
    }

    protected function casts(): array
    {
        return [
            'stargazers_count' => 'integer',
            'forks_count' => 'integer',
            'open_issues_count' => 'integer',
            'last_push_at' => 'datetime',
            'synced_at' => 'datetime',
        ];
    }
}
