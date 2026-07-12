<?php

declare(strict_types=1);

use App\Models\User;

test('guests are unauthorized to view scramble dashboard', function (): void {
    $response = $this->get('/docs/api');

    $response->assertStatus(403);
});

test('configured superadmin is authorized to view scramble dashboard', function (): void {
    $adminEmail = config('bizkit.admin_email') ?: 'admin@example.com';
    $user = User::query()->where('email', $adminEmail)->first()
        ?? User::factory()->create(['email' => $adminEmail]);

    $response = $this->actingAs($user)->get('/docs/api');

    $response->assertOk();
});

test('unauthorized logged-in users are forbidden from viewing scramble dashboard', function (): void {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/docs/api');

    $response->assertStatus(403);
});
