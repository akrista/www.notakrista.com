<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Support\Facades\Route;

test('registration routes do not exist', function (): void {
    $this->assertFalse(Route::has('register'));
    $this->assertFalse(Route::has('filament.filament.auth.register'));
});

test('registration screen cannot be rendered', function (): void {
    $response = $this->get('/register');
    $response->assertNotFound();

    $response = $this->get('/admin/register');
    $this->assertTrue($response->isNotFound() || $response->isRedirect());
});

test('new users cannot register', function (): void {
    $response = $this->post('/register', [
        'username' => 'johndoe',
        'firstname' => 'John',
        'lastname' => 'Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertNotFound();

    $this->assertDatabaseMissing(User::class, [
        'email' => 'test@example.com',
    ]);
});
