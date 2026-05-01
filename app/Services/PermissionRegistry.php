<?php

declare(strict_types=1);

namespace App\Services;

use App\Filament\Pages\Settings\General;
use Filament\Facades\Filament;
use Filament\Widgets\WidgetConfiguration;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

final class PermissionRegistry
{
    /**
     * Default policy methods for resources.
     * Format: entity.action (e.g., users.view, users.create)
     *
     * @var array<string>
     */
    private const array DEFAULT_RESOURCE_PERMISSIONS = [
        'view',
        'create',
        'edit',
        'delete',
    ];

    /**
     * Resources to exclude from permission generation.
     *
     * @var array<string>
     */
    private array $excludedResources = [];

    /**
     * Pages to exclude from permission generation.
     * By default, excludes settings pages as they handle their own permissions.
     *
     * @var array<string>
     */
    private array $excludedPages = [
        General::class,
    ];

    /**
     * Widgets to exclude from permission generation.
     *
     * @var array<string>
     */
    private array $excludedWidgets = [];

    /**
     * Custom permissions to include.
     * By default includes settings permissions.
     *
     * @var array<string, string>
     */
    private array $customPermissions = [
        'settings.view' => 'View Settings',
        'settings.edit' => 'Edit Settings',
        'horizon.view' => 'View Horizon Dashboard',
        'pulse.view' => 'View Pulse Dashboard',
        'smart-cache.view' => 'View Smart Cache Dashboard',
    ];

    public function excludeResources(array $resources): static
    {
        $this->excludedResources = $resources;

        return $this;
    }

    public function excludePages(array $pages): static
    {
        $this->excludedPages = $pages;

        return $this;
    }

    public function excludeWidgets(array $widgets): static
    {
        $this->excludedWidgets = $widgets;

        return $this;
    }

    /**
     * @param  array<string, string>  $permissions
     */
    public function setCustomPermissions(array $permissions): static
    {
        $this->customPermissions = $permissions;

        return $this;
    }

    /**
     * Get all discovered resources with their permissions.
     *
     * @return array<string, array{resourceFqcn: string, model: string, permissions: array<string, string>}>
     */
    public function getResources(): array
    {
        return once(fn (): array => $this->discoverResources()
            ->reject(fn (string $resource): bool => in_array($resource, $this->excludedResources, true))
            ->mapWithKeys(fn (string $resource): array => [
                $resource => [
                    'resourceFqcn' => $resource,
                    'model' => class_basename($resource::getModel()),
                    'permissions' => $this->getResourcePermissions($resource),
                ],
            ])
            ->sortKeys()
            ->all());
    }

    /**
     * Get all discovered pages with their permissions.
     *
     * @return array<string, array{pageFqcn: string, permissions: array<string, string>}>
     */
    public function getPages(): array
    {
        return once(fn (): array => $this->discoverPages()
            ->reject(fn (string $page): bool => in_array($page, $this->excludedPages, true))
            ->mapWithKeys(fn (string $page): array => [
                $page => [
                    'pageFqcn' => $page,
                    'permissions' => $this->getPagePermissions($page),
                ],
            ])
            ->all());
    }

    /**
     * Get all discovered widgets with their permissions.
     *
     * @return array<string, array{widgetFqcn: string, permissions: array<string, string>}>
     */
    public function getWidgets(): array
    {
        return once(fn (): array => $this->discoverWidgets()
            ->reject(fn (string | WidgetConfiguration $widget): bool => in_array(
                $this->getWidgetClass($widget),
                $this->excludedWidgets,
                true
            ))
            ->mapWithKeys(fn (string | WidgetConfiguration $widget): array => [
                $this->getWidgetClass($widget) => [
                    'widgetFqcn' => $this->getWidgetClass($widget),
                    'permissions' => $this->getWidgetPermissions($widget),
                ],
            ])
            ->all());
    }

    /**
     * Get custom permissions.
     *
     * @return array<string, string>
     */
    public function getCustomPermissions(): array
    {
        return $this->customPermissions;
    }

    /**
     * Get all permissions as a flat array.
     *
     * @return array<string>
     */
    public function getAllPermissions(): array
    {
        $permissions = collect();

        // Resource permissions
        foreach ($this->getResources() as $resource) {
            $permissions = $permissions->merge(array_keys($resource['permissions']));
        }

        // Page permissions
        foreach ($this->getPages() as $page) {
            $permissions = $permissions->merge(array_keys($page['permissions']));
        }

        // Widget permissions
        foreach ($this->getWidgets() as $widget) {
            $permissions = $permissions->merge(array_keys($widget['permissions']));
        }

        // Custom permissions
        $permissions = $permissions->merge(array_keys($this->customPermissions));

        return $permissions->unique()->values()->all();
    }

    /**
     * Get resource permissions with labels.
     * Format: entity.action (e.g., users.view, users.create)
     *
     * @return array<string, string>
     */
    public function getResourcePermissions(string $resource): array
    {
        $model = Str::of(class_basename($resource::getModel()))->plural()->snake()->toString();

        return collect(self::DEFAULT_RESOURCE_PERMISSIONS)
            ->mapWithKeys(fn (string $permission): array => [
                $model . '.' . $permission => Str::of($permission)->title()->toString(),
            ])
            ->all();
    }

    /**
     * Get all resource permissions as a flat array with labels.
     *
     * @return array<string, string>
     */
    public function getAllResourcePermissions(): array
    {
        return collect($this->getResources())
            ->flatMap(fn (array $resource): array => $resource['permissions'])
            ->all();
    }

    /**
     * Get page permissions.
     * Format: page_name.view (e.g., dashboard.view, settings.view)
     *
     * @return array<string, string>
     */
    private function getPagePermissions(string $page): array
    {
        $pageName = Str::of(class_basename($page))->snake()->toString();

        return [
            $pageName . '.view' => Str::of(class_basename($page))->headline()->toString(),
        ];
    }

    /**
     * Get widget permissions.
     * Format: widget_name.view (e.g., fillakit_info.view)
     *
     * @return array<string, string>
     */
    private function getWidgetPermissions(string | WidgetConfiguration $widget): array
    {
        $widgetClass = $this->getWidgetClass($widget);
        $widgetName = Str::of(class_basename($widgetClass))->snake()->toString();

        return [
            $widgetName . '.view' => Str::of(class_basename($widgetClass))->headline()->toString(),
        ];
    }

    private function discoverResources(): Collection
    {
        return collect(Filament::getResources());
    }

    private function discoverPages(): Collection
    {
        $clusters = collect(Filament::getPages())
            ->map(fn (string $page): ?string => $page::getCluster())
            ->reject(fn (mixed $cluster): bool => is_null($cluster))
            ->unique()
            ->values()
            ->all();

        return collect(Filament::getPages())
            ->reject(fn (string $page): bool => in_array($page, $clusters, true));
    }

    private function discoverWidgets(): Collection
    {
        return collect(Filament::getWidgets());
    }

    private function getWidgetClass(string | WidgetConfiguration $widget): string
    {
        return $widget instanceof WidgetConfiguration
            ? $widget->widget
            : $widget;
    }
}
