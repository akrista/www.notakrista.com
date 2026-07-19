<?php

declare(strict_types=1);

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Migrations\Migration;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * @var list<string>
     */
    private const array RESOURCES = [
        'transaction_category',
        'transaction',
        'schedule',
        'account',
    ];

    /**
     * @var list<string>
     */
    private const array ACTIONS = [
        'view_any',
        'view',
        'create',
        'update',
        'delete',
        'delete_any',
        'force_delete',
        'force_delete_any',
        'restore',
        'restore_any',
    ];

    public function up(): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $permissions = [];
        foreach (self::RESOURCES as $resource) {
            foreach (self::ACTIONS as $action) {
                $permissions[] = Permission::query()->firstOrCreate([
                    'name' => sprintf('%s_%s', $action, $resource),
                    'guard_name' => 'web',
                ]);
            }
        }

        foreach (Role::query()->where('name', 'admin')->get() as $role) {
            $role->givePermissionTo($permissions);
        }
    }

    public function down(): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        foreach (self::RESOURCES as $resource) {
            $names = array_map(
                static fn (string $action): string => sprintf('%s_%s', $action, $resource),
                self::ACTIONS,
            );

            foreach (Role::query()->where('name', 'admin')->get() as $role) {
                $role->revokePermissionTo($names);
            }

            Permission::query()->whereIn('name', $names)->delete();
        }
    }
};
