<?php

declare(strict_types=1);

use App\Services\PermissionRegistry;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $essentialPermissions = [
            'users.view' => 'Can view users',
            'users.create' => 'Can create users',
            'users.edit' => 'Can edit users',
            'users.delete' => 'Can delete users',
            'roles.view' => 'Can view roles',
            'roles.create' => 'Can create roles',
            'roles.edit' => 'Can edit roles',
            'roles.delete' => 'Can delete roles',
            'permissions.view' => 'Can view permissions',
            'permissions.create' => 'Can create permissions',
            'permissions.edit' => 'Can edit permissions',
            'permissions.delete' => 'Can delete permissions',
            'settings.view' => 'Can view settings',
            'settings.edit' => 'Can edit settings',
            'dashboard.view' => 'Can view dashboard',
        ];

        try {
            $registry = resolve(PermissionRegistry::class);

            $resourcePermissions = $registry->getAllResourcePermissions();
            $customPermissions = $registry->getCustomPermissions();

            $permissionsToCreate = [];

            foreach ($resourcePermissions as $permission => $description) {
                $permissionsToCreate[$permission] = 'Can ' . $description;
            }

            foreach ($customPermissions as $permission => $description) {
                $permissionsToCreate[$permission] = $description;
            }

            $permissionsToCreate = array_merge($essentialPermissions, $permissionsToCreate);

        } catch (Exception) {
            $permissionsToCreate = $essentialPermissions;
        }

        foreach ($permissionsToCreate as $permission => $description) {
            Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => 'web',
                'description' => $description,
            ]);
        }

        $admin = Role::query()->firstOrCreate([
            'name' => 'admin',
            'guard_name' => 'web',
        ], [
            'description' => 'Administrator with full access',
        ]);

        $admin->givePermissionTo(Permission::all());
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
        $tableNames = config('permission.table_names');
        throw_if(empty($tableNames), Exception::class, 'Error: config/permission.php not found and defaults could not be merged. Please publish the package configuration before proceeding, or drop the tables manually.');
        Schema::drop($tableNames['role_has_permissions']);
        Schema::drop($tableNames['model_has_roles']);
        Schema::drop($tableNames['model_has_permissions']);
        Schema::drop($tableNames['roles']);
        Schema::drop($tableNames['permissions']);
    }
};
