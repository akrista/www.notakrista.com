<?php

declare(strict_types=1);

namespace App\Filament\Resources\MtgCards\Pages;

use App\Filament\Resources\MtgCards\MtgCardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListMtgCards extends ListRecords
{
    #[Override]
    protected static string $resource = MtgCardResource::class;

    /**
     * @return array<int, CreateAction>
     */
    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('app.create_mtg_card')),
        ];
    }
}
