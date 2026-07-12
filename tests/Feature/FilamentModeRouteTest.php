<?php

declare(strict_types=1);

use App\Enums\FilamentMode;

test('fromConfig reads the bizkit.filament_mode config value', function (): void {
    config(['bizkit.filament_mode' => 'app']);
    expect(FilamentMode::fromConfig())->toBe(FilamentMode::App);

    config(['bizkit.filament_mode' => 'admin']);
    expect(FilamentMode::fromConfig())->toBe(FilamentMode::Admin);
});

test('fromConfig defaults to admin when config is null', function (): void {
    config(['bizkit.filament_mode' => null]);

    expect(FilamentMode::fromConfig())->toBe(FilamentMode::Admin);
});

test('admin mode is the default config value', function (): void {
    expect(config('bizkit.filament_mode'))->toBe('admin');
});
