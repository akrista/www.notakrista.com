<?php

declare(strict_types=1);

use App\Enums\BillCadence;

test('bill cadence advances dates by the expected interval', function (BillCadence $cadence, string $expected): void {
    $from = new DateTimeImmutable('2026-01-15');

    expect($cadence->advance($from)->format('Y-m-d'))->toBe($expected);
})->with([
    'weekly' => [BillCadence::Weekly, '2026-01-22'],
    'biweekly' => [BillCadence::Biweekly, '2026-01-29'],
    'monthly' => [BillCadence::Monthly, '2026-02-15'],
    'bimonthly' => [BillCadence::Bimonthly, '2026-03-15'],
    'quarterly' => [BillCadence::Quarterly, '2026-04-15'],
    'yearly' => [BillCadence::Yearly, '2027-01-15'],
    'once' => [BillCadence::Once, '2026-01-15'],
]);
