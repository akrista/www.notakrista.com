<?php

declare(strict_types=1);

use App\Enums\FilamentMode;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\DonationsController;
use App\Http\Controllers\InventoryController;
use App\Http\Middleware\EnsureTeamMembership;
use Illuminate\Support\Facades\Route;

if (FilamentMode::fromConfig()->isAdmin()) {
    Route::view('/', 'welcome')->name('home');
    Route::view('/todoticket', 'todoticket')->name('todoticket');
    Route::get('/character', CharacterController::class)->name('character');
    Route::get('/inventory', InventoryController::class)->name('inventory');
    Route::view('/skills', 'skills')->name('skills');
    Route::view('/stats', 'stats')->name('stats');
    Route::get('/donations', DonationsController::class)->name('donations');
    Route::view('/foundry', 'foundry')->name('foundry');

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
