<?php

declare(strict_types=1);

namespace App\Filament\Resources\HomePhrases\Pages;

use App\Filament\Resources\HomePhrases\HomePhraseResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListHomePhrases extends ListRecords
{
    #[Override]
    protected static string $resource = HomePhraseResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
