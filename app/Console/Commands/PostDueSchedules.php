<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Actions\Schedules\PostScheduleAction;
use App\Models\Schedule;
use App\Models\Transaction;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Description('Post every active schedule whose next_run_on is on or before today.')]
#[Signature('budget:post-schedules {--dry-run : Show what would be posted without writing}')]
final class PostDueSchedules extends Command
{
    public function handle(PostScheduleAction $action): int
    {
        $now = now();

        $due = Schedule::query()
            ->active()
            ->where('next_run_on', '<=', $now->toDateString())
            ->get();

        if ($due->isEmpty()) {
            $this->info('No schedules due.');

            return self::SUCCESS;
        }

        $isDryRun = (bool) $this->option('dry-run');
        $posted = 0;
        $skipped = 0;

        foreach ($due as $schedule) {
            if ($isDryRun) {
                $this->line(sprintf(
                    'Would post schedule #%s "%s" (%s %s) on %s [auto_post=%s]',
                    $schedule->getKey(),
                    $schedule->name,
                    $schedule->amount,
                    $schedule->direction->value,
                    $schedule->next_run_on->toDateString(),
                    $schedule->auto_post ? 'true' : 'false',
                ));
                $posted++;

                continue;
            }

            $transaction = $action->handle($schedule, $now);
            if ($transaction instanceof Transaction) {
                $this->line(sprintf(
                    'Posted schedule #%s "%s" → transaction #%s [posted_at=%s]',
                    $schedule->getKey(),
                    $schedule->name,
                    $transaction->getKey(),
                    $transaction->posted_at?->toDateString() ?? 'pending',
                ));
                $posted++;
            } else {
                $skipped++;
            }
        }

        $this->info(sprintf('Done. Posted: %d. Skipped: %d.', $posted, $skipped));

        return self::SUCCESS;
    }
}
