<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\YugiohCard;
use App\Services\Yugioh\YgoprodeckService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'yugioh:sync-cards')]
#[Description('Sync Yu-Gi-Oh card details and prices with YGOPRODeck.')]
#[Signature('yugioh:sync-cards')]
final class SyncYugiohCardsCommand extends Command
{
    public function handle(YgoprodeckService $service): int
    {
        $this->info('Fetching Yu-Gi-Oh cards from the database...');

        $cards = YugiohCard::all();

        if ($cards->isEmpty()) {
            $this->warn('No Yu-Gi-Oh cards found in the database.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Syncing %d cards with YGOPRODeck...', $cards->count()));

        $service->syncCards($cards);

        $this->info('Sync completed successfully.');

        return self::SUCCESS;
    }
}
