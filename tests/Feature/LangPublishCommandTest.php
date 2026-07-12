<?php

declare(strict_types=1);

use App\Console\Commands\LangPublishCommand;
use Illuminate\Support\Facades\File;
use Illuminate\Support\ServiceProvider;

beforeEach(function (): void {
    $this->calls = [];
    $this->outputs = ['lang:publish' => '', 'vendor:publish' => ''];

    // Reset publish groups to avoid interference from other tests
    ServiceProvider::$publishGroups = [];

    $this->app->bind(LangPublishCommand::class, function (): LangPublishCommand {
        $command = new LangPublishCommand;

        $command->setArtisanCall(function (string $command, array $parameters = []): int {
            $this->calls[] = ['command' => $command, 'parameters' => $parameters];

            return 0;
        });

        $command->setArtisanOutput(fn (string $command) => $this->outputs[$command] ?? '');

        return $command;
    });
});

afterEach(function (): void {
    File::deleteDirectory(app()->langPath() . '/vendor/test-package');
});

function callsMatchingCommand(array $calls, string $command): array
{
    return array_values(array_filter($calls, fn (array $c): bool => $c['command'] === $command));
}

test('it always calls lang:publish with --no-interaction', function (): void {
    $this->artisan('bizkit:lang', ['--no-vendor' => true])
        ->assertSuccessful();

    $matches = callsMatchingCommand($this->calls, 'lang:publish');
    expect($matches)->toHaveCount(1)
        ->and($matches[0]['parameters'])->toBe(['--no-interaction' => true]);
});

test('it forwards the --existing flag to lang:publish', function (): void {
    $this->artisan('bizkit:lang', ['--existing' => true, '--no-vendor' => true])
        ->assertSuccessful();

    $matches = callsMatchingCommand($this->calls, 'lang:publish');
    expect($matches)->toHaveCount(1)
        ->and($matches[0]['parameters'])->toBe(['--no-interaction' => true, '--existing' => true]);
});

test('it forwards the --force flag to lang:publish', function (): void {
    $this->artisan('bizkit:lang', ['--force' => true, '--no-vendor' => true])
        ->assertSuccessful();

    $matches = callsMatchingCommand($this->calls, 'lang:publish');
    expect($matches)->toHaveCount(1)
        ->and($matches[0]['parameters'])->toBe(['--no-interaction' => true, '--force' => true]);
});

test('it never publishes vendor translations when --no-vendor is set', function (): void {
    $this->artisan('bizkit:lang', ['--no-vendor' => true])
        ->assertSuccessful();

    $vendorCalls = array_filter(
        $this->calls,
        fn (array $c): bool => $c['command'] === 'vendor:publish'
            && array_key_exists('--tag', $c['parameters']),
    );

    expect($vendorCalls)->toBeEmpty();
});

test('it publishes a discovered vendor translations tag with --force after confirmation', function (): void {
    // Set up expected publish groups
    ServiceProvider::$publishGroups = [
        'filament-actions-translations' => [],
        'filament-forms-translations' => [],
    ];

    $this->artisan('bizkit:lang')
        ->expectsConfirmation(
            'Publish 2 vendor translation tag(s)? (filament-actions-translations, filament-forms-translations)',
            'yes',
        )
        ->assertSuccessful();

    $publishCalls = array_values(array_filter(
        $this->calls,
        fn (array $c): bool => $c['command'] === 'vendor:publish'
            && array_key_exists('--tag', $c['parameters']),
    ));

    expect($publishCalls)->toHaveCount(2)
        ->and($publishCalls[0]['parameters'])->toMatchArray([
            '--tag' => 'filament-actions-translations',
            '--no-interaction' => true,
            '--force' => true,
        ])
        ->and($publishCalls[1]['parameters'])->toMatchArray([
            '--tag' => 'filament-forms-translations',
            '--no-interaction' => true,
            '--force' => true,
        ]);
});

