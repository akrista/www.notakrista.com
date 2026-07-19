<?php

declare(strict_types=1);

namespace App\Filament\Resources\Locales\Pages;

use App\Filament\Resources\Locales\LocaleResource;
use App\Models\Locale;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListLocales extends ListRecords
{
    #[Override]
    protected static string $resource = LocaleResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    if ((bool) ($data['is_default'] ?? false)) {
                        Locale::query()->update(['is_default' => false]);
                    }

                    return $data;
                }),
        ];
    }
}
