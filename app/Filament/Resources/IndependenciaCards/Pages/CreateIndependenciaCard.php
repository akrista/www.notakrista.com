<?php

declare(strict_types=1);

namespace App\Filament\Resources\IndependenciaCards\Pages;

use App\Filament\Resources\IndependenciaCards\IndependenciaCardResource;
use Filament\Resources\Pages\CreateRecord;
use Override;

class CreateIndependenciaCard extends CreateRecord
{
    #[Override]
    protected static string $resource = IndependenciaCardResource::class;
}
