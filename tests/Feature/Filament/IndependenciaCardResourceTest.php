<?php

declare(strict_types=1);

use App\Filament\Resources\IndependenciaCards\Pages\ListIndependenciaCards;
use App\Models\IndependenciaCard;
use App\Models\Permission;
use App\Models\Role;
use App\Models\User;
use Filament\Facades\Filament;
use Livewire\Livewire;

test('admin can render the independencia cards list page under a team tenant', function (): void {
    $user = User::factory()->create();
    $team = $user->personalTeam();

    setPermissionsTeamId($team->id);
    $role = Role::create(['name' => 'admin', 'guard_name' => 'web', 'team_id' => $team->id]);
    foreach (['view_any_independencia_card', 'view_independencia_card', 'create_independencia_card', 'update_independencia_card', 'delete_independencia_card'] as $perm) {
        $permission = Permission::query()->firstOrCreate(['name' => $perm, 'guard_name' => 'web']);
        $role->givePermissionTo($permission);
    }

    $user->assignRole($role);

    $this->actingAs($user);
    Filament::setCurrentPanel(Filament::getPanel('filament'));
    Filament::setTenant($team);

    $cards = IndependenciaCard::factory()->count(3)->create();

    Livewire::test(ListIndependenciaCards::class)
        ->call('loadTable')
        ->assertOk()
        ->assertCanSeeTableRecords($cards);
});
