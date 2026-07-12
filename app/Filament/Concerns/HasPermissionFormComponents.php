<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use App\Models\Role;
use App\Services\PermissionRegistry;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Components\Utilities\Set;
use Filament\Support\Icons\Heroicon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

trait HasPermissionFormComponents
{
    /**
     * Get the select all toggle component.
     */
    public static function getSelectAllToggle(): Toggle
    {
        return Toggle::make('select_all')
            ->label(__('fields.select_all'))
            ->live()
            ->afterStateHydrated(function (Toggle $component, ?Model $record, Get $get): void {
                $registry = resolve(PermissionRegistry::class);
                $allPermissionsCount = 0;
                $selectedPermissionsCount = 0;

                foreach ($registry->getResources() as $resourceClass => $perms) {
                    $allPermissionsCount += count($perms);
                    /** @var array<int, string> $selected */
                    $selected = $get($resourceClass) ?? [];
                    $selectedPermissionsCount += count($selected);
                }

                foreach (['pages_tab', 'widgets_tab', 'custom_permissions_tab'] as $tab) {
                    $allPermissionsCount += count($registry->getPermissionsForTab($tab));
                    /** @var array<int, string> $selected */
                    $selected = $get($tab) ?? [];
                    $selectedPermissionsCount += count($selected);
                }

                $component->state($allPermissionsCount > 0 && $allPermissionsCount === $selectedPermissionsCount);
            })
            ->afterStateUpdated(function (bool $state, Set $set): void {
                $registry = resolve(PermissionRegistry::class);

                foreach ($registry->getResources() as $resourceClass => $perms) {
                    $set($resourceClass, $state ? array_keys($perms) : []);
                }

                foreach (['pages_tab', 'widgets_tab', 'custom_permissions_tab'] as $tab) {
                    $set($tab, $state ? $registry->getPermissionsForTab($tab) : []);
                }
            });
    }

    /**
     * Get the tabs component with all permission checkboxes grouped by resource, page, widget, or custom.
     */
    public static function getPermissionFormComponents(): Tabs
    {
        $registry = resolve(PermissionRegistry::class);
        $tabs = [];

        // Tab 1: Resources
        $resourceSections = [];
        foreach ($registry->getResources() as $resourceClass => $permissions) {
            $resourceName = class_basename($resourceClass);
            $displayName = str_replace('Resource', '', $resourceName);
            $displayNamePlural = Str::headline($displayName);

            $resourceSections[] = Section::make($displayNamePlural)
                ->collapsible()
                ->compact()
                ->schema([
                    CheckboxList::make($resourceClass)
                        ->hiddenLabel()
                        ->options($permissions)
                        ->columns([
                            'default' => 1,
                            'sm' => 2,
                            'lg' => 3,
                        ])
                        ->afterStateHydrated(function (CheckboxList $component, ?Model $record) use ($permissions): void {
                            $rolePermissions = self::resolveRolePermissions($record);
                            if ($rolePermissions === null) {
                                return;
                            }

                            $component->state(array_intersect(array_keys($permissions), $rolePermissions));
                        }),
                ]);
        }

        $tabs[] = Tab::make(__('fields.resources'))
            ->icon(Heroicon::OutlinedCpuChip)
            ->schema($resourceSections);

        // Tab 2: Pages
        $pagesPermissions = $registry->getPages();
        if (count($pagesPermissions) > 0) {
            $tabs[] = Tab::make(__('fields.pages'))
                ->icon(Heroicon::OutlinedDocumentText)
                ->schema([
                    CheckboxList::make('pages_tab')
                        ->hiddenLabel()
                        ->options($pagesPermissions)
                        ->columns([
                            'default' => 1,
                            'sm' => 2,
                        ])
                        ->afterStateHydrated(function (CheckboxList $component, ?Model $record) use ($pagesPermissions): void {
                            $rolePermissions = self::resolveRolePermissions($record);
                            if ($rolePermissions === null) {
                                return;
                            }

                            $component->state(array_intersect(array_keys($pagesPermissions), $rolePermissions));
                        }),
                ]);
        }

        // Tab 3: Widgets
        $widgetsPermissions = $registry->getWidgets();
        if (count($widgetsPermissions) > 0) {
            $tabs[] = Tab::make(__('fields.widgets'))
                ->icon(Heroicon::OutlinedChartBar)
                ->schema([
                    CheckboxList::make('widgets_tab')
                        ->hiddenLabel()
                        ->options($widgetsPermissions)
                        ->columns([
                            'default' => 1,
                            'sm' => 2,
                        ])
                        ->afterStateHydrated(function (CheckboxList $component, ?Model $record) use ($widgetsPermissions): void {
                            $rolePermissions = self::resolveRolePermissions($record);
                            if ($rolePermissions === null) {
                                return;
                            }

                            $component->state(array_intersect(array_keys($widgetsPermissions), $rolePermissions));
                        }),
                ]);
        }

        // Tab 4: Custom Permissions
        $customPermissions = $registry->getCustomPermissions();
        if (count($customPermissions) > 0) {
            $tabs[] = Tab::make(__('resources.custom_permissions'))
                ->icon(Heroicon::OutlinedKey)
                ->schema([
                    CheckboxList::make('custom_permissions_tab')
                        ->hiddenLabel()
                        ->options($customPermissions)
                        ->columns([
                            'default' => 1,
                            'sm' => 2,
                        ])
                        ->afterStateHydrated(function (CheckboxList $component, ?Model $record) use ($customPermissions): void {
                            $rolePermissions = self::resolveRolePermissions($record);
                            if ($rolePermissions === null) {
                                return;
                            }

                            $component->state(array_intersect(array_keys($customPermissions), $rolePermissions));
                        }),
                ]);
        }

        return Tabs::make('permissions_tabs')
            ->tabs($tabs)
            ->columnSpanFull();
    }

    /**
     * Resolve the names of all permissions currently attached to the given role record.
     *
     * @return array<int, string>|null
     */
    private static function resolveRolePermissions(?Model $record): ?array
    {
        if (! $record instanceof Role) {
            return null;
        }

        $permissionsRelation = $record->relationLoaded('permissions')
            ? $record->permissions
            : $record->permissions()->get();

        return $permissionsRelation
            ->pluck('name')
            ->map(static fn(mixed $name): string => is_string($name) ? $name : '')
            ->values()
            ->all();
    }
}
