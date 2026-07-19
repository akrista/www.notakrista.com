<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Translations\SyncTranslationsFromLangFilesAction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'bizkit:sync-translations')]
#[Description('Sync translation strings from the lang/ folder into the language_lines table.')]
#[Signature('bizkit:sync-translations
                            {--path= : Base directory to scan. Defaults to lang_path().}
                            {--locale=* : Restrict the sync to one or more locales. Defaults to every subdirectory of the path.}')]
final class SyncTranslationsCommand extends Command
{
    public function handle(SyncTranslationsFromLangFilesAction $action): int
    {
        $pathOption = $this->option('path');
        $basePath = is_string($pathOption) && $pathOption !== ''
            ? $pathOption
            : lang_path();

        $localeOption = $this->option('locale');
        $locales = $localeOption === []
            ? null
            : array_values(array_filter(
                $localeOption,
                static fn (mixed $value): bool => is_string($value) && $value !== '',
            ));

        $this->info(sprintf('Scanning translations under %s…', $basePath));

        $result = $action->handle($basePath, $locales);

        $this->line(sprintf('  Files processed: %d', $result->filesProcessed));
        $this->line(sprintf('  Created:         %d', $result->created));
        $this->line(sprintf('  Updated:         %d', $result->updated));
        $this->line(sprintf('  Skipped:         %d', $result->skipped));

        if ($result->isEmpty()) {
            $this->warn('No translation keys were found. Check that the path and locale filters match your lang/ folder.');

            return self::SUCCESS;
        }

        $this->info('Translations sync completed.');

        return self::SUCCESS;
    }
}
