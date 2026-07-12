<?php

declare(strict_types=1);

namespace App\Filament\Resources\Roles\Pages;

use App\Filament\Resources\Roles\RoleResource;
use App\Models\Permission;
use App\Models\Role;
use App\Services\PermissionRegistry;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Collection;
use Override;

final class EditRole extends EditRecord
{
    /** @var Collection<int, string> */
    public Collection $permissions;

    #[Override]
    protected static string $resource = RoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        $this->permissions = $this->extractPermissionsFromData($data);

        return [
            'name' => is_string($data['name'] ?? null) ? $data['name'] : '',
            'guard_name' => is_string($data['guard_name'] ?? null) ? $data['guard_name'] : 'web',
        ];
    }

    protected function afterSave(): void
    {
        $guardName = is_string($this->data['guard_name'] ?? null) ? $this->data['guard_name'] : 'web';

        $permissionModels = $this->permissions->map(
            fn (string $permission): Permission => Permission::query()->firstOrCreate([
                'name' => $permission,
                'guard_name' => $guardName,
            ])
        );

        $record = $this->record;
        if ($record instanceof Role) {
            $record->syncPermissions($permissionModels);
        }
    }

    /**
     * @param  array<string, mixed>  $data
     * @return Collection<int, string>
     */
    private function extractPermissionsFromData(array $data): Collection
    {
        $registry = resolve(PermissionRegistry::class);
        $excludedKeys = ['name', 'guard_name', 'select_all'];

        $resourceKeys = collect($registry->getResources())->keys()->all();

        return collect($data)
            ->filter(
                fn (mixed $value, string $key): bool => ! in_array($key, $excludedKeys, true)
                && (in_array($key, $resourceKeys, true) || in_array($key, ['pages_tab', 'widgets_tab', 'custom_permissions_tab'], true))
            )
            ->values()
            ->flatten()
            ->filter()
            ->map(static fn (mixed $value): string => is_string($value) ? $value : '')
            ->unique()
            ->values();
    }
}
