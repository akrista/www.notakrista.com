<?php

declare(strict_types=1);

namespace App\Filament\Widgets;

use Composer\InstalledVersions;
use Filament\Widgets\Widget;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Override;
use PDO;
use Throwable;

final class FillakitInfo extends Widget
{
    #[Override]
    protected int | string | array $columnSpan = 'full';

    #[Override]
    protected static ?int $sort = -2;

    #[Override]
    protected static bool $isLazy = false;

    /**
     * @var view-string
     */
    #[Override]
    protected string $view = 'filament.widgets.fillakit-info-widget';

    /**
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $databaseVersion = 'No database';

        try {
            if (DB::connection()) {
                $driver = ucfirst(DB::connection()->getDriverName());
                $pdo = DB::connection()->getPdo();
                if ($pdo instanceof PDO) {
                    $serverVersion = (string) $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
                    $databaseVersion = mb_trim($driver . ' ' . $serverVersion);
                } else {
                    $databaseVersion = $driver;
                }
            }
        } catch (Throwable) {
        }

        return [
            'app_name' => config('app.name', 'Fillakit'),
            'app_version' => config('fillakit.version', '3.0.0'),
            'filament_version' => InstalledVersions::getVersion('filament/filament'),
            'laravel_version' => Application::VERSION,
            'php_version' => PHP_VERSION,
            'db_version' => $databaseVersion,
            'github_url' => config('fillakit.github_url'),
            'docs_url' => config('fillakit.docs_url'),
        ];
    }
}
