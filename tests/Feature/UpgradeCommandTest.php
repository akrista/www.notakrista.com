<?php

declare(strict_types=1);

use Illuminate\Process\PendingProcess;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;

function createTestZipContent(array $files): string
{
    $zip = new ZipArchive();
    $tempZipPath = tempnam(sys_get_temp_dir(), 'test_zip_');
    $zip->open($tempZipPath, ZipArchive::CREATE);
    foreach ($files as $name => $content) {
        $zip->addFromString('root/' . $name, $content);
    }

    $zip->close();
    $zipContent = (string) file_get_contents($tempZipPath);
    @unlink($tempZipPath);

    return $zipContent;
}

$bizkitJsonBackup = null;

beforeEach(function () use (&$bizkitJsonBackup): void {
    if (file_exists(base_path('bizkit.json'))) {
        $bizkitJsonBackup = file_get_contents(base_path('bizkit.json'));
    }

    Process::fake([
        '*' => function (PendingProcess $process) {
            $cmd = implode(' ', $process->command);
            if (str_contains($cmd, 'where git') || str_contains($cmd, 'which git')) {
                return Process::result('', 0);
            }

            if (str_contains($cmd, 'git status')) {
                return Process::result('', 0); // clean working tree
            }

            return Process::result('', 0);
        },
    ]);
});

afterEach(function () use (&$bizkitJsonBackup): void {
    if ($bizkitJsonBackup !== null) {
        file_put_contents(base_path('bizkit.json'), $bizkitJsonBackup);
    } else {
        @unlink(base_path('bizkit.json'));
    }

    @unlink(base_path('test_differ.txt'));
    @unlink(base_path('test_deleted.txt'));
    @unlink(base_path('test_new.txt'));
});

test('it throws an error when both --take-all and --skip-all are provided', function (): void {
    $this->artisan('bizkit:upgrade', [
        '--take-all' => true,
        '--skip-all' => true,
    ])
        ->assertFailed()
        ->expectsOutputToContain('Error: You cannot use both --take-all and --skip-all.');
});

test('it handles --take-all option automatically', function (): void {
    $zipV090 = createTestZipContent([
        'test_differ.txt' => 'local differ content',
        'test_deleted.txt' => 'local deleted content',
    ]);

    $zipV100 = createTestZipContent([
        'test_differ.txt' => 'upstream differ content',
        'test_new.txt' => 'new file content',
    ]);

    Http::fake([
        'https://api.github.com/repos/akrista/bizkit/tags' => Http::response([
            ['name' => 'v1.0.0'],
        ], 200),
        'https://api.github.com/repos/akrista/bizkit/zipball/v0.9.0' => Http::response($zipV090, 200),
        'https://api.github.com/repos/akrista/bizkit/zipball/v1.0.0' => Http::response($zipV100, 200),
    ]);

    file_put_contents(base_path('bizkit.json'), json_encode([
        'version' => 'v0.9.0',
        'repository' => 'akrista/bizkit',
    ]));

    file_put_contents(base_path('test_differ.txt'), 'local differ content');
    file_put_contents(base_path('test_deleted.txt'), 'local deleted content');

    $this->artisan('bizkit:upgrade', [
        '--take-all' => true,
    ])
        ->expectsConfirmation('Proceed with applying changes?', 'yes')
        ->assertSuccessful();

    // Check that files are overwritten/created/deleted correctly
    expect(file_exists(base_path('test_differ.txt')))->toBeTrue()
        ->and(file_get_contents(base_path('test_differ.txt')))->toBe('upstream differ content')
        ->and(file_exists(base_path('test_new.txt')))->toBeTrue()
        ->and(file_get_contents(base_path('test_new.txt')))->toBe('new file content')
        ->and(file_exists(base_path('test_deleted.txt')))->toBeFalse();

    // Check version updated
    $updatedVersion = json_decode((string) file_get_contents(base_path('bizkit.json')), true);
    expect($updatedVersion['version'])->toBe('v1.0.0');
});

test('it handles --skip-all option automatically', function (): void {
    $zipV090 = createTestZipContent([
        'test_differ.txt' => 'local differ content',
        'test_deleted.txt' => 'local deleted content',
    ]);

    $zipV100 = createTestZipContent([
        'test_differ.txt' => 'upstream differ content',
        'test_new.txt' => 'new file content',
    ]);

    Http::fake([
        'https://api.github.com/repos/akrista/bizkit/tags' => Http::response([
            ['name' => 'v1.0.0'],
        ], 200),
        'https://api.github.com/repos/akrista/bizkit/zipball/v0.9.0' => Http::response($zipV090, 200),
        'https://api.github.com/repos/akrista/bizkit/zipball/v1.0.0' => Http::response($zipV100, 200),
    ]);

    file_put_contents(base_path('bizkit.json'), json_encode([
        'version' => 'v0.9.0',
        'repository' => 'akrista/bizkit',
    ]));

    file_put_contents(base_path('test_differ.txt'), 'local differ content');
    file_put_contents(base_path('test_deleted.txt'), 'local deleted content');

    $this->artisan('bizkit:upgrade', [
        '--skip-all' => true,
    ])
        ->expectsConfirmation('Proceed with applying changes?', 'yes')
        ->assertSuccessful();

    // Check that files are NOT overwritten, NOT deleted, but new is created
    expect(file_exists(base_path('test_differ.txt')))->toBeTrue()
        ->and(file_get_contents(base_path('test_differ.txt')))->toBe('local differ content')
        ->and(file_exists(base_path('test_new.txt')))->toBeTrue()
        ->and(file_get_contents(base_path('test_new.txt')))->toBe('new file content')
        ->and(file_exists(base_path('test_deleted.txt')))->toBeTrue();

    // Check version updated
    $updatedVersion = json_decode((string) file_get_contents(base_path('bizkit.json')), true);
    expect($updatedVersion['version'])->toBe('v1.0.0');
});
