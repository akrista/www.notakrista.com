<?php

declare(strict_types=1);

use App\Enums\FilamentMode;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

if (FilamentMode::fromConfig()->isAdmin()) {
    Route::view('/', 'welcome')->name('home');

    Route::prefix('team/{current_team}')
        ->middleware(['auth', 'verified', EnsureTeamMembership::class])
        ->group(function (): void {
            Route::view('dashboard', 'dashboard')->name('dashboard');
        });

    Route::middleware(['auth'])->group(function (): void {
        Route::livewire('invitations/{invitation}/accept', 'pages::teams.accept-invitation')->name('invitations.accept');
    });

    require __DIR__ . '/settings.php';
}
