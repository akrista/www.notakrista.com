<?php

declare(strict_types=1);

use App\Models\TeamInvitation;
use Illuminate\Support\Facades\Schedule;

Schedule::call(function (): void {
    TeamInvitation::query()
        ->whereNotNull('expires_at')
        ->where('expires_at', '<', now())
        ->delete();
})->daily()->description('Delete expired team invitations.');

Schedule::command('budget:post-schedules')
    ->dailyAt('06:00')
    ->timezone(config('app.timezone'))
    ->description('Post every active, auto-post schedule whose next_run_on is on or before today.');

Schedule::command('mtg:sync-cards')
    ->weekly()
    ->timezone(config('app.timezone'))
    ->description('Sync MTG card details and prices with Scryfall.');

Schedule::command('yugioh:sync-cards')
    ->weekly()
    ->timezone(config('app.timezone'))
    ->description('Sync Yu-Gi-Oh card details and prices with YGOPRODeck.');
