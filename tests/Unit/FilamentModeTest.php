<?php

declare(strict_types=1);

use App\Enums\FilamentMode;

test('admin mode returns correct panel path', function (): void {
    expect(FilamentMode::Admin->panelPath())->toBe('admin');
});

test('app mode returns empty panel path', function (): void {
    expect(FilamentMode::App->panelPath())->toBe('');
});

test('isApp returns true only for app mode', function (): void {
    expect(FilamentMode::App->isApp())->toBeTrue()
        ->and(FilamentMode::Admin->isApp())->toBeFalse();
});

test('from returns correct enum for valid values', function (): void {
    expect(FilamentMode::from('admin'))->toBe(FilamentMode::Admin)
        ->and(FilamentMode::from('app'))->toBe(FilamentMode::App);
});

test('from throws for invalid values', function (): void {
    expect(fn () => FilamentMode::from('invalid'))->toThrow(ValueError::class);
});
