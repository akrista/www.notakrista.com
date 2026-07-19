<?php

declare(strict_types=1);

use App\Filament\Resources\Categories\Pages\ListCategories;
use App\Models\Category;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

test('admin can render the categories list page under a team tenant', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    foreach (['view_any_category', 'view_category', 'create_category', 'update_category', 'delete_category'] as $perm) {
        $permission = Permission::query()->firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
    }

    $user->assignRole($role);

    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('filament'));
    Filament::setTenant($team);

    $categories = Category::factory()->count(3)->create();

    Livewire::test(ListCategories::class)
        ->call('loadTable')
        ->assertOk()
        ->assertCanSeeTableRecords($categories);
});