test('it skips vendor publishing when the user declines the confirmation', function (): void {
    // Set up expected publish groups
    ServiceProvider::$publishGroups = [
        'filament-actions-translations' => [],
    ];

    $this->artisan('bizkit:lang')
        ->expectsConfirmation(
            'Publish 1 vendor translation tag(s)? (filament-actions-translations)',
            'no',
        )
        ->assertSuccessful();

    $publishCalls = array_filter(
        $this->calls,
        fn (array $c): bool => $c['command'] === 'vendor:publish'
            && array_key_exists('--tag', $c['parameters']),
    );

    expect($publishCalls)->toBeEmpty();
});

test('it cleans up unwanted vendor translations using default app and fallback locales', function (): void {
    // 1. Create dummy files/folders
    $testPackagePath = app()->langPath() . '/vendor/test-package';
    File::makeDirectory($testPackagePath . '/en', 0755, true, true);
    File::makeDirectory($testPackagePath . '/fr', 0755, true, true);
    File::put($testPackagePath . '/es.json', '{}');

    // Make sure they exist
    expect(File::isDirectory($testPackagePath . '/en'))->toBeTrue()
        ->and(File::isDirectory($testPackagePath . '/fr'))->toBeTrue()
        ->and(File::exists($testPackagePath . '/es.json'))->toBeTrue();

    // 2. Set up expected publish groups and run command
    ServiceProvider::$publishGroups = [
        'test-package-translations' => [],
    ];

    $this->artisan('bizkit:lang')
        ->expectsConfirmation(
            'Publish 1 vendor translation tag(s)? (test-package-translations)',
            'yes',
        )
        ->assertSuccessful();

    // 3. Verify cleanup
    // Default app.locale and app.fallback_locale should be kept (usually 'en')
    expect(File::isDirectory($testPackagePath . '/en'))->toBeTrue()
        ->and(File::isDirectory($testPackagePath . '/fr'))->toBeFalse()
        ->and(File::exists($testPackagePath . '/es.json'))->toBeFalse();
});

test('it cleans up unwanted vendor translations using custom --lang options', function (): void {
    $testPackagePath = app()->langPath() . '/vendor/test-package';
    File::makeDirectory($testPackagePath . '/en', 0755, true, true);
    File::makeDirectory($testPackagePath . '/fr', 0755, true, true);
    File::put($testPackagePath . '/es.json', '{}');

    ServiceProvider::$publishGroups = [
        'test-package-translations' => [],
    ];

    // Specify --lang=fr,es
    $this->artisan('bizkit:lang', ['--lang' => ['fr,es']])
        ->expectsConfirmation(
            'Publish 1 vendor translation tag(s)? (test-package-translations)',
            'yes',
        )
        ->assertSuccessful();

    // 'fr' and 'es' should be kept, 'en' should be deleted
    expect(File::isDirectory($testPackagePath . '/en'))->toBeFalse()
        ->and(File::isDirectory($testPackagePath . '/fr'))->toBeTrue()
        ->and(File::exists($testPackagePath . '/es.json'))->toBeTrue();
});

test('it does not clean up anything when --lang=all is passed', function (): void {
    $testPackagePath = app()->langPath() . '/vendor/test-package';
    File::makeDirectory($testPackagePath . '/en', 0755, true, true);
    File::makeDirectory($testPackagePath . '/fr', 0755, true, true);
    File::put($testPackagePath . '/es.json', '{}');

    ServiceProvider::$publishGroups = [
        'test-package-translations' => [],
    ];

    $this->artisan('bizkit:lang', ['--lang' => ['all']])
        ->expectsConfirmation(
            'Publish 1 vendor translation tag(s)? (test-package-translations)',
            'yes',
        )
        ->assertSuccessful();

    // Everything should be kept
    expect(File::isDirectory($testPackagePath . '/en'))->toBeTrue()
        ->and(File::isDirectory($testPackagePath . '/fr'))->toBeTrue()
        ->and(File::exists($testPackagePath . '/es.json'))->toBeTrue();
});
