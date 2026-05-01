<?php

declare(strict_types=1);

use App\Http\Controllers\ProjectsController;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

if (!config('fillakit.only_filament')) {
    // Livewire (Blade)
    Route::view('/', 'welcome')->name('home');
    Route::get('/projects', [ProjectsController::class, 'index'])->name('projects');

    // React pages — uses app-react.blade.php root view
    Route::get('/react', static fn() => Inertia::render('react/Welcome')->rootView('app-react'))->name('react.home');

    // Vue pages — uses app-vue.blade.php root view
    Route::get('/vue', static fn() => Inertia::render('vue/Welcome')->rootView('app-vue'))->name('vue.home');

    // Svelte pages — uses app-svelte.blade.php root view
    Route::get('/svelte', static fn() => Inertia::render('svelte/Welcome')->rootView('app-svelte'))->name('svelte.home');
}

Route::get('switch-language/{code}', static function (string $code): RedirectResponse {
    session(['locale' => $code]);

    return redirect(request()->header('referer', url('/')));
})->name('language-switcher.switch');
