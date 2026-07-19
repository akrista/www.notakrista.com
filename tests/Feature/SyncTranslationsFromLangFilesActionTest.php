<?php

declare(strict_types=1);

use App\Actions\Translations\SyncTranslationsFromLangFilesAction;
use App\Models\LanguageLine;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\File;

beforeEach(function (): void {
    Cache::flush();
    app()->setLocale('en');
});

/**
 * Build a temp lang/ directory with the supplied locale->files map and
 * return its absolute path. The directory is created fresh for every call.
 *
 * @param  array<string, array<string, array<string, mixed>|string>>  $layout
 */
function syncTranslationsFixture(array $layout): string
{
    $base = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sync-translations-' . uniqid('', true);

    if (File::isDirectory($base)) {
        File::deleteDirectory($base);
    }

    File::makeDirectory($base, 0o755, true);

    foreach ($layout as $locale => $files) {
        $localePath = $base . DIRECTORY_SEPARATOR . $locale;
        File::makeDirectory($localePath, 0o755, true);

        foreach ($files as $relativePath => $contents) {
            $full = $localePath . DIRECTORY_SEPARATOR . $relativePath;
            $dir = dirname($full);
            if (! File::isDirectory($dir)) {
                File::makeDirectory($dir, 0o755, true);
            }

            $payload = is_array($contents)
                ? '<?php' . PHP_EOL . 'declare(strict_types=1);' . PHP_EOL . PHP_EOL . 'return ' . var_export($contents, true) . ';' . PHP_EOL
                : $contents;

            File::put($full, $payload);
        }
    }

    return $base;
}

afterEach(function (): void {
    foreach (File::directories(sys_get_temp_dir()) as $dir) {
        if (str_starts_with(basename((string) $dir), 'sync-translations-')) {
            File::deleteDirectory($dir);
        }
    }
});

test('it seeds a new LanguageLine for every key in the lang files', function (): void {
    $base = syncTranslationsFixture([
        'en' => [
            'resources.php' => ['user' => 'User', 'users' => 'Users'],
        ],
    ]);

    $result = (new SyncTranslationsFromLangFilesAction)->handle($base);

    expect($result->created)->toBe(2)
        ->and($result->updated)->toBe(0)
        ->and($result->skipped)->toBe(0)
        ->and($result->filesProcessed)->toBe(1);

    $line = LanguageLine::query()->where('group', 'resources')->where('key', 'user')->first();
    expect($line)->not->toBeNull()
        ->and($line->getTranslation('en'))->toBe('User');
});

test('it flattens nested arrays into dotted keys that match Laravel lookup syntax', function (): void {
    $base = syncTranslationsFixture([
        'en' => [
            'menu.php' => ['nav_group' => ['settings' => 'Settings']],
        ],
    ]);

    (new SyncTranslationsFromLangFilesAction)->handle($base);

    expect(__('menu.nav_group.settings'))->toBe('Settings');
});

test('it does not overwrite an existing database value for a locale', function (): void {
    $base = syncTranslationsFixture([
        'en' => [
            'resources.php' => ['user' => 'User'],
        ],
    ]);

    LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['en' => 'Account'],
    ]);

    $result = (new SyncTranslationsFromLangFilesAction)->handle($base);

    expect($result->skipped)->toBe(1)
        ->and($result->created)->toBe(0)
        ->and($result->updated)->toBe(0);

    $line = LanguageLine::query()->where('group', 'resources')->where('key', 'user')->first();
    expect($line->getTranslation('en'))->toBe('Account');
});

test('it fills a missing locale on an existing row but leaves existing locales alone', function (): void {
    $base = syncTranslationsFixture([
        'en' => [
            'resources.php' => ['user' => 'User'],
        ],
    ]);

    LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['en' => 'Account'],
    ]);

    // The fixture has only `en`; the existing row already has `en`, so nothing happens.
    expect((new SyncTranslationsFromLangFilesAction)->handle($base)->updated)->toBe(0);

    $base2 = syncTranslationsFixture([
        'es' => [
            'resources.php' => ['user' => 'Cuenta'],
        ],
    ]);

    $result = (new SyncTranslationsFromLangFilesAction)->handle($base2);

    expect($result->updated)->toBe(1)
        ->and($result->skipped)->toBe(0);

    $line = LanguageLine::query()->where('group', 'resources')->where('key', 'user')->first();
    expect($line->getTranslation('en'))->toBe('Account')
        ->and($line->getTranslation('es'))->toBe('Cuenta');
});

