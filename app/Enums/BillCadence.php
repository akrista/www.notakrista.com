<?php

declare(strict_types=1);

namespace App\Enums;

use DateTimeInterface;
use Illuminate\Support\Facades\Date;

enum BillCadence: string
{
    case Weekly = 'weekly';
    case Biweekly = 'biweekly';
    case Monthly = 'monthly';
    case Bimonthly = 'bimonthly';
    case Quarterly = 'quarterly';
    case Yearly = 'yearly';
    case Once = 'once';

    public function label(): string
    {
        return __('app.bill_cadence_' . $this->value);
    }

    public function colorToken(): string
    {
        return match ($this) {
            self::Weekly, self::Biweekly => 'blue',
            self::Monthly, self::Bimonthly => 'accent',
            self::Quarterly, self::Yearly => 'primary',
            self::Once => 'muted',
        };
    }

    /**
     * Advance a date by one cadence interval.
     */
    public function advance(DateTimeInterface $from): DateTimeInterface
    {
        $base = Date::instance($from);

        return match ($this) {
            self::Weekly => $base->modify('+1 week'),
            self::Biweekly => $base->modify('+2 weeks'),
            self::Monthly => $base->modify('+1 month'),
            self::Bimonthly => $base->modify('+2 months'),
            self::Quarterly => $base->modify('+3 months'),
            self::Yearly => $base->modify('+1 year'),
            self::Once => $base,
        };
    }
}
