<?php

declare(strict_types=1);

namespace App\Filament\Resources\YugiohCards\Pages;

use App\Filament\Resources\YugiohCards\YugiohCardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListYugiohCards extends ListRecords
{
    #[Override]
    protected static string $resource = YugiohCardResource::class;

    /**
     * @return array<int, CreateAction>
     */
    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label(__('app.create_yugioh_card')),
        ];
    }
}
