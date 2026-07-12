<?php

declare(strict_types=1);

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Facades\Artisan;

test('horizon:snapshot is scheduled to run every five minutes', function (): void {
    // Trigger Artisan to boot the console application and populate the schedule callbacks
    Artisan::call('schedule:list');

    $schedule = resolve(Schedule::class);

    $event = collect($schedule->events())->first(
        fn ($event): bool => str_contains((string) $event->command, 'horizon:snapshot')
    );

    expect($event)->not->toBeNull();
    expect($event->expression)->toBe('*/5 * * * *');
});
