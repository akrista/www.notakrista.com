<?php

declare(strict_types=1);

it('has a non-empty session cookie name configured', function (): void {
    $cookie = config('session.cookie');

    expect($cookie)
        ->not->toBeNull()
        ->not->toBeEmpty()
        ->toBeString();

    expect((string) $cookie)->not->toBe('-session');
});
