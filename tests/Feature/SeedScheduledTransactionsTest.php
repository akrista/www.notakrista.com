<?php

declare(strict_types=1);

use Illuminate\Support\Facades\DB;

test('zinli account is seeded', function (): void {
    $zinli = DB::table('accounts')->where('name', 'Zinli')->first();

    expect($zinli)->not->toBeNull()
        ->and($zinli->type)->toBe('wallet')
        ->and($zinli->currency)->toBe('USD')
        ->and($zinli->icon)->toBe('💳')
        ->and($zinli->color_token)->toBe('primary');
});

test('scheduled base transactions are seeded', function (): void {
    $schedules = DB::table('schedules')->get();

    // Verify some specific schedules from the migration
    $youtubePremium = $schedules->firstWhere('name', 'YouTube Premium Subscription');
    expect($youtubePremium)->not->toBeNull()
        ->and((float) $youtubePremium->amount)->toBe(18.09)
        ->and($youtubePremium->cadence)->toBe('monthly')
        ->and($youtubePremium->direction)->toBe('outflow')
        ->and($youtubePremium->next_run_on)->toBe('2026-08-18');

    $tbtbPayment = $schedules->firstWhere('name', 'TBTB Global Job Payment');
    expect($tbtbPayment)->not->toBeNull()
        ->and((float) $tbtbPayment->amount)->toBe(900.0)
        ->and($tbtbPayment->cadence)->toBe('monthly')
        ->and($tbtbPayment->direction)->toBe('inflow')
        ->and($tbtbPayment->next_run_on)->toBe('2026-07-30');

    $psychoanalist = $schedules->firstWhere('name', 'Psychoanalist Consultation');
    expect($psychoanalist)->not->toBeNull()
        ->and((float) $psychoanalist->amount)->toBe(25.0)
        ->and($psychoanalist->cadence)->toBe('biweekly')
        ->and($psychoanalist->direction)->toBe('outflow')
        ->and($psychoanalist->next_run_on)->toBe('2026-07-20');

    $insurance = $schedules->firstWhere('name', 'Mercantil Seguros Panama Insurance Policy');
    expect($insurance)->not->toBeNull()
        ->and((float) $insurance->amount)->toBe(241.13)
        ->and($insurance->cadence)->toBe('quarterly')
        ->and($insurance->direction)->toBe('outflow')
        ->and($insurance->next_run_on)->toBe('2026-10-01');

    $orange = $schedules->firstWhere('name', 'MiOrange Spain Mobile Recharge');
    expect($orange)->not->toBeNull()
        ->and((float) $orange->amount)->toBe(23.29)
        ->and($orange->cadence)->toBe('bimonthly')
        ->and($orange->direction)->toBe('outflow')
        ->and($orange->next_run_on)->toBe('2026-08-01');

    $contabo = $schedules->firstWhere('name', 'Contabo VPS Germany Monthly Debt');
    expect($contabo)->not->toBeNull()
        ->and((float) $contabo->amount)->toBe(17.25)
        ->and($contabo->cadence)->toBe('monthly')
        ->and($contabo->direction)->toBe('outflow')
        ->and($contabo->next_run_on)->toBe('2026-07-24');

    expect($schedules->count())->toBeGreaterThanOrEqual(14);
});
