<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    public function up(): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $names = [
            'view_any_category',
            'view_category',
            'create_category',
            'update_category',
            'delete_category',
            'delete_any_category',
            'force_delete_category',
            'force_delete_any_category',
            'restore_category',
            'restore_any_category',
        ];

        $permissions = [];
        foreach ($names as $name) {
            $permissions[] = Permission::query()->firstOrCreate([
                'name' => $name,
                'guard_name' => 'web',
            ]);
        }

        foreach (Role::query()->where('name', 'admin')->get() as $role) {
            $role->givePermissionTo($permissions);
        }
    }

    public function down(): void
    {
        $names = [
            'view_any_category',
            'view_category',
            'create_category',
            'update_category',
            'delete_category',
            'delete_any_category',
            'force_delete_category',
            'force_delete_any_category',
            'restore_category',
            'restore_any_category',
        ];

        foreach (Role::query()->where('name', 'admin')->get() as $role) {
            $role->revokePermissionTo($names);
        }

        Permission::query()->whereIn('name', $names)->delete();

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }
};
