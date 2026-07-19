<?php

declare(strict_types=1);

namespace App\Filament\Resources\IndependenciaCards\Pages;

use App\Filament\Resources\IndependenciaCards\IndependenciaCardResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

class ListIndependenciaCards extends ListRecords
{
    #[Override]
    protected static string $resource = IndependenciaCardResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
