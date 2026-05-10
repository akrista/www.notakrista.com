<?php

declare(strict_types=1);

namespace App\Settings;

use Spatie\LaravelSettings\Settings;

final class GeneralSettings extends Settings
{
    public string $brand_name;

    public ?string $brand_logo = null;

    public string $brand_logo_height;

    public string $brand_logo_height_unit;

    public ?string $site_favicon = null;

    public array $site_theme;

    public bool $search_engine_indexing;

    public string $pwa_theme_color = '#EB880C';

    public string $pwa_background_color = '#2B2822';

    public ?string $pwa_splash_background_color = null;

    public static function group(): string
    {
        return 'general';
    }
}
