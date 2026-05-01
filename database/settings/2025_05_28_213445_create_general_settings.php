<?php

declare(strict_types=1);

use Spatie\LaravelSettings\Migrations\SettingsMigration;

return new class extends SettingsMigration
{
    public function up(): void
    {
        $this->migrator->add('general.brand_name', config('app.name'));
        $this->migrator->add('general.brand_logo', 'sites/logo.png');
        $this->migrator->add('general.brand_logo_height', '3');
        $this->migrator->add('general.brand_logo_height_unit', 'rem');
        $this->migrator->add('general.site_favicon', 'sites/logo.ico');
        $this->migrator->add('general.search_engine_indexing', false);
        $this->migrator->add('general.site_theme', [
            'primary' => '#A6E22E',
            'secondary' => '#66D9EF',
            'gray' => null,
            'success' => '#A6E22E',
            'danger' => '#F92672',
            'info' => '#66D9EF',
            'warning' => '#E6DB74',
        ]);
        $this->migrator->add('general.pwa_theme_color', '#A6E22E');
        $this->migrator->add('general.pwa_background_color', '#272822');
        $this->migrator->add('general.pwa_splash_background_color', '#272822');
    }
};
