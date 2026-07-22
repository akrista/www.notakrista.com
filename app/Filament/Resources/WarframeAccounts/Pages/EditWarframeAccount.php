<?php

declare(strict_types=1);

namespace App\Filament\Resources\WarframeAccounts\Pages;

use App\Filament\Resources\WarframeAccounts\WarframeAccountResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Override;

final class EditWarframeAccount extends EditRecord
{
    #[Override]
    protected static string $resource = WarframeAccountResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
