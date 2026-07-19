<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions\Pages;

use App\Enums\TransactionDirection;
use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Carbon;
use Override;

final class EditTransaction extends EditRecord
{
    #[Override]
    protected static string $resource = TransactionResource::class;

    #[Override]
    protected function mutateFormDataBeforeSave(array $data): array
    {
        if (isset($data['direction']) && is_string($data['direction'])) {
            $data['direction'] = TransactionDirection::from($data['direction']);
        }

        if (isset($data['occurred_on']) && $data['occurred_on'] instanceof Carbon) {
            $data['occurred_on'] = $data['occurred_on']->toDateString();
        }

        if (isset($data['posted_at']) && $data['posted_at'] instanceof Carbon) {
            $data['posted_at'] = $data['posted_at']->toDateString();
        } elseif (empty($data['posted_at'])) {
            $data['posted_at'] = null;
        }

        return $data;
    }

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }

    #[Override]
    protected function getRedirectUrl(): string
    {
        return (string) $this->getResource()::getUrl('index');
    }
}
