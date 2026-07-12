<?php

declare(strict_types=1);

use App\Models\User;

test('users can be soft deleted', function (): void {
    /** @var User $user */
    $user = User::factory()->create();

    $user->delete();

    $this->assertDatabaseHas('users', [
        'id' => $user->id,
    ]);

    $this->assertNotNull($user->deleted_at);
    $this->assertTrue($user->trashed());

    $this->assertNull(User::query()->find($user->id));
    $this->assertNotNull(User::withTrashed()->find($user->id));
});
