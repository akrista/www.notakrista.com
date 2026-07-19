<?php

declare(strict_types=1);

namespace App\Actions\Translations;

use App\Models\LanguageLine;
use Illuminate\Database\Eloquent\Model;

final class SyncModelTranslationLinesAction
{
    /**
     * Persist a record's translatable fields into the `language_lines` table
     * (Spatie's translation-loader model). The record is identified by its
     * current value of `$keyAttribute` (e.g. an item's `slug`); that key is
     * combined with each field to form the dotted `key` on the language line.
     *
     * Old language lines that no longer correspond to a known field are removed.
     * Locale values are merged so editing one locale never clobbers the other.
     *
     * @param  array<string, array<string, string|null>>  $translations  keyed by field then locale
     */
    public function handle(
        Model $record,
        string $group,
        string $keyAttribute,
        array $translations,
    ): void {
        $keyValue = (string) $record->getAttribute($keyAttribute);
        if ($keyValue === '') {
            return;
        }

        $expectedKeys = [];
        foreach ($translations as $field => $byLocale) {
            $lineKey = sprintf('%s.%s', $keyValue, $field);
            $expectedKeys[] = $lineKey;

            $line = LanguageLine::query()->firstOrNew(
                ['group' => $group, 'key' => $lineKey],
            );

            $existing = is_array($line->text) ? $line->text : [];
            foreach ($byLocale as $locale => $value) {
                $existing[$locale] = is_string($value) ? $value : null;
            }

            $line->text = $existing;
            $line->save();
        }

        if ($expectedKeys === []) {
            return;
        }

        LanguageLine::query()
            ->where('group', $group)
            ->where('key', 'like', sprintf('%s.%%', addcslashes($keyValue, '%_\\')))
            ->whereNotIn('key', $expectedKeys)
            ->delete();
    }

    /**
     * Delete every language line that belongs to a record under the given
     * group + key attribute (used when the record is deleted).
     */
    public function purge(Model $record, string $group, string $keyAttribute): void
    {
        $keyValue = (string) $record->getAttribute($keyAttribute);
        if ($keyValue === '') {
            return;
        }

        LanguageLine::query()
            ->where('group', $group)
            ->where('key', 'like', sprintf('%s.%%', addcslashes($keyValue, '%_\\')))
            ->delete();
    }

    /**
     * When the record's key attribute (e.g. slug) changes, rewrite the `key`
     * of every language line so the translation follows the record.
     */
    public function renameKey(Model $record, string $group, string $keyAttribute, string $previousValue): void
    {
        if ($previousValue === '' || $previousValue === $record->getAttribute($keyAttribute)) {
            return;
        }

        $previous = (string) $previousValue;
        $next = (string) $record->getAttribute($keyAttribute);

        $lines = LanguageLine::query()
            ->where('group', $group)
            ->where('key', 'like', sprintf('%s.%%', addcslashes($previous, '%_\\')))
            ->get();

        foreach ($lines as $line) {
            $line->key = sprintf('%s.%s', $next, mb_substr((string) $line->key, mb_strlen($previous) + 1));
            $line->save();
        }
    }
}