test('it fills a null or empty locale value on an existing row from the file', function (): void {
    $base = syncTranslationsFixture([
        'es' => [
            'resources.php' => ['user' => 'Cuenta'],
        ],
    ]);

    LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['es' => null],
    ]);

    $result = (new SyncTranslationsFromLangFilesAction)->handle($base);

    expect($result->updated)->toBe(1);

    $line = LanguageLine::query()->where('group', 'resources')->where('key', 'user')->first();
    expect($line->getTranslation('es'))->toBe('Cuenta');
});

test('it scopes to the locales passed in and skips others on disk', function (): void {
    $base = syncTranslationsFixture([
        'en' => [
            'resources.php' => ['user' => 'User'],
        ],
        'es' => [
            'resources.php' => ['user' => 'Cuenta'],
        ],
    ]);

    $result = (new SyncTranslationsFromLangFilesAction)->handle($base, ['es']);

    expect($result->created)->toBe(1);

    $line = LanguageLine::query()->where('group', 'resources')->where('key', 'user')->first();
    expect($line->getTranslation('es'))->toBe('Cuenta')
        ->and($line->getTranslation('en'))->toBeNull();
});

test('it auto-detects locales from subdirectories while ignoring vendor and other dirs', function (): void {
    $base = syncTranslationsFixture([
        'en' => ['resources.php' => ['user' => 'User']],
        'es' => ['resources.php' => ['user' => 'Cuenta']],
    ]);

    File::makeDirectory($base . '/vendor', 0o755, true);
    File::makeDirectory($base . '/.hidden', 0o755, true);
    File::put($base . '/vendor/skip.php', '<?php return ["ignored" => true];');

    $result = (new SyncTranslationsFromLangFilesAction)->handle($base);

    // The (group, key) row is created once and the second locale updates it.
    expect($result->created)->toBe(1)
        ->and($result->updated)->toBe(1)
        ->and($result->filesProcessed)->toBe(2);
});

test('it returns an empty result when the base path does not exist', function (): void {
    $result = (new SyncTranslationsFromLangFilesAction)->handle(sys_get_temp_dir() . '/does-not-exist-' . uniqid());

    expect($result->isEmpty())->toBeTrue()
        ->and($result->filesProcessed)->toBe(0);
});

test('it skips files that do not return an array', function (): void {
    $base = syncTranslationsFixture([
        'en' => [
            'broken.php' => '<?php $x = 1;',
            'resources.php' => ['user' => 'User'],
        ],
    ]);

    $result = (new SyncTranslationsFromLangFilesAction)->handle($base);

    expect($result->created)->toBe(1)
        ->and($result->filesProcessed)->toBe(1);
});

test('it makes the synced translation immediately resolvable through the __() helper', function (): void {
    $base = syncTranslationsFixture([
        'en' => [
            'resources.php' => ['user' => 'User'],
            'fields.php' => ['name' => 'Name'],
        ],
    ]);

    (new SyncTranslationsFromLangFilesAction)->handle($base);

    expect(__('resources.user'))->toBe('User')
        ->and(__('fields.name'))->toBe('Name');
});

test('it seeds language lines from JSON translation files', function (): void {
    $base = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'sync-translations-json-' . uniqid('', true);
    File::makeDirectory($base, 0o755, true);

    try {
        File::put($base . DIRECTORY_SEPARATOR . 'en.json', json_encode([
            'Create a new team' => 'Create a new team',
            'Cancel' => 'Cancel',
        ]));
        File::put($base . DIRECTORY_SEPARATOR . 'es.json', json_encode([
            'Create a new team' => 'Crear un nuevo equipo',
            'Cancel' => 'Cancelar',
        ]));

        $result = (new SyncTranslationsFromLangFilesAction)->handle($base);

        expect($result->created)->toBe(2)
            ->and($result->updated)->toBe(2)
            ->and($result->filesProcessed)->toBe(2);

        $line = LanguageLine::query()->where('group', '*')->where('key', 'Create a new team')->first();
        expect($line)->not->toBeNull()
            ->and($line->getTranslation('en'))->toBe('Create a new team')
            ->and($line->getTranslation('es'))->toBe('Crear un nuevo equipo');
    } finally {
        File::deleteDirectory($base);
    }
});
