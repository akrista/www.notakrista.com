<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\Role;
use App\Services\PermissionRegistry;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Clear cached permissions to avoid state issues
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $registry = resolve(PermissionRegistry::class);
        $permissions = [];

        // 1. Gather all dynamic permissions from the registry
        foreach ($registry->getResources() as $perms) {
            foreach (array_keys($perms) as $key) {
                $permissions[] = $key;
            }
        }

        foreach (['pages_tab', 'widgets_tab', 'custom_permissions_tab'] as $tab) {
            foreach ($registry->getPermissionsForTab($tab) as $key) {
                $permissions[] = $key;
            }
        }

        // 2. Insert permissions into the database
        $permissionModels = [];
        foreach ($permissions as $permissionName) {
            $permissionModels[] = Permission::query()->firstOrCreate([
                'name' => $permissionName,
                'guard_name' => 'web',
            ]);
        }

        // 3. Assign permissions to all admin roles
        $adminRoles = Role::query()->where('name', 'admin')->get();
        foreach ($adminRoles as $role) {
            $role->syncPermissions($permissionModels);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $registry = resolve(PermissionRegistry::class);
        $permissions = [];

        foreach ($registry->getResources() as $perms) {
            foreach (array_keys($perms) as $key) {
                $permissions[] = $key;
            }
        }

        foreach (['pages_tab', 'widgets_tab', 'custom_permissions_tab'] as $tab) {
            foreach ($registry->getPermissionsForTab($tab) as $key) {
                $permissions[] = $key;
            }
        }

        // Detach permissions from admin roles
        $adminRoles = Role::query()->where('name', 'admin')->get();
        foreach ($adminRoles as $role) {
            $role->revokePermissionTo($permissions);
        }

        // Delete permissions
        Permission::query()->whereIn('name', $permissions)->delete();

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
