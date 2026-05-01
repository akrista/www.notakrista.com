<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Services\PermissionRegistry;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Override;
use Spatie\Permission\Models\Permission;

final class EditRole extends EditRecord
{
    public Collection $permissions;

    #[Override]
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissions = $this->extractPermissionsFromData($data);

        return Arr::only($data, ['name', 'guard_name']);
    }

    protected function afterSave(): void
    {
        $guardName = $this->data['guard_name'] ?? 'web';

        $permissionModels = $this->permissions->map(
            fn (string $permission): Permission => Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName,
            ])
        );

        $this->record->syncPermissions($permissionModels);
    }

    private function extractPermissionsFromData(array $data): Collection
    {
        $registry = resolve(PermissionRegistry::class);
        $excludedKeys = ['name', 'guard_name', 'select_all'];

        // Get all resource FQCNs to identify permission arrays
        $resourceKeys = collect($registry->getResources())->keys()->all();

        return collect($data)
            ->filter(
                fn (mixed $value, string $key): bool => ! in_array($key, $excludedKeys, true)
                && (in_array($key, $resourceKeys, true) || in_array($key, ['pages_tab', 'widgets_tab', 'custom_permissions_tab'], true))
            )
            ->values()
            ->flatten()
            ->filter()
            ->unique();
    }
}
