<?php

declare(strict_types=1);

namespace App\Providers\Converge;

use App\Settings\GeneralSettings;
use Converge\Enums\HighlighterName;
use Converge\Enums\Layout;
use Converge\Enums\Spotlight;
use Converge\MenuItems\MenuItems;
use Converge\Module;
use Converge\Providers\ModuleProvider;
use Converge\Support\Themes;
use Converge\Theme\Theme;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

final class DocsModuleProvider extends ModuleProvider
{
    public function module(Module $module): Module
    {
        return $module
            ->id('docs-akrista')
            ->default()
            ->latestVersionLabel('v' . config('fillakit.version', '3.0.0'))
            ->routePath('docs/akrista')
            ->in(base_path('docs'))
            ->brandLogo(function () {
                try {
                    $settings = $this->getSettings();
                    if (! $settings || ! $settings->brand_logo) {
                        return asset('images/logo.png');
                    }

                    return Storage::disk('public')->exists($settings->brand_logo) ? Storage::url($settings->brand_logo) : asset('images/logo.png');
                } catch (Exception) {
                    return asset('images/logo.png');
                }
            })
            ->brandLogoHeight(function (): string {
                try {
                    $settings = $this->getSettings();
                    if (! $settings || ! $settings->brand_logo_height || ! $settings->brand_logo_height_unit) {
                        return '2rem';
                    }

                    return $settings->brand_logo_height . $settings->brand_logo_height_unit;
                } catch (Exception) {
                    return '2rem';
                }
            })
            ->theme(fn(Theme $theme): Theme => $this->theme($theme))
            ->defineMenuItems(fn(MenuItems $menuItems) => $this->defineMenuItems());
    }

    private function hasSettingsTable(): bool
    {
        try {
            return DB::connection()->getSchemaBuilder()->hasTable('settings');
        } catch (Exception) {
            return false;
        }
    }

    private function getSettings(): ?GeneralSettings
    {
        try {
            if (! $this->hasSettingsTable()) {
                return null;
            }

            return resolve(GeneralSettings::class);
        } catch (Exception) {
            return null;
        }
    }

    private function defineMenuItems(): void
    {
        // $menuItems->add(
        //     fn (MenuItem $menuItem): MenuItem => $menuItem->url('https://github.com/akrista/fillakit')
        //         ->openUrlInNewTab()
        //         ->icon(fn(): HtmlString => new HtmlString('<svg class="w-5 h-5" viewBox="0 0 98 96" xmlns="http://www.w3.org/2000/svg"><path clip-rule="evenodd" fill="currentColor" fill-rule="evenodd" d="M48.854 0C21.839 0 0 22 0 49.217c0 21.756 13.993 40.172 33.405 46.69 2.427.49 3.316-1.059 3.316-2.362 0-1.141-.08-5.052-.08-9.127-13.59 2.934-16.42-5.867-16.42-5.867-2.184-5.704-5.42-7.17-5.42-7.17-4.448-3.015.324-3.015.324-3.015 4.934.326 7.523 5.052 7.523 5.052 4.367 7.496 11.404 5.378 14.235 4.074.404-3.178 1.699-5.378 3.074-6.6-10.839-1.141-22.243-5.378-22.243-24.283 0-5.378 1.94-9.778 5.014-13.2-.485-1.222-2.184-6.275.486-13.038 0 0 4.125-1.304 13.426 5.052a46.97 46.97 0 0 1 12.214-1.63c4.125 0 8.33.571 12.213 1.63 9.302-6.356 13.427-5.052 13.427-5.052 2.67 6.763.97 11.816.485 13.038 3.155 3.422 5.015 7.822 5.015 13.2 0 18.905-11.404 23.06-22.324 24.283 1.78 1.548 3.316 4.481 3.316 9.126 0 6.6-.08 11.897-.08 13.526 0 1.304.89 2.853 3.316 2.364 19.412-6.52 33.405-24.935 33.405-46.691C97.707 22 75.788 0 48.854 0z"/></svg>'))
        //         ->iconPosition(IconPosition::After)
        //         ->label('')
        // );
        // $menuItems->add(
        //     fn(MenuItem $menuItem) => $menuItem->url('https://github.com/akrista/fillakit?sponsor=1')
        //         ->openUrlInNewTab()
        //         ->classes('btn btn-sm btn-outline btn-primary')
        //         ->label('Sponsor')
        // );
    }

