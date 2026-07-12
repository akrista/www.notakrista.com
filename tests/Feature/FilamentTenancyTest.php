<?php

declare(strict_types=1);

use App\Filament\Pages\Tenancy\RegisterTeam;
use App\Models\User;
use Livewire\Livewire;

test('guests are redirected to the login page', function (): void {
    $response = $this->get('/admin');
    $response->assertRedirect(route('filament.filament.auth.login'));
});

test('authenticated users with a team are redirected to their tenant dashboard', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    $response = $this
        ->actingAs($user)
        ->get('/admin');

    $response->assertRedirect('/admin/' . $team->slug);
});

test('authenticated users can visit their team dashboard directly', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    $response = $this
        ->actingAs($user)
        ->get('/admin/' . $team->slug);

    $response->assertOk();
});

test('users cannot access other teams dashboards', function (): void {
    $user1 = User::factory()->create();
    $user2 = User::factory()->create();
    $team2 = $user2->personalTeam();

    $response = $this
        ->actingAs($user1)
        ->get('/admin/' . $team2->slug);

    expect($response->status())->toBeGreaterThanOrEqual(400);
});

test('authenticated users can visit their profile page without tenancy error', function (): void {
    $user = User::factory()->create();

    $response = $this
        ->actingAs($user)
        ->get('/admin/profile');

    $response->assertOk();
});

test('authenticated users can register a new team', function (): void {
    $user = User::factory()->create();

    // Clear user's teams so we test the redirect behavior when they have no teams
    $user->teams()->detach();
    $user->update(['current_team_id' => null]);

    // Visited page redirects to registration page when they have no teams
    $response = $this->actingAs($user)->get('/admin');
    $response->assertRedirect('/admin/new');

    // Visit registration page and submit
    Livewire::actingAs($user)
        ->test(RegisterTeam::class)
        ->fillForm([
            'name' => 'New Awesome Team',
        ])
        ->call('register')
        ->assertHasNoErrors()
        ->assertRedirect('/admin/new-awesome-team');

    // Verify team is created in DB
    $this->assertDatabaseHas('teams', [
        'name' => 'New Awesome Team',
        'slug' => 'new-awesome-team',
    ]);
});
