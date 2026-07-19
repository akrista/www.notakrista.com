<?php

declare(strict_types=1);

use App\Services\TodoticketCalculator;

test('it returns zero values for zero or negative inputs', function (): void {
    $zeroResult = TodoticketCalculator::calculate(0.0);
    expect($zeroResult)->toBe([
        'retiroOptimo' => 0.0,
        'comision' => 0.0,
        'restante' => 0.0,
    ]);

    $negativeResult = TodoticketCalculator::calculate(-50.0);
    expect($negativeResult)->toBe([
        'retiroOptimo' => 0.0,
        'comision' => 0.0,
        'restante' => 0.0,
    ]);
});

test('it calculates optimal values for standard input 100', function (): void {
    $result = TodoticketCalculator::calculate(100.0);
    expect($result['retiroOptimo'])->toBe(99.40)
        ->and($result['comision'])->toBe(0.60)
        ->and($result['restante'])->toBe(0.00);
});

test('it calculates optimal values for standard input 100.25', function (): void {
    $result = TodoticketCalculator::calculate(100.25);
    expect($result['retiroOptimo'])->toBe(99.65)
        ->and($result['comision'])->toBe(0.60)
        ->and($result['restante'])->toBe(0.00);
});

test('it calculates values when there is a small remainder', function (): void {
    // Let's test a case where there might be a remainder
    $result = TodoticketCalculator::calculate(10.0);
    // 10 / 1.006 = 9.9403 -> 9.94.
    // Commission: 9.94 * 0.006 = 0.05964 -> round to 0.06.
    // 9.94 + 0.06 = 10.0. Remaining = 0.00.
    expect($result['retiroOptimo'])->toBe(9.94)
        ->and($result['comision'])->toBe(0.06)
        ->and($result['restante'])->toBe(0.00);
});

test('it checks if comision plus retiroOptimo ever exceeds total', function (): void {
    $exceededCount = 0;
    for ($i = 1; $i <= 100000; $i++) {
        $total = $i / 100;
        $result = TodoticketCalculator::calculate($total);
        $sum = round(($result['retiroOptimo'] + $result['comision']) * 100) / 100;
        if ($sum > $total) {
            $exceededCount++;
        }
    }

    expect($exceededCount)->toBe(0);
});

test('it verifies that positive remainders are mathematically possible for some inputs', function (): void {
    $resultFor84 = TodoticketCalculator::calculate(0.84);
    expect($resultFor84['restante'])->toBe(0.01)
        ->and($resultFor84['retiroOptimo'] + $resultFor84['comision'])->toBeLessThanOrEqual(0.84);

    $positiveRemainderCount = 0;
    for ($i = 1; $i <= 50000; $i++) {
        $total = $i / 100;
        $result = TodoticketCalculator::calculate($total);
        if ($result['restante'] > 0) {
            $positiveRemainderCount++;
        }
    }

    expect($positiveRemainderCount)->toBeGreaterThan(0);
});
