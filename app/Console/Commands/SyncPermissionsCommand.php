<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Permission;
use App\Models\Role;
use App\Services\PermissionRegistry;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Spatie\Permission\PermissionRegistrar;
use Spatie\Permission\Support\Config;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'bizkit:sync-permissions')]
#[Description('Sync all registered permissions from the registry to the database and assign them to admin roles.')]
#[Signature('bizkit:sync-permissions')]
final class SyncPermissionsCommand extends Command
{
    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting permissions synchronization...');

        // Clear cached permissions to avoid state issues
        resolve(PermissionRegistrar::class)->forgetCachedPermissions();

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

        if ($permissions === []) {
            $this->warn('No permissions found in the registry.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Found %d permissions to sync.', count($permissions)));

        // 2. Insert/Find-or-Create permissions in the database
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
            $teamKey = $role->getAttribute(Config::teamForeignKey());
            $teamDisplay = is_scalar($teamKey) ? (string) $teamKey : 'global';
            $this->line(sprintf('Synced permissions for admin role in team ID: %s', $teamDisplay));
        }

        $this->info('Permissions synchronization completed successfully!');

        // Clear cache again to reflect changes
        resolve(PermissionRegistrar::class)->forgetCachedPermissions();

        return self::SUCCESS;
    }
}
