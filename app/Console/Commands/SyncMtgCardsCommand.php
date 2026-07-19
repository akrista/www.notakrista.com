<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\MtgCard;
use App\Services\Mtg\ScryfallService;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;

#[AsCommand(name: 'mtg:sync-cards')]
#[Description('Sync MTG card details and prices with Scryfall.')]
#[Signature('mtg:sync-cards')]
final class SyncMtgCardsCommand extends Command
{
    public function handle(ScryfallService $service): int
    {
        $this->info('Fetching MTG cards from the database...');

        $cards = MtgCard::all();

        if ($cards->isEmpty()) {
            $this->warn('No MTG cards found in the database.');

            return self::SUCCESS;
        }

        $this->info(sprintf('Syncing %d cards with Scryfall...', $cards->count()));

        $service->syncCards($cards);

        $this->info('Sync completed successfully.');

        return self::SUCCESS;
    }
}
