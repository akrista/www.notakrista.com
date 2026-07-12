<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Closure;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

use function Laravel\Prompts\confirm;
use function Laravel\Prompts\info;
use function Laravel\Prompts\note;

use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'bizkit:lang')]
#[Description('Republish Laravel framework language files and optionally vendor (Filament) translations.')]
#[Signature('bizkit:lang
                            {--existing : Pass --existing through to `lang:publish` (only overwrite already-published files)}
                            {--force : Pass --force through to `lang:publish` (overwrite any existing files)}
                            {--no-vendor : Skip publishing vendor (Filament) translations}
                            {--lang=* : The language(s) to keep (e.g. "en", "es"). Defaults to app and fallback locales. Use "all" to keep everything.}')]
final class LangPublishCommand extends Command
{
    /**
     * The Artisan call runner; injected for testability.
     *
     * @var Closure(string, array<int|string, mixed>): int
     */
    private Closure $artisanCall;

    /**
     * The Artisan output reader; injected for testability.
     *
     * @var Closure(string): string
     */
    private Closure $artisanOutput;

    public function __construct()
    {
        parent::__construct();

        $this->artisanCall = static fn (string $command, array $parameters = []): int => Artisan::call($command, $parameters);
        $this->artisanOutput = static fn (string $command): string => Artisan::output();
    }

    public function handle(): int
    {
        $this->publishFrameworkTranslations();
        $this->publishVendorTranslations();

        return self::SUCCESS;
    }

    /**
     * Inject a fake Artisan call runner. Intended for tests.
     *
     * @param  Closure(string, array<int|string, mixed>): int  $callback
     */
    public function setArtisanCall(Closure $callback): void
    {
        $this->artisanCall = $callback;
    }

    /**
     * Inject a fake Artisan output reader. Intended for tests.
     *
     * @param  Closure(string): string  $callback
     */
    public function setArtisanOutput(Closure $callback): void
    {
        $this->artisanOutput = $callback;
    }

    /**
     * Run `php artisan lang:publish` with the user's --existing / --force flags.
     */
    private function publishFrameworkTranslations(): void
    {
        info('Publishing Laravel framework language files…');

        $arguments = ['--no-interaction' => true];

        if ($this->option('existing')) {
            $arguments['--existing'] = true;
        }

        if ($this->option('force')) {
            $arguments['--force'] = true;
        }

        $exit = ($this->artisanCall)('lang:publish', $arguments);

        $this->line(($this->artisanOutput)('lang:publish'));

        if ($exit !== 0) {
            $this->error('`lang:publish` exited with a non-zero status.');
        }
    }

    /**
     * Discover and (optionally) re-publish every installed `*-translations` vendor tag.
     */
    private function publishVendorTranslations(): void
    {
        $tags = $this->discoverTranslationTags();

        if ($tags === []) {
            note('No vendor `*-translations` publish tags were discovered.');

            return;
        }

        $shouldPublish = $this->option('no-vendor')
            ? false
            : confirm(
                label: sprintf('Publish %d vendor translation tag(s)? (%s)', count($tags), implode(', ', $tags)),
                default: true,
            );

        if (! $shouldPublish) {
            note('Skipping vendor translations (use --no-vendor to silence this prompt in the future).');

            return;
        }

        foreach ($tags as $tag) {
            info(sprintf('Publishing vendor translations for [%s]…', $tag));

            $exit = ($this->artisanCall)('vendor:publish', [
                '--tag' => $tag,
                '--no-interaction' => true,
                '--force' => true,
            ]);

            $this->line(($this->artisanOutput)('vendor:publish'));

            if ($exit !== 0) {
                $this->error(sprintf('`vendor:publish --tag=%s` exited with a non-zero status.', $tag));
            }
        }

        $languagesToKeep = $this->getLanguagesToKeep();

        if ($languagesToKeep !== null) {
            $this->cleanUnwantedVendorTranslations($languagesToKeep);
        }
    }

    /**
     * Get the list of languages to keep.
     *
     * @return array<int, string>|null
     */
    private function getLanguagesToKeep(): ?array
    {
        $languages = $this->option('lang');

        if (empty($languages)) {
            $locale = config('app.locale');
            $fallback = config('app.fallback_locale');

            $candidates = array_filter([
                is_string($locale) ? $locale : null,
                is_string($fallback) ? $fallback : null,
            ], static fn (?string $value): bool => $value !== null && $value !== '');

            return array_values(array_unique($candidates));
        }

        $parsed = [];
        foreach ($languages as $lang) {
            if (mb_strtolower((string) $lang) === 'all') {
                return null;
            }

            foreach (explode(',', (string) $lang) as $l) {
                $trimmed = mb_trim($l);
                if ($trimmed !== '') {
                    $parsed[] = $trimmed;
                }
            }
        }

        return array_unique($parsed);
    }

    /**
     * Clean up unwanted vendor translations from the lang/vendor directory.
     *
     * @param  array<int, string>  $languagesToKeep
     */
    private function cleanUnwantedVendorTranslations(array $languagesToKeep): void
    {
        info(sprintf('Cleaning up unwanted vendor translations… keeping: %s', implode(', ', $languagesToKeep)));

        $vendorPath = app()->langPath() . DIRECTORY_SEPARATOR . 'vendor';

        if (! File::isDirectory($vendorPath)) {
            return;
        }

        $directories = File::directories($vendorPath);

        foreach ($directories as $packageDir) {
            if (! is_string($packageDir)) {
                continue;
            }

            if (! File::isDirectory($packageDir)) {
                continue;
            }

            $locales = File::directories($packageDir);
            foreach ($locales as $localeDir) {
                if (! is_string($localeDir)) {
                    continue;
                }

                $locale = basename($localeDir);
                if (! in_array($locale, $languagesToKeep, true)) {
                    File::deleteDirectory($localeDir);
                }
            }

            $files = File::files($packageDir);
            foreach ($files as $file) {
                if ($file->getExtension() === 'json') {
                    $locale = $file->getBasename('.json');
                    if (! in_array($locale, $languagesToKeep, true)) {
                        $realPath = $file->getRealPath();
                        if (is_string($realPath)) {
                            File::delete($realPath);
                        }
                    }
                }
            }
        }
    }

    /**
     * Detect every `*-translations` vendor publish tag currently installed.
     *
     * Parses `vendor:publish` output to find all available translation tags,
     * then filters to only those matching the `*-translations` pattern.
     *
     * @return array<int, string>
     */
    private function discoverTranslationTags(): array
    {
        $available = $this->listAvailableTags();

        $tags = array_values(array_filter($available, fn (string $tag): bool => str_ends_with($tag, '-translations')));

        sort($tags);

        return $tags;
    }

    /**
     * Parse `php artisan vendor:publish` output to extract every
     * `Tag: <name>` advertised by a registered service provider.
     *
     * @return array<int, string>
     */
    private function listAvailableTags(): array
    {
        return array_keys(ServiceProvider::$publishGroups);
    }
}
