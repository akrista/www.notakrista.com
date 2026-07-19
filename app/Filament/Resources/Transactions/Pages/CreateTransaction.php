<?php

declare(strict_types=1);

namespace App\Filament\Resources\Transactions\Pages;

use App\Actions\Transactions\CreateTransactionAction;
use App\Enums\TransactionDirection;
use App\Filament\Resources\Transactions\TransactionResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;
use Override;

final class CreateTransaction extends CreateRecord
{
    #[Override]
    protected static string $resource = TransactionResource::class;

    #[Override]
    protected function handleRecordCreation(array $data): Model
    {
        $directionRaw = $data['direction'] ?? TransactionDirection::Outflow->value;
        $direction = $directionRaw instanceof TransactionDirection
            ? $directionRaw
            : TransactionDirection::from(is_string($directionRaw) ? $directionRaw : TransactionDirection::Outflow->value);

        $occurredOn = $data['occurred_on'] ?? null;
        if ($occurredOn instanceof Carbon) {
            $occurredOn = $occurredOn->toDateString();
        } elseif (! is_string($occurredOn)) {
            $occurredOn = now()->toDateString();
        }

        $postedAt = $data['posted_at'] ?? null;
        if ($postedAt instanceof Carbon) {
            $postedAt = $postedAt->toDateString();
        } elseif ($postedAt !== null && ! is_string($postedAt)) {
            $postedAt = null;
        }

        return resolve(CreateTransactionAction::class)->handle([
            'account_id' => is_string($data['account_id'] ?? null) ? $data['account_id'] : '',
            'category_id' => isset($data['category_id']) && is_string($data['category_id']) ? $data['category_id'] : null,
            'amount' => is_numeric($data['amount'] ?? null) ? (float) $data['amount'] : 0.0,
            'direction' => $direction,
            'occurred_on' => $occurredOn,
            'posted_at' => $postedAt,
            'is_public' => (bool) ($data['is_public'] ?? false),
            'memo' => is_string($data['memo'] ?? null) ? $data['memo'] : null,
            'payee_name' => is_string($data['payee_name'] ?? null) ? $data['payee_name'] : null,
        ]);
    }

    #[Override]
    protected function getRedirectUrl(): string
    {
        $resource = $this->getResource();
        $url = $resource::getUrl('index');

        return is_string($url) ? $url : '/';
    }
}
