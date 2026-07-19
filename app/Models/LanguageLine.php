<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Contracts\Translation\Translator;
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Override;
use Spatie\TranslationLoader\LanguageLine as BaseLanguageLine;
use Throwable;

/**
 * @property int|string|null $id
 * @property string $group
 * @property string $key
 * @property array<string, string|null> $text
 * @property bool $is_active
 * @property ?Carbon $created_at
 * @property ?Carbon $updated_at
 *
 * @mixin Model
 */
final class LanguageLine extends BaseLanguageLine
{
    use HasFactory;

    public const string HOME_PHRASES_GROUP = 'home_phrases';

    public const string CATEGORIES_GROUP = 'categories';

    public const string ITEMS_GROUP = 'items';

    /**
     * Collect every active phrase for a translation group, indexed by locale.
     * Used by the guest welcome page Alpine component to rotate subheadings.
     *
     * @return array<string, array<int, string>>
     */
    public static function getActivePhrasesByLocaleForGroup(string $group): array
    {
        $locales = self::availableLocales();

        $result = array_fill_keys($locales, []);
        $lines = self::query()
            ->where('group', $group)
            ->where('is_active', true)
            ->ordered()
            ->get();

        foreach ($locales as $locale) {
            $result[$locale] = $lines
                ->map(fn (self $line): string => self::resolveLineText($line, $locale))
                ->filter(fn (string $value): bool => $value !== '')
                ->values()
                ->all();
        }

        return $result;
    }

    /**
     * @return array<int, string>
     */
    public static function availableLocales(): array
    {
        try {
            $locales = Locale::query()->active()->pluck('code')->all();
        } catch (Throwable) {
            $locales = [];
        }

        if ($locales === []) {
            $locales = ['en', 'es'];
        }

        return array_values(array_map(strval(...), $locales));
    }

    /**
     * Clear the translator's in-memory `$loaded` array when saving or deleting.
     * The package's `flushGroupCache()` only forgets the per-group cached entry.
     * The singleton Translator keeps a process-wide `$loaded` array that is not
     * invalidated by that call. Without this hook, an admin panel edit does not
     * show up in the same request or subsequent requests served by the same Octane
     * worker until the cache is cleared.
     */
    #[Override]
    protected static function booted(): void
    {
        self::saved(static fn (self $line): bool => self::forgetLoadedTranslations());
        self::deleted(static fn (self $line): bool => self::forgetLoadedTranslations());
    }

    /**
     * @param  Builder<LanguageLine>  $query
     * @return Builder<LanguageLine>
     */
    #[Scope]
    protected function active(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Order phrases by insertion. Home phrases rotate in the order they were
     * added; manual reorder would need a future feature with an explicit field.
     *
     * @param  Builder<LanguageLine>  $query
     * @return Builder<LanguageLine>
     */
    #[Scope]
    protected function ordered(Builder $query): Builder
    {
        return $query->orderBy('id');
    }

    /**
     * @param  Builder<LanguageLine>  $query
     * @return Builder<LanguageLine>
     */
    #[Scope]
    protected function forGroup(Builder $query, string $group): Builder
    {
        return $query->where('group', $group);
    }

    /**
     * @return array<string, mixed>
     */
    protected function casts(): array
    {
        return [
            'text' => 'array',
            'is_active' => 'boolean',
        ];
    }

    private static function forgetLoadedTranslations(): bool
    {
        if (app()->bound(Translator::class)) {
            resolve(Translator::class)->setLoaded([]);
        }

        return true;
    }

    private static function resolveLineText(self $line, string $locale): string
    {
        $text = is_array($line->text) ? $line->text : [];

        $value = $text[$locale] ?? null;

        if (! is_string($value) || $value === '') {
            $fallback = $text[app()->getFallbackLocale()] ?? null;
            $value = is_string($fallback) ? $fallback : '';
        }

        return $value;
    }
}
