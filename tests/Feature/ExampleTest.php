<?php

declare(strict_types=1);

test('returns a successful response', function (): void {
    if (config('fillakit.only_filament')) {
        $this->markTestSkipped('Home route not available when ONLY_FILAMENT is enabled');
    }

    $response = $this->get(route('home'));

    $response->assertOk();
});
