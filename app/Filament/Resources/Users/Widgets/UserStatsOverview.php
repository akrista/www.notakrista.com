<?php

declare(strict_types=1);

namespace App\Filament\Resources\Users\Widgets;

use App\Models\User;
use Filament\Support\Icons\Heroicon;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

final class UserStatsOverview extends StatsOverviewWidget
{
    protected function getStats(): array
    {
        return [
            Stat::make(__('fields.total_users'), User::withTrashed()->count())
                ->description(__('fields.total_users_desc'))
                ->descriptionIcon(Heroicon::OutlinedUserGroup)
                ->color('primary'),
            Stat::make(__('fields.active_users'), User::query()->count())
                ->description(__('fields.active_users_desc'))
                ->descriptionIcon(Heroicon::OutlinedCheckCircle)
                ->color('success'),
            Stat::make(__('fields.deleted_users'), User::onlyTrashed()->count())
                ->description(__('fields.deleted_users_desc'))
                ->descriptionIcon(Heroicon::OutlinedTrash)
                ->color('danger'),
        ];
    }
}
