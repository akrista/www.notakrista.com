<?php

declare(strict_types=1);

namespace App\Filament\Concerns;

use App\Services\PermissionRegistry;
use Filament\Actions\Action;
use Filament\Forms\Components\CheckboxList;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Components\Component;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Set;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\HtmlString;
use Livewire\Component as Livewire;

trait HasPermissionFormComponents
{
    public static function getPermissionFormComponents(): Component
    {
        return Tabs::make('Permissions')
            ->contained()
            ->tabs([
                static::getResourcesTab(),
                static::getPagesTab(),
                static::getWidgetsTab(),
                static::getCustomPermissionsTab(),
            ])
            ->columnSpan('full');
    }

    public static function getSelectAllToggle(): Component
    {
        return Toggle::make('select_all')
            ->onIcon('heroicon-s-shield-check')
            ->offIcon('heroicon-s-shield-exclamation')
            ->label(__('app.select_all'))
            ->helperText(fn (): HtmlString => new HtmlString(__('Enable all permissions for this role.')))
            ->live()
            ->afterStateUpdated(function (Livewire $livewire, Set $set, bool $state): void {
                static::toggleAllPermissions($livewire, $set, $state);
            })
            ->dehydrated(fn (bool $state): bool => $state);
    }

    protected static function getResourcesTab(): Component
    {
        $registry = static::getPermissionRegistry();
        $resources = $registry->getResources();
        $count = collect($resources)->sum(fn (array $resource): int => count($resource['permissions']));

        return Tab::make('resources')
            ->label(__('app.resources'))
            ->visible(fn (): bool => $count > 0)
            ->badge($count)
            ->schema([
                Grid::make()
                    ->schema(static::getResourceSections())
                    ->columns([
                        'sm' => 2,
                        'lg' => 3,
                    ]),
            ]);
    }

    /**
     * @return array<Section>
     */
    protected static function getResourceSections(): array
    {
        $registry = static::getPermissionRegistry();

        return collect($registry->getResources())
            ->map(fn (array $entity): Section => Section::make($entity['model'])
                ->description(fn (): HtmlString => new HtmlString('<span class="text-xs text-gray-500">' . $entity['resourceFqcn'] . '</span>'))
                ->compact()
                ->schema([
                    static::getCheckboxListComponent(
                        name: $entity['resourceFqcn'],
                        options: $entity['permissions'],
                        searchable: false,
                        columns: 2,
                    ),
                ])
                ->columnSpan(1)
                ->collapsible())
            ->toArray();
    }

    protected static function getPagesTab(): Component
    {
        $registry = static::getPermissionRegistry();
        $options = collect($registry->getPages())
            ->flatMap(fn (array $page): array => $page['permissions'])
            ->all();
        $count = count($options);

        return Tab::make('pages')
            ->label(__('app.pages'))
            ->visible(fn (): bool => $count > 0)
            ->badge($count)
            ->schema([
                static::getCheckboxListComponent(
                    name: 'pages_tab',
                    options: $options,
                ),
            ]);
    }

    protected static function getWidgetsTab(): Component
    {
        $registry = static::getPermissionRegistry();
        $options = collect($registry->getWidgets())
            ->flatMap(fn (array $widget): array => $widget['permissions'])
            ->all();
        $count = count($options);

        return Tab::make('widgets')
            ->label(__('app.widgets'))
            ->visible(fn (): bool => $count > 0)
            ->badge($count)
            ->schema([
                static::getCheckboxListComponent(
                    name: 'widgets_tab',
                    options: $options,
                ),
            ]);
    }

    protected static function getCustomPermissionsTab(): Component
    {
        $registry = static::getPermissionRegistry();
        $options = $registry->getCustomPermissions();
        $count = count($options);

        return Tab::make('custom_permissions')
            ->label(__('app.custom'))
            ->visible(fn (): bool => $count > 0)
            ->badge($count)
            ->schema([
                static::getCheckboxListComponent(
                    name: 'custom_permissions_tab',
                    options: $options,
                ),
            ]);
    }

    protected static function getCheckboxListComponent(
        string $name,
        array $options,
        bool $searchable = true,
        int | array | null $columns = null,
    ): Component {
        return CheckboxList::make($name)
            ->hiddenLabel()
            ->options(fn (): array => $options)
            ->searchable($searchable)
            ->live()
            ->afterStateHydrated(function (Component $component, string $operation, ?Model $record, Set $set) use ($options): void {
                static::hydratePermissionState($component, $operation, $options, $record);
                static::updateSelectAllState($component->getLivewire(), $set);
            })
            ->afterStateUpdated(function (Livewire $livewire, Set $set): void {
                static::updateSelectAllState($livewire, $set);
            })
            ->selectAllAction(fn (
                Action $action,
                Component $component,
                Livewire $livewire,
                Set $set
            ) => static::bulkToggleAction($action, $component, $livewire, $set))
            ->deselectAllAction(fn (
                Action $action,
                Component $component,
                Livewire $livewire,
                Set $set
            ) => static::bulkToggleAction($action, $component, $livewire, $set, resetState: true))
            ->dehydrated(fn ($state): bool => filled($state))
            ->bulkToggleable()
            ->gridDirection('row')
            ->columns($columns ?? 4);
    }

    protected static function hydratePermissionState(
        Component $component,
        string $operation,
        array $permissions,
        ?Model $record
    ): void {
        if (! in_array($operation, ['edit', 'view'], true)) {
            return;
        }

        if (blank($record)) {
            return;
        }

        if (! $component->isVisible() || $permissions === []) {
            return;
        }

        $component->state(
            collect($permissions)
                ->filter(fn ($value, $key) => $record->checkPermissionTo($key))
                ->keys()
                ->all()
        );
    }

    protected static function updateSelectAllState(Livewire $livewire, Set $set): void
    {
        $entitiesStates = collect($livewire->form->getFlatComponents())
            ->reduce(function (mixed $counts, Component $component) {
                if ($component instanceof CheckboxList) {
                    $optionsCount = count(array_keys($component->getOptions()));
                    $selectedCount = count(collect($component->getState())->values()->unique()->toArray());
                    $counts[$component->getName()] = $optionsCount === $selectedCount;
                }

                return $counts;
            }, collect())
            ->values();

        $set('select_all', ! $entitiesStates->containsStrict(false));
    }

    protected static function toggleAllPermissions(Livewire $livewire, Set $set, bool $state): void
    {
        $checkboxLists = collect($livewire->form->getFlatComponents())
            ->filter(fn (Component $component): bool => $component instanceof CheckboxList);

        if ($state) {
            $checkboxLists->each(function (CheckboxList $component) use ($set): void {
                $set($component->getName(), array_keys($component->getOptions()));
            });
        } else {
            $checkboxLists->each(fn (CheckboxList $component): CheckboxList => $component->state([]));
        }
    }

    protected static function bulkToggleAction(
        Action $action,
        Component $component,
        Livewire $livewire,
        Set $set,
        bool $resetState = false
    ): void {
        $action
            ->livewireClickHandlerEnabled(true)
            ->action(function () use ($component, $livewire, $set, $resetState): void {
                $component->state($resetState ? [] : array_keys($component->getOptions()));
                static::updateSelectAllState($livewire, $set);
            });
    }

    protected static function getPermissionRegistry(): PermissionRegistry
    {
        return resolve(PermissionRegistry::class);
    }
}
