<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\MtgCard;
use App\Services\Mtg\ScryfallService;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Tries;
use Illuminate\Queue\Attributes\UniqueFor;

#[Backoff([10, 30])]
#[Timeout(80)]
#[Tries(3)]
#[UniqueFor(3600)]
final class SyncMtgCardsJob implements ShouldBeUnique, ShouldQueue
{
    use Queueable;

    /**
     * The unique ID of the job.
     */
    public function uniqueId(): string
    {
        return 'sync-mtg-cards';
    }

    /**
     * Execute the job.
     */
    public function handle(ScryfallService $service): void
    {
        $cards = MtgCard::all();

        if ($cards->isNotEmpty()) {
            $service->syncCards($cards);
        }
    }
}
