<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Str;
use Symfony\Component\Finder\Finder;

final class PermissionRegistry
{
    /**
     * @var array<string, array<string, string>>|null
     */
    private ?array $resources = null;

    /**
     * Get all discoverable Filament resources and their associated permissions.
     *
     * @return array<string, array<string, string>>
     */
    public function getResources(): array
    {
        if ($this->resources !== null) {
            return $this->resources;
        }

        $this->resources = [];
        $resourcesPath = app_path('Filament/Resources');

        if (is_dir($resourcesPath)) {
            $finder = new Finder();
            $finder->files()->in($resourcesPath)->name('*Resource.php');

            foreach ($finder as $file) {
                $relativePath = str_replace(
                    ['/', '.php'],
                    ['\\', ''],
                    $file->getRelativePathname()
                );

                $className = 'App\Filament\Resources\\' . $relativePath;

                if (class_exists($className)) {
                    /** @var string $modelClass */
                    $modelClass = $className::getModel();
                    $modelName = Str::snake(class_basename($modelClass));
                    $modelLabel = Str::headline($modelName);

                    $this->resources[$className] = [
                        'view_any_' . $modelName => __('app.permission_view_any', ['model' => $modelLabel]),
                        'view_' . $modelName => __('app.permission_view', ['model' => $modelLabel]),
                        'create_' . $modelName => __('app.permission_create', ['model' => $modelLabel]),
                        'update_' . $modelName => __('app.permission_update', ['model' => $modelLabel]),
                        'delete_' . $modelName => __('app.permission_delete', ['model' => $modelLabel]),
                        'delete_any_' . $modelName => __('app.permission_delete_any', ['model' => $modelLabel]),
                        'force_delete_' . $modelName => __('app.permission_force_delete', ['model' => $modelLabel]),
                        'force_delete_any_' . $modelName => __('app.permission_force_delete_any', ['model' => $modelLabel]),
                        'restore_' . $modelName => __('app.permission_restore', ['model' => $modelLabel]),
                        'restore_any_' . $modelName => __('app.permission_restore_any', ['model' => $modelLabel]),
                    ];
                }
            }
        }

        return $this->resources;
    }

    /**
     * Get all discoverable Filament pages and their associated permissions.
     *
     * @return array<string, string>
     */
    public function getPages(): array
    {
        return [
            // 'view_dashboard' => 'View Dashboard',
        ];
    }

    /**
     * Get all discoverable Filament widgets and their associated permissions.
     *
     * @return array<string, string>
     */
    public function getWidgets(): array
    {
        return [
            // 'view_user_stats_overview' => 'View User Stats Overview',
        ];
    }

    /**
     * Get all custom permissions.
     *
     * @return array<string, string>
     */
    public function getCustomPermissions(): array
    {
        return [
            'view_horizon' => __('app.permission_view_horizon'),
            'view_pulse' => __('app.permission_view_pulse'),
        ];
    }

    /**
     * Get all permission keys for a specific tab.
     *
     * @return array<int, string>
     */
    public function getPermissionsForTab(string $tab): array
    {
        return match ($tab) {
            'pages_tab' => array_keys($this->getPages()),
            'widgets_tab' => array_keys($this->getWidgets()),
            'custom_permissions_tab' => array_keys($this->getCustomPermissions()),
            default => [],
        };
    }
}
