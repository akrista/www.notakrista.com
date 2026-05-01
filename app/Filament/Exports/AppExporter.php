<?php

declare(strict_types=1);

namespace App\Filament\Exports;

use Filament\Actions\Exports\Exporter;

/**
 * Base exporter for all exporters.
 *
 * @see https://github.com/filamentphp/filament/issues/16930
 * @see https://github.com/laravel/framework/discussions/43502
 */
abstract class AppExporter extends Exporter
{
    final public function getJobQueue(): string
    {
        return 'exports';
    }
}
