<?php

declare(strict_types=1);

use App\Models\User;

test('registration screen can be rendered', function (): void {
    $response = $this->get(route('register'));

    $response->assertOk();
});

test('registration screen has defaults in non-production environment', function (): void {
    $response = $this->get(route('register'));

    $response->assertOk();
    $response->assertSee('devuser');
    $response->assertSee('dev@example.com');
});

test('new users can register', function (): void {
    $response = $this->post(route('register.store'), [
        'username' => 'johndoe',
        'firstname' => 'John',
        'lastname' => 'Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::query()->where('email', 'test@example.com')->first();

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('dashboard', absolute: false));

    $this->assertAuthenticated();
});
