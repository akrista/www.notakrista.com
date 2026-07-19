<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\YugiohCard;
use App\Services\Yugioh\YgoprodeckService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\Attributes\Backoff;
use Illuminate\Queue\Attributes\Timeout;
use Illuminate\Queue\Attributes\Tries;

#[Backoff([10, 30])]
#[Timeout(60)]
#[Tries(3)]
final class SyncYugiohCardsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     *
     * @param  array<int, string>  $cardIds
     */
    public function __construct(public array $cardIds) {}

    /**
     * Execute the job.
     */
    public function handle(YgoprodeckService $service): void
    {
        $cards = YugiohCard::query()
            ->whereIn('id', $this->cardIds)
            ->get();

        if ($cards->isNotEmpty()) {
            $service->syncCards($cards);
        }
    }
}
