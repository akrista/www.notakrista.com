<?php

declare(strict_types=1);

use App\Models\LanguageLine;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

beforeEach(function (): void {
    Cache::flush();
    app()->setLocale('en');
});

test('file-based translations still resolve when no database row exists', function (): void {
    expect(__('resources.user'))->toBe('User')
        ->and(__('fields.name'))->toBe('Name')
        ->and(__('sections.personal_information'))->toBe('Personal Information');
});

test('database row overrides the matching file-based translation', function (): void {
    LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['en' => 'Account'],
    ]);

    expect(__('resources.user'))->toBe('Account');
});

test('database row can add a locale that is missing from the file', function (): void {
    expect(__('resources.user'))->toBe('User');

    LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['es' => 'Cuenta'],
    ]);

    expect(__('resources.user'))->toBe('User');

    app()->setLocale('es');
    expect(__('resources.user'))->toBe('Cuenta');
    app()->setLocale('en');
});

test('deleting a database row restores the file-based fallback', function (): void {
    $line = LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['en' => 'Account'],
    ]);

    expect(__('resources.user'))->toBe('Account');

    $line->delete();
    expect(__('resources.user'))->toBe('User');
});

test('updating a database row invalidates the cached group lookup', function (): void {
    $line = LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['en' => 'Account'],
    ]);

    expect(__('resources.user'))->toBe('Account');

    $line->update(['text' => ['en' => 'Member']]);
    expect(__('resources.user'))->toBe('Member');
});

test('database row supports nested keys via dotted syntax', function (): void {
    LanguageLine::query()->create([
        'group' => 'fields',
        'key' => 'nested.subkey',
        'text' => ['en' => 'Nested Value'],
    ]);

    expect(__('fields.nested.subkey'))->toBe('Nested Value');
});

test('database row with only the fallback locale set still overrides the file for every locale', function (): void {
    // The package's Db loader asks the model for each (group, locale) pair,
    // and the model itself falls back to `app.fallback_locale` when the
    // requested locale is missing. This means a row with only `en` text still
    // replaces the file value for any other locale, not the file's value.
    LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['en' => 'Account'],
    ]);

    app()->setLocale('es');
    expect(__('resources.user'))->toBe('Account');
    app()->setLocale('fr');
    expect(__('resources.user'))->toBe('Account');
    app()->setLocale('en');
});

test('the translator falls back to file translations when the database table is missing', function (): void {
    LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['en' => 'Account'],
    ]);

    // Simulate the migration being rolled back
    Schema::drop('language_lines');

    expect(__('resources.user'))->toBe('User');
});

test('the translator returns the raw key when neither file nor database has it', function (): void {
    expect(__('resources.does_not_exist'))->toBe('resources.does_not_exist');
});

test('bulk deletion through the model also flushes the per-group cache', function (): void {
    LanguageLine::query()->create(['group' => 'resources', 'key' => 'user', 'text' => ['en' => 'Account']]);
    LanguageLine::query()->create(['group' => 'resources', 'key' => 'users', 'text' => ['en' => 'Accounts']]);

    expect(__('resources.user'))->toBe('Account')
        ->and(__('resources.users'))->toBe('Accounts');

    // Use model instance delete() so events fire; builder::delete() would bypass them.
    LanguageLine::query()->where('group', 'resources')->get()
        ->each(fn (LanguageLine $line) => $line->delete());

    expect(__('resources.user'))->toBe('User')
        ->and(__('resources.users'))->toBe('Users');
});
