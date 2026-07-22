<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Models\WarframeAccount;
use App\Models\WarframeItem;
use App\Models\WarframeUserItem;
use Illuminate\Support\Facades\DB;

final class AlecaFrameImportService
{
    /**
     * Parse lastData.dat content, update single WarframeAccount, and bulk normalize inventory items inside a transaction.
     */
    public function import(string $fileContent, User $user): WarframeAccount
    {
        $parsed = AlecaFrameParser::parse($fileContent);

        return DB::transaction(function () use ($parsed, $user): WarframeAccount {
            $account = WarframeAccount::query()->updateOrCreate(
                ['user_id' => $user->id],
                [
                    'account_name' => $user->name,
                    'active_avatar' => $parsed['ActiveAvatarImageType'] ?? null,
                    'credits' => (int) ($parsed['RegularCredits'] ?? 0),
                    'platinum' => (int) ($parsed['PremiumCredits'] ?? 0),
                    'void_traces' => (int) ($parsed['VoidTraces'] ?? $parsed['Traces'] ?? 0),
                    'endo' => (int) ($parsed['Endo'] ?? 0),
                    'mastery_rank' => (int) (
                        $parsed['MasteryRank']
                        ?? $parsed['PlayerRank']
                        ?? $parsed['PlayerLevel']
                        ?? $parsed['Rank']
                        ?? $parsed['AccountInfo']['MasteryRank']
                        ?? $parsed['AccountInfo']['PlayerRank']
                        ?? $parsed['Player']['Rank']
                        ?? 0
                    ),
                    'boosters' => $parsed['Boosters'] ?? [],
                    'last_imported_at' => now(),
                ]
            );

            // Delete existing user inventory records for a clean overwrite
            $account->userItems()->delete();

            $itemCategoryMappings = [
                'Suits' => 'Warframe',
                'SpaceSuits' => 'Archwing',
                'MechSuits' => 'Nechramech',
                'LongGuns' => 'Primary',
                'Pistols' => 'Secondary',
                'Melee' => 'Melee',
                'SpaceGuns' => 'ArchWeapon',
                'SpaceMelee' => 'ArchWeapon',
                'Sentinels' => 'Companion',
                'KubrowPet' => 'Companion',
                'Upgrades' => 'Mod',
                'RawMods' => 'Mod',
                'RivenMods' => 'Riven',
                'SpecialMods' => 'Riven',
                'LevelKeys' => 'Relic',
                'FlavourItems' => 'Cosmetic',
                'Consumables' => 'Resource',
                'Drones' => 'Gear',
            ];

            // Pre-fetch uniqueName catalog mapping dictionary in one query
            $catalogDictionary = WarframeItem::query()
                ->pluck('id', 'unique_name')
                ->all();

            $now = now()->toDateTimeString();
            $batch = [];

            foreach ($itemCategoryMappings as $jsonKey => $category) {
                if (! isset($parsed[$jsonKey])) {
                    continue;
                }

                if (! is_array($parsed[$jsonKey])) {
                    continue;
                }

                foreach ($parsed[$jsonKey] as $entry) {
                    if (! is_array($entry)) {
                        continue;
                    }

                    if (! isset($entry['ItemType'])) {
                        continue;
                    }

                    $itemType = (string) $entry['ItemType'];
                    $catalogId = $catalogDictionary[$itemType] ?? null;

                    $xp = (int) ($entry['XP'] ?? 0);
                    $level = $this->calculateLevel($category, $xp);
                    $formas = (int) ($entry['Features'] ?? 0);
                    $count = (int) ($entry['ItemCount'] ?? 1);

                    $refinement = $this->extractRefinement($category, $entry);
                    $fusionRank = isset($entry['Rank']) ? (int) $entry['Rank'] : null;
                    $maxFusionRank = isset($entry['FusionLimit']) ? (int) $entry['FusionLimit'] : null;
                    $rivenStats = $this->extractRivenStats($category, $entry);

                    $batch[] = [
                        'warframe_account_id' => $account->id,
                        'warframe_item_id' => $catalogId,
                        'item_type' => $itemType,
                        'category' => $category,
                        'xp' => $xp,
                        'level' => $level,
                        'formas' => $formas,
                        'refinement' => $refinement,
                        'fusion_rank' => $fusionRank,
                        'max_fusion_rank' => $maxFusionRank,
                        'riven_stats' => $rivenStats ? json_encode($rivenStats) : null,
                        'item_count' => $count,
                        'item_data' => json_encode($entry),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];

                    if (count($batch) >= 500) {
                        WarframeUserItem::query()->insert($batch);
                        $batch = [];
                    }
                }
            }

            if ($batch !== []) {
                WarframeUserItem::query()->insert($batch);
            }

            // Update summary counters
            $account->update([
                'total_warframes' => $account->userItems()->whereIn('category', ['Warframe', 'Archwing', 'Nechramech'])->count(),
                'total_weapons' => $account->userItems()->whereIn('category', ['Primary', 'Secondary', 'Melee', 'ArchWeapon'])->count(),
                'total_mods' => $account->userItems()->whereIn('category', ['Mod', 'Riven'])->count(),
                'total_relics' => $account->userItems()->where('category', 'Relic')->sum('item_count'),
            ]);

            return $account->fresh();
        });
    }

    /**
     * Extract relic refinement stage (Intact, Exceptional, Flawless, Radiant).
     */
    private function extractRefinement(string $category, array $entry): ?string
    {
        if ($category !== 'Relic') {
            return null;
        }

        $rank = (int) ($entry['Rank'] ?? 0);

        return match ($rank) {
            1 => 'Exceptional',
            2 => 'Flawless',
            3 => 'Radiant',
            default => 'Intact',
        };
    }

    /**
     * Extract Riven mod attributes and rolls.
     */
    private function extractRivenStats(string $category, array $entry): ?array
    {
        if ($category !== 'Riven') {
            return null;
        }

        return [
            'name' => $entry['ItemName'] ?? $entry['Name'] ?? null,
            'rerolls' => (int) ($entry['Rerolls'] ?? 0),
            'mastery_req' => (int) ($entry['MasteryReq'] ?? 8),
            'attributes' => $entry['Buffs'] ?? $entry['Stats'] ?? [],
        ];
    }

    /**
     * Calculate item level based on category and XP.
     */
    private function calculateLevel(string $category, int $xp): int
    {
        if (in_array($category, ['Warframe', 'Archwing', 'Nechramech', 'Primary', 'Secondary', 'Melee', 'ArchWeapon', 'Companion'], true)) {
            if ($xp >= 300000) {
                return 30;
            }

            return (int) min(30, floor(sqrt($xp / 333.33)));
        }

        return 0;
    }
}
