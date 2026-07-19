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
    private const array RESOURCES = ['language_line'];

    public function up(): void
    {
        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();

        $names = [];
        foreach (self::RESOURCES as $resource) {
            foreach ($this->actions() as $action) {
                $names[] = sprintf('%s_%s', $action, $resource);
            }
        }

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
        $names = [];
        foreach (self::RESOURCES as $resource) {
            foreach ($this->actions() as $action) {
                $names[] = sprintf('%s_%s', $action, $resource);
            }
        }

        foreach (Role::query()->where('name', 'admin')->get() as $role) {
            $role->revokePermissionTo($names);
        }

        Permission::query()->whereIn('name', $names)->delete();

        app()->make(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * @return list<string>
     */
    private function actions(): array
    {
        return [
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
    }
};