    private function theme(Theme $theme): Theme
    {
        return $theme
            ->layout(Layout::Spacious)
            ->spotlight(Spotlight::Hive)
            ->highlighterTheme(
                darkmodeHighlighter: HighlighterName::Monokai,
                lightmodeHighlighter: HighlighterName::Solarized_light
            )
            ->theme(
                Themes::overrideDark([
                    '--prose-bg' => 'black',
                    '--color-base-100' => 'oklch(20% 0 0)',
                    '--color-base-200' => 'oklch(14.1% 0.005 285.823)',
                    '--color-base-300' => '#3838382e',
                    '--color-base-content' => 'oklch(0.872 0.01 258.338)',
                    '--color-primary' => '#A6E22E',
                    '--color-primary-content' => 'white',
                    '--color-secondary' => '#66D9EF',
                    '--color-secondary-content' => 'white',
                    '--color-accent' => 'oklch(77% 0.152 181.912)',
                    '--color-accent-content' => 'oklch(38% 0.063 188.416)',
                    '--color-neutral' => 'oklch(14% 0.005 285.823)',
                    '--color-neutral-content' => 'oklch(92% 0.004 286.32)',
                    '--color-info' => '#66D9EF',
                    '--color-info-content' => 'white',
                    '--color-success' => '#A6E22E',
                    '--color-success-content' => 'white',
                    '--color-warning' => '#E6DB74',
                    '--color-warning-content' => 'white',
                    '--color-error' => '#F92672',
                    '--color-error-content' => 'white',
                    '--radius-selector' => '0.5rem',
                    '--radius-field' => '.75rem',
                    '--radius-box' => '1rem',
                    '--size-selector' => '0.25rem',
                    '--size-field' => '0.25rem',
                    '--border' => '.1px',
                    '--depth' => '0.25rem',
                    '--noise' => '0.5',
                    '--text-sm' => '0.88rem',
                    '--text-base' => '0.94rem',
                    '--font-weight' => '400',
                ]),
                Themes::overrideLight([
                    '--color-base-100' => 'oklch(97.788% 0.004 56.375)',
                    '--color-base-200' => 'oklch(0.985 0.001 106.423)',
                    '--color-base-300' => 'oklch(91.586% 0.006 53.44)',
                    '--color-base-content' => 'oklch(23.574% 0.066 313.189)',
                    '--color-primary' => '#A6E22E',
                    '--color-primary-content' => 'white',
                    '--color-secondary' => '#66D9EF',
                    '--color-secondary-content' => 'white',
                    '--color-accent' => '#2878ad',
                    '--color-accent-content' => 'oklch(47% 0.157 37.304)',
                    '--color-neutral' => 'oklch(27% 0.006 286.033)',
                    '--color-neutral-content' => 'oklch(92% 0.004 286.32)',
                    '--color-info' => '#66D9EF',
                    '--color-info-content' => 'white',
                    '--color-success' => '#A6E22E',
                    '--color-success-content' => 'white',
                    '--color-warning' => '#E6DB74',
                    '--color-warning-content' => 'white',
                    '--color-error' => '#F92672',
                    '--color-error-content' => 'white',
                    '--radius-selector' => '0.5rem',
                    '--radius-field' => '0.75rem',
                    '--radius-box' => '1rem',
                    '--size-selector' => '0.25rem',
                    '--size-field' => '0.25rem',
                    '--border' => '.1px',
                    '--depth' => '0.25rem',
                    '--noise' => '0.5',
                    '--text-sm' => '0.88rem',
                    '--text-base' => '0.94rem',
                    '--font-weight' => '400',
                ]),
            );
    }
}
