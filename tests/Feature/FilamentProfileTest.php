<?php

declare(strict_types=1);

use App\Filament\Pages\Auth\EditProfile;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;

test('filament profile page displays username and avatar fields', function (): void {
    $user = User::factory()->create();

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->assertFormSet([
            'username' => $user->username,
            'firstname' => $user->firstname,
            'lastname' => $user->lastname,
            'email' => $user->email,
        ]);
});

test('filament profile page can update username and avatar', function (): void {
    Storage::fake('public');

    $user = User::factory()->create([
        'username' => 'oldusername',
    ]);

    $avatar = UploadedFile::fake()->image('avatar.jpg');

    Livewire::actingAs($user)
        ->test(EditProfile::class)
        ->fillForm([
            'username' => 'newusername',
            'firstname' => 'UpdatedFirst',
            'lastname' => 'UpdatedLast',
            'avatar_url' => $avatar,
        ])
        ->call('save')
        ->assertHasNoFormErrors();

    $user->refresh();

    expect($user->username)->toBe('newusername');
    expect($user->firstname)->toBe('UpdatedFirst');
    expect($user->lastname)->toBe('UpdatedLast');
    expect($user->avatar_url)->not->toBeNull();

    Storage::disk('public')->assertExists($user->avatar_url);
});
