<?php

declare(strict_types=1);

namespace App\Actions\Translations;

use App\Data\SyncTranslationsResult;
use App\Models\LanguageLine;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\File;

final class SyncTranslationsFromLangFilesAction
{
    /**
     * Scan every `lang/<locale>/*.php` file and merge its strings into the
     * `language_lines` table. Database edits win: a locale that already
     * has a non-empty value in the database is left untouched. A locale
     * that is missing, null, or empty is filled from the file.
     *
     * @param  array<int, string>|null  $locales  When null or empty, every
     *                                            subdirectory of `$basePath` is
     *                                            treated as a locale (skipping
     *                                            `vendor` and any name that does
     *                                            not look like a locale code).
     */
    public function handle(string $basePath, ?array $locales = null): SyncTranslationsResult
    {
        $result = new SyncTranslationsResult();

        if (! File::isDirectory($basePath)) {
            return $result;
        }

        if ($locales === null || $locales === []) {
            $locales = $this->detectLocales($basePath);
        }

        foreach ($locales as $locale) {
            $localePath = $basePath . DIRECTORY_SEPARATOR . $locale;
            if ($locale === '') {
                continue;
            }

            if (! File::isDirectory($localePath)) {
                continue;
            }

            foreach (File::allFiles($localePath) as $file) {
                if ($file->getExtension() !== 'php') {
                    continue;
                }

                $relative = $file->getRelativePathname();
                $group = mb_substr($relative, 0, -4);

                if ($group === '') {
                    continue;
                }

                $realPath = $file->getRealPath();
                if ($realPath === false) {
                    continue;
                }

                $translations = require $realPath;
                if (! is_array($translations)) {
                    continue;
                }

                if ($translations === []) {
                    continue;
                }

                $result->filesProcessed++;
                $this->processTranslations($result, $locale, $group, $translations);
            }
        }

        foreach (File::files($basePath) as $file) {
            if ($file->getExtension() !== 'json') {
                continue;
            }

            $locale = $file->getBasename('.json');
            if (preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/', $locale) !== 1) {
                continue;
            }

            if ($locale === 'vendor') {
                continue;
            }

            if (! in_array($locale, $locales, true)) {
                continue;
            }

            $realPath = $file->getRealPath();
            if ($realPath === false) {
                continue;
            }

            $content = File::get($realPath);
            $translations = json_decode($content, true);
            if (! is_array($translations)) {
                continue;
            }

            if ($translations === []) {
                continue;
            }

            $result->filesProcessed++;
            $this->processTranslations($result, $locale, '*', $translations);
        }

        return $result;
    }

    /**
     * @param  array<int|string, mixed>  $translations
     */
    private function processTranslations(
        SyncTranslationsResult $result,
        string $locale,
        string $group,
        array $translations,
    ): void {
        foreach (Arr::dot($translations) as $key => $value) {
            if (! is_string($key)) {
                continue;
            }

            if ($key === '') {
                continue;
            }

            if (! is_string($value) && ! is_numeric($value)) {
                continue;
            }

            $stringValue = (string) $value;
            if ($stringValue === '') {
                continue;
            }

            $line = LanguageLine::query()->firstOrCreate(
                ['group' => $group, 'key' => $key],
                ['text' => []],
            );

            $existing = $line->text ?? [];
            $wasJustCreated = $line->wasRecentlyCreated;

            if (array_key_exists($locale, $existing) && $existing[$locale] !== null && $existing[$locale] !== '') {
                $result->skipped++;

                continue;
            }

            $existing[$locale] = $stringValue;
            $line->text = $existing;
            $line->save();

            if ($wasJustCreated) {
                $result->created++;
            } else {
                $result->updated++;
            }
        }
    }

    private function detectLocales(string $basePath): array
    {
        $locales = [];

        foreach (File::directories($basePath) as $directory) {
            if (! is_string($directory)) {
                continue;
            }

            $name = basename($directory);
            if ($name === 'vendor') {
                continue;
            }

            if (preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/', $name) !== 1) {
                continue;
            }

            $locales[] = $name;
        }

        foreach (File::files($basePath) as $file) {
            if ($file->getExtension() === 'json') {
                $name = $file->getBasename('.json');
                if ($name === 'vendor') {
                    continue;
                }

                if (preg_match('/^[a-zA-Z][a-zA-Z0-9_-]*$/', $name) !== 1) {
                    continue;
                }

                $locales[] = $name;
            }
        }

        $locales = array_values(array_unique($locales));
        sort($locales);

        return $locales;
    }
}
