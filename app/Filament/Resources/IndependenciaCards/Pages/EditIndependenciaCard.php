<?php

declare(strict_types=1);

namespace App\Filament\Resources\IndependenciaCards\Pages;

use App\Filament\Resources\IndependenciaCards\IndependenciaCardResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ForceDeleteAction;
use Filament\Actions\RestoreAction;
use Filament\Resources\Pages\EditRecord;
use Override;

class EditIndependenciaCard extends EditRecord
{
    #[Override]
    protected static string $resource = IndependenciaCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
            ForceDeleteAction::make(),
            RestoreAction::make(),
        ];
    }
}
