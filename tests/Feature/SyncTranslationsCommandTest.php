<?php

declare(strict_types=1);

use App\Models\LanguageLine;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    Cache::flush();
    app()->setLocale('en');
});

/**
 * Build a temp lang/ directory and return its path. Mirrors the helper
 * used by the action test but kept in this file so the command test
 * stays self-contained.
 *
 * @param  array<string, array<string, array<string, mixed>>>  $layout
 */
function syncTranslationsCommandFixture(array $layout): string
{
    $base = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sync-translations-cmd-' . uniqid('', true);

    if (File::isDirectory($base)) {
        File::deleteDirectory($base);
    }

    File::makeDirectory($base, 0o755, true);

    foreach ($layout as $locale => $files) {
        $localePath = $base . DIRECTORY_SEPARATOR . $locale;
        File::makeDirectory($localePath, 0o755, true);

        foreach ($files as $name => $contents) {
            File::put(
                $localePath . DIRECTORY_SEPARATOR . $name,
                '<?php' . PHP_EOL . 'declare(strict_types=1);' . PHP_EOL . PHP_EOL . 'return ' . var_export($contents, true) . ';' . PHP_EOL,
            );
        }
    }

    return $base;
}

afterEach(function (): void {
    foreach (File::directories(sys_get_temp_dir()) as $dir) {
        if (str_starts_with(basename((string) $dir), 'sync-translations-cmd-')) {
            File::deleteDirectory($dir);
        }
    }
});

test('the command exits successfully and seeds translations from the supplied path', function (): void {
    $base = syncTranslationsCommandFixture([
        'en' => [
            'resources.php' => ['user' => 'User', 'users' => 'Users'],
        ],
    ]);

    $this->artisan('bizkit:sync-translations', ['--path' => $base])
        ->expectsOutputToContain('Files processed: 1')
        ->expectsOutputToContain('Created:         2')
        ->expectsOutputToContain('Updated:         0')
        ->expectsOutputToContain('Skipped:         0')
        ->expectsOutputToContain('Translations sync completed.')
        ->assertSuccessful();

    expect(LanguageLine::query()->where('group', 'resources')->count())->toBe(2);
});

test('the command respects the --locale filter', function (): void {
    $base = syncTranslationsCommandFixture([
        'en' => ['resources.php' => ['user' => 'User']],
        'es' => ['resources.php' => ['user' => 'Cuenta']],
    ]);

    $this->artisan('bizkit:sync-translations', ['--path' => $base, '--locale' => ['es']])
        ->expectsOutputToContain('Created:         1')
        ->assertSuccessful();

    $line = LanguageLine::query()->where('group', 'resources')->where('key', 'user')->first();
    expect($line->getTranslation('es'))->toBe('Cuenta')
        ->and($line->getTranslation('en'))->toBeNull();
});

test('the command preserves existing database values for a locale', function (): void {
    $base = syncTranslationsCommandFixture([
        'en' => ['resources.php' => ['user' => 'User']],
    ]);

    LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['en' => 'Account'],
    ]);

    $this->artisan('bizkit:sync-translations', ['--path' => $base])
        ->expectsOutputToContain('Skipped:         1')
        ->expectsOutputToContain('Created:         0')
        ->assertSuccessful();

    $line = LanguageLine::query()->where('group', 'resources')->where('key', 'user')->first();
    expect($line->getTranslation('en'))->toBe('Account');
});
