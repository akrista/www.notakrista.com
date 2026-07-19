<?php

declare(strict_types=1);

namespace App\Filament\Resources\TransactionCategories\Pages;

use App\Filament\Resources\TransactionCategories\TransactionCategoryResource;
use Filament\Resources\Pages\ListRecords;
use Override;

final class ListTransactionCategories extends ListRecords
{
    #[Override]
    protected static string $resource = TransactionCategoryResource::class;
}
