<?php

declare(strict_types=1);

namespace App\Filament\Colors;

final class Gruvbox
{
    public const array SignalOrange = [
        50 => 'oklch(0.965 0.020 50)',
        100 => 'oklch(0.925 0.050 50)',
        200 => 'oklch(0.860 0.090 50)',
        300 => 'oklch(0.780 0.130 50)',
        400 => 'oklch(0.700 0.180 50)',
        500 => 'oklch(0.620 0.180 50)',
        600 => 'oklch(0.540 0.180 45)',
        700 => 'oklch(0.490 0.180 45)',
        800 => 'oklch(0.400 0.150 45)',
        900 => 'oklch(0.300 0.120 45)',
        950 => 'oklch(0.220 0.080 45)',
    ];

    public const array MossAqua = [
        50 => 'oklch(0.965 0.018 145)',
        100 => 'oklch(0.925 0.035 145)',
        200 => 'oklch(0.870 0.055 145)',
        300 => 'oklch(0.820 0.075 145)',
        400 => 'oklch(0.760 0.090 145)',
        500 => 'oklch(0.640 0.085 150)',
        600 => 'oklch(0.510 0.080 150)',
        700 => 'oklch(0.430 0.070 150)',
        800 => 'oklch(0.350 0.060 150)',
        900 => 'oklch(0.270 0.050 150)',
        950 => 'oklch(0.190 0.040 150)',
    ];

    public const array CathodeYellow = [
        50 => 'oklch(0.965 0.020 80)',
        100 => 'oklch(0.920 0.060 80)',
        200 => 'oklch(0.880 0.100 80)',
        300 => 'oklch(0.840 0.130 80)',
        400 => 'oklch(0.810 0.150 80)',
        500 => 'oklch(0.710 0.150 75)',
        600 => 'oklch(0.620 0.140 70)',
        700 => 'oklch(0.530 0.120 65)',
        800 => 'oklch(0.430 0.100 60)',
        900 => 'oklch(0.330 0.080 55)',
        950 => 'oklch(0.230 0.060 50)',
    ];

    public const array TerminalRed = [
        50 => 'oklch(0.965 0.020 25)',
        100 => 'oklch(0.910 0.060 25)',
        200 => 'oklch(0.840 0.110 25)',
        300 => 'oklch(0.760 0.170 25)',
        400 => 'oklch(0.690 0.200 25)',
        500 => 'oklch(0.640 0.220 25)',
        600 => 'oklch(0.500 0.200 26)',
        700 => 'oklch(0.380 0.180 28)',
        800 => 'oklch(0.310 0.140 28)',
        900 => 'oklch(0.230 0.100 28)',
        950 => 'oklch(0.160 0.060 28)',
    ];

    public const array CalmBlue = [
        50 => 'oklch(0.965 0.010 230)',
        100 => 'oklch(0.920 0.025 230)',
        200 => 'oklch(0.860 0.040 230)',
        300 => 'oklch(0.790 0.050 230)',
        400 => 'oklch(0.730 0.050 215)',
        500 => 'oklch(0.680 0.050 200)',
        600 => 'oklch(0.540 0.080 220)',
        700 => 'oklch(0.450 0.090 230)',
        800 => 'oklch(0.360 0.080 235)',
        900 => 'oklch(0.270 0.060 240)',
        950 => 'oklch(0.180 0.040 245)',
    ];

    public const array Workshop = [
        50 => 'oklch(0.985 0.005 70)',
        100 => 'oklch(0.955 0.030 95)',
        200 => 'oklch(0.920 0.030 90)',
        300 => 'oklch(0.870 0.025 85)',
        400 => 'oklch(0.760 0.020 80)',
        500 => 'oklch(0.650 0.020 80)',
        600 => 'oklch(0.530 0.013 65)',
        700 => 'oklch(0.420 0.010 70)',
        800 => 'oklch(0.310 0.008 70)',
        900 => 'oklch(0.230 0.005 70)',
        950 => 'oklch(0.150 0.005 70)',
    ];

    public const array WorkshopDark = [
        50 => 'oklch(0.985 0.005 65)',
        100 => 'oklch(0.910 0.030 88)',
        200 => 'oklch(0.800 0.020 80)',
        300 => 'oklch(0.650 0.020 80)',
        400 => 'oklch(0.500 0.015 70)',
        500 => 'oklch(0.420 0.010 70)',
        600 => 'oklch(0.380 0.008 60)',
        700 => 'oklch(0.350 0.008 65)',
        800 => 'oklch(0.320 0.005 65)',
        900 => 'oklch(0.290 0.005 65)',
        950 => 'oklch(0.270 0.005 65)',
    ];

    /**
     * @return array<string, array<int, string>>
     */
    public static function palette(): array
    {
        return [
            'primary' => self::SignalOrange,
            'gray' => self::Workshop,
            'success' => self::MossAqua,
            'warning' => self::CathodeYellow,
            'danger' => self::TerminalRed,
            'info' => self::CalmBlue,
        ];
    }
}
