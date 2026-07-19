<?php

declare(strict_types=1);

use App\Models\LanguageLine;
use Illuminate\Support\Facades\Cache;

beforeEach(function (): void {
    Cache::flush();
    app()->setLocale('en');
});

test('the model is bound in the translation loader config', function (): void {
    expect(config('translation-loader.model'))->toBe(LanguageLine::class);
});

test('text is cast to an array', function (): void {
    $line = LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['en' => 'Account'],
    ]);

    expect($line->text)->toBe(['en' => 'Account'])
        ->and($line->getCasts())->toHaveKey('text');
});

test('getTranslation returns the value for the requested locale', function (): void {
    $line = LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['en' => 'Account', 'es' => 'Cuenta'],
    ]);

    expect($line->getTranslation('en'))->toBe('Account')
        ->and($line->getTranslation('es'))->toBe('Cuenta');
});

test('getTranslation falls back to the configured fallback locale when the requested locale is missing', function (): void {
    config(['app.fallback_locale' => 'en']);

    $line = LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['en' => 'Account'],
    ]);

    expect($line->getTranslation('es'))->toBe('Account')
        ->and($line->getTranslation('fr'))->toBe('Account');
});

test('getTranslation returns null when neither the requested nor the fallback locale is set', function (): void {
    config(['app.fallback_locale' => 'de']);

    $line = LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['en' => 'Account'],
    ]);

    expect($line->getTranslation('fr'))->toBeNull();
});

test('setTranslation merges a single locale into the text array without clobbering others', function (): void {
    $line = LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['en' => 'Account'],
    ]);

    $line->setTranslation('es', 'Cuenta');
    $line->save();

    expect($line->fresh()->text)->toBe(['en' => 'Account', 'es' => 'Cuenta']);
});

test('saving a line flushes the cached translations for its group and every locale it stores', function (): void {
    $line = LanguageLine::query()->create([
        'group' => 'resources',
        'key' => 'user',
        'text' => ['en' => 'Account', 'es' => 'Cuenta'],
    ]);

    foreach (['en', 'es'] as $locale) {
        Cache::put(LanguageLine::getCacheKey('resources', $locale), ['stale' => true], 60);
    }

    expect(Cache::get(LanguageLine::getCacheKey('resources', 'en')))
        ->toBe(['stale' => true])
        ->and(Cache::get(LanguageLine::getCacheKey('resources', 'es')))
        ->toBe(['stale' => true]);

    $line->touch();

    expect(Cache::get(LanguageLine::getCacheKey('resources', 'en')))->toBeNull()
        ->and(Cache::get(LanguageLine::getCacheKey('resources', 'es')))->toBeNull();
});

test('getCacheKey namespacing prevents collisions across groups', function (): void {
    expect(LanguageLine::getCacheKey('resources', 'en'))
        ->toBe('spatie.translation-loader.resources.en')
        ->and(LanguageLine::getCacheKey('fields', 'en'))
        ->toBe('spatie.translation-loader.fields.en');
});
