<?php

declare(strict_types=1);

namespace App\Filament\Resources\Schedules\Pages;

use App\Filament\Resources\Schedules\ScheduleResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Override;

final class CreateSchedule extends CreateRecord
{
    #[Override]
    protected static string $resource = ScheduleResource::class;

    #[Override]
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (isset($data['next_run_on']) && $data['next_run_on'] instanceof Carbon) {
            $data['next_run_on'] = $data['next_run_on']->toDateString();
        }

        return $data;
    }

    #[Override]
    protected function handleRecordCreation(array $data): Model
    {
        return self::getModel()::query()->create($data);
    }

    #[Override]
    protected function getRedirectUrl(): string
    {
        return (string) $this->getResource()::getUrl('index');
    }
}
