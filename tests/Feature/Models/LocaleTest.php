<?php

declare(strict_types=1);

use App\Models\Locale;

test('uses uuid primary keys', function (): void {
    $locale = Locale::factory()->create();

    expect($locale->id)->toBeString()
        ->and(mb_strlen((string) $locale->id))->toBe(36);
});

test('casts boolean and integer columns', function (): void {
    $locale = Locale::factory()->create([
        'is_active' => 1,
        'is_default' => 0,
        'position' => '5',
    ]);

    expect($locale->is_active)->toBeTrue()
        ->and($locale->is_default)->toBeFalse()
        ->and($locale->position)->toBe(5);
});

test('route key is the locale code', function (): void {
    $locale = Locale::factory()->create(['code' => 'fr']);

    expect($locale->getRouteKeyName())->toBe('code')
        ->and($locale->getRouteKey())->toBe('fr');
});

test('active scope excludes inactive locales', function (): void {
    Locale::factory()->create(['code' => 'fr', 'is_active' => true]);
    Locale::factory()->create(['code' => 'de', 'is_active' => false]);

    $codes = Locale::query()->active()->pluck('code')->all();

    expect($codes)->toContain('fr')
        ->and($codes)->not->toContain('de');
});

test('ordered scope sorts by position then name', function (): void {
    Locale::factory()->create(['code' => 'fr', 'position' => 2, 'name' => 'French']);
    Locale::factory()->create(['code' => 'de', 'position' => 1, 'name' => 'German']);
    Locale::factory()->create(['code' => 'it', 'position' => 1, 'name' => 'Italian']);

    $codes = Locale::query()
        ->whereIn('code', ['fr', 'de', 'it'])
        ->ordered()
        ->pluck('code')
        ->all();

    expect($codes)->toBe(['de', 'it', 'fr']);
});

test('isRtl reports the direction flag', function (): void {
    $ltr = Locale::factory()->create(['code' => 'fr', 'direction' => 'ltr']);
    $rtl = Locale::factory()->rtl()->create(['code' => 'ar']);

    expect($ltr->isRtl())->toBeFalse()
        ->and($rtl->isRtl())->toBeTrue();
});

test('the migration seeds English and Spanish as defaults', function (): void {
    $english = Locale::query()->where('code', 'en')->first();
    $spanish = Locale::query()->where('code', 'es')->first();

    expect($english)->not->toBeNull()
        ->and($spanish)->not->toBeNull()
        ->and($english?->is_default)->toBeTrue()
        ->and($spanish?->is_default)->toBeFalse()
        ->and($english?->direction)->toBe('ltr')
        ->and($spanish?->native_name)->toBe('Español');
});
