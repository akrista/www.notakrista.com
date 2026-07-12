<?php

declare(strict_types=1);

use App\Models\User;
use Laravel\Pennant\Feature;

test('example-beta-access feature flag is active for @example.com emails', function (): void {
    $user = User::factory()->create(['email' => 'test@example.com']);

    expect(Feature::for($user)->active('example-beta-access'))->toBeTrue();
});

test('example-beta-access feature flag is inactive for other emails', function (): void {
    $user = User::factory()->create(['email' => 'test@gmail.com']);

    expect(Feature::for($user)->active('example-beta-access'))->toBeFalse();
});
