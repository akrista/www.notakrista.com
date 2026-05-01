<?php

declare(strict_types=1);

namespace App\Filament\Pages\Settings;

use App\Services\FileService;
use App\Services\PwaIconService;
use App\Settings\GeneralSettings;
use BackedEnum;
use Filament\Forms\Components\CodeEditor;
use Filament\Forms\Components\CodeEditor\Enums\Language;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Pages\SettingsPage;
use Filament\Schemas\Components\Grid;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Schema;
use Filament\Support\Facades\FilamentView;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Storage;
use Override;
use Throwable;

final class General extends SettingsPage
{
    /**
     * @var array<string, mixed> | null
     */
    #[Override]
    public ?array $data = [];

    public string $theme = '';

    #[Override]
    protected static string $settings = GeneralSettings::class;

    #[Override]
    protected static ?int $navigationSort = 1002;

    #[Override]
    protected static string | BackedEnum | null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    public static function canAccess(): bool
    {
        $user = request()->user();

        return $user?->can('settings.view') ?? false;
    }

    public static function getNavigationGroup(): ?string
    {
        return __('menu.nav_group.settings');
    }

    public static function getNavigationLabel(): string
    {
        return __('page.general_settings.navigationLabel');
    }

    public function canEdit(): bool
    {
        $user = request()->user();

        return $user?->can('settings.edit') ?? false;
    }

    public function mount(): void
    {
        $this->theme = resource_path('css/filament/admin/theme.css');

        $this->fillForm();
    }

    public function getTitle(): string
    {
        return __('page.general_settings.title');
    }

    public function getHeading(): string
    {
        return __('page.general_settings.heading');
    }

    public function getSubheading(): ?string
    {
        return __('page.general_settings.subheading');
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Tabs::make('Settings')
                    ->columnSpanFull()
                    ->tabs([
                        Tab::make('Site')
                            ->icon(Heroicon::OutlinedGlobeAlt)
                            ->schema([
                                Grid::make()->schema([
                                    TextInput::make('brand_name')
                                        ->label(fn(): string | array | null => __('page.general_settings.fields.brand_name'))
                                        ->required(),
                                    Toggle::make('search_engine_indexing')
                                        ->label('App Indexing')
                                        ->helperText('When disabled, search engines will be instructed not to index the app')
                                        ->default(true),
                                ]),
                            ]),

                        Tab::make('Branding')
                            ->icon(Heroicon::OutlinedPhoto)
                            ->schema([
                                Grid::make()->schema([
                                    TextInput::make('brand_logo_height')
                                        ->label(fn(): string | array | null => __('page.general_settings.fields.brand_logo_height'))
                                        ->numeric()
                                        ->suffix(fn(Get $get): mixed => $get('brand_logo_height_unit'))
                                        ->required(),
                                    Select::make('brand_logo_height_unit')
                                        ->label('CSS Unit')
                                        ->native(false)
                                        ->live()
                                        ->options([
                                            'cm' => 'Centimeters (cm)',
                                            'mm' => 'Millimeters (mm)',
                                            'in' => 'Inches (in)',
                                            'px' => 'Pixels (px)',
                                            'pt' => 'Points (pt)',
                                            'pc' => 'Picas (pc)',
                                            'em' => 'Relative to the font-size of the element (em)',
                                            'ex' => 'Relative to the x-height of the current font (ex)',
                                            'ch' => 'Relative to the width of the "0" (ch)',
                                            'rem' => 'Relative to font-size of the root element (rem)',
                                            'vw' => 'Relative to 1% of the width of the viewport (vw)',
                                            'vh' => 'Relative to 1% of the height of the viewport (vh)',
                                            'vmin' => "Relative to 1% of viewport's smaller dimension (vmin)",
                                            'vmax' => "Relative to 1% of viewport's larger dimension (vmax)",
                                            '%' => 'Relative to the parent element (%)',
                                        ])
                                        ->required(),
                                ])->columnSpan(3),

                                Grid::make()->schema([
                                    FileUpload::make('brand_logo')
                                        ->label(fn(): string | array | null => __('page.general_settings.fields.brand_logo'))
                                        ->helperText('Upload your site logo (optional)')
                                        ->image()
                                        ->disk('public')
                                        ->previewable(true)
                                        ->directory('sites')
                                        ->visibility('public')
                                        ->moveFiles()
                                        ->imageEditor()
                                        ->imageEditorAspectRatios([
                                            null,
                                            '16:9',
                                            '4:3',
                                            '1:1',
                                        ])
                                        ->imagePreviewHeight('250')
                                        ->maxSize(5120)
                                        ->acceptedFileTypes(['image/png', 'image/jpeg', 'image/svg+xml']),

                                    FileUpload::make('site_favicon')
                                        ->label(fn(): string | array | null => __('page.general_settings.fields.site_favicon'))
                                        ->helperText('Supports .ico, .png, .jpg, and .svg formats. PWA icons will be regenerated when you save.')
                                        ->image()
                                        ->disk('public')
                                        ->previewable(true)
                                        ->directory('sites')
                                        ->visibility('public')
                                        ->moveFiles()
                                        ->imageEditor()
                                        ->imageEditorAspectRatios([
                                            null,
                                            '16:9',
                                            '4:3',
                                            '1:1',
                                        ])
                                        ->imagePreviewHeight('250')
                                        ->maxSize(5120)
                                        ->acceptedFileTypes(['image/x-icon', 'image/vnd.microsoft.icon', 'image/png', 'image/jpeg', 'image/svg+xml']),
                                ])->columns(2)->columnSpan(3),
                            ])->columns(3),

                        Tab::make('Theme')
                            ->icon(Heroicon::OutlinedSwatch)
                            ->schema([
                                Section::make('Theme Colors')
                                    ->description('Customize your admin panel color scheme')
                                    ->icon(Heroicon::OutlinedPaintBrush)
                                    ->compact()
                                    ->collapsible()
                                    ->schema([
                                        ColorPicker::make('site_theme.primary')
                                            ->label(fn(): string | array | null => __('page.general_settings.fields.primary'))
                                            ->helperText('Used for primary buttons and links'),
                                        ColorPicker::make('site_theme.secondary')
                                            ->label(fn(): string | array | null => __('page.general_settings.fields.secondary'))
                                            ->helperText('Used for secondary elements'),
                                        ColorPicker::make('site_theme.gray')
                                            ->label(fn(): string | array | null => __('page.general_settings.fields.gray'))
                                            ->helperText('Used for neutral backgrounds and text'),
                                    ])->columns(3),
                                Section::make('Status Colors')
                                    ->description('Define colors for different states and notifications')
                                    ->icon(Heroicon::OutlinedPaintBrush)
                                    ->compact()
                                    ->collapsible()
                                    ->schema([
                                        ColorPicker::make('site_theme.success')
                                            ->label(fn(): string | array | null => __('page.general_settings.fields.success'))
                                            ->regex('/^#([A-F0-9]{6}|[A-F0-9]{3})\b$/')
                                            ->helperText('Used for success states and confirmations'),
                                        ColorPicker::make('site_theme.danger')
                                            ->label(fn(): string | array | null => __('page.general_settings.fields.danger'))
                                            ->regex('/^#([A-F0-9]{6}|[A-F0-9]{3})\b$/')
                                            ->helperText('Used for errors and dangerous actions'),
                                        ColorPicker::make('site_theme.info')
                                            ->label(fn(): string | array | null => __('page.general_settings.fields.info'))
                                            ->regex('/^#([A-F0-9]{6}|[A-F0-9]{3})\b$/')
                                            ->helperText('Used for informational notifications'),
                                        ColorPicker::make('site_theme.warning')
                                            ->label(fn(): string | array | null => __('page.general_settings.fields.warning'))
                                            ->regex('/^#([A-F0-9]{6}|[A-F0-9]{3})\b$/')
                                            ->helperText('Used for warnings and cautions'),
                                    ])->columns(2),
                                Section::make('Theme CSS')
                                    ->description('Edit the CSS theme directly (changes will be applied after saving)')
                                    ->icon(Heroicon::OutlinedCodeBracket)
                                    ->compact()
                                    ->collapsible()
                                    ->collapsed()
                                    ->schema([
                                        CodeEditor::make('theme-editor')
                                            ->language(Language::Css)
                                            ->columnSpanFull(),
                                    ])->columns(2),
                            ]),
                        Tab::make('PWA')
                            ->icon(Heroicon::OutlinedDevicePhoneMobile)
                            ->schema([
                                Section::make('PWA Colors')
                                    ->description('Customize PWA theme and background colors')
                                    ->icon(Heroicon::OutlinedPaintBrush)
                                    ->compact()
                                    ->collapsible()
                                    ->schema([
                                        ColorPicker::make('pwa_theme_color')
                                            ->label('Theme Color')
                                            ->helperText('Browser toolbar color on mobile devices'),
                                        ColorPicker::make('pwa_background_color')
                                            ->label('Background Color')
                                            ->helperText('App background color'),
                                        ColorPicker::make('pwa_splash_background_color')
                                            ->label('Splash Screen Background')
                                            ->helperText('Background color for iOS splash screens. Leave empty for transparent background.'),
                                    ])->columns(3),
                            ]),
                    ]),
            ])
            ->columns(3)
            ->statePath('data');
    }

    public function save(): void
    {
        try {
            $data = $this->mutateFormDataBeforeSave($this->form->getState());

            $settings = resolve(self::getSettings());

            $settings->fill($data);
            $settings->save();

            $fileService = new FileService;
            $fileService->writeFile($this->theme, $data['theme-editor']);

            if (isset($data['site_favicon']) && $data['site_favicon']) {
                $this->regeneratePwaIcons($data['site_favicon']);
            }

            Notification::make()
                ->title('Settings updated successfully!')
                ->body('Your changes have been saved.')
                ->success()
                ->send();

            $this->redirect(self::getUrl(), navigate: FilamentView::hasSpaMode());
        } catch (Throwable $throwable) {
            Notification::make()
                ->title('Error saving settings')
                ->body($throwable->getMessage())
                ->danger()
                ->send();

            throw $throwable;
        }
    }

    protected function fillForm(): void
    {
        $settings = resolve(self::getSettings());

        $data = $this->mutateFormDataBeforeFill($settings->toArray());

        $fileService = new FileService;

        $data['theme-editor'] = $fileService->readfile($this->theme);

        $this->form->fill($data);
    }

    protected function regeneratePwaIcons(?string $uploadedPath): void
    {
        if (!$uploadedPath) {
            return;
        }

        $pwaService = new PwaIconService;
        $backgroundColor = $this->data['pwa_splash_background_color'] ?? '#0000';

        try {
            $fullPath = Storage::disk('public')->path($uploadedPath);

            $pwaService->generateFromUpload($fullPath, $backgroundColor);

            Notification::make()
                ->title('PWA icons generated successfully')
                ->body('All icon sizes and splash screens have been created.')
                ->success()
                ->send();
        } catch (Throwable $throwable) {
            Notification::make()
                ->title('Error generating PWA icons')
                ->body($throwable->getMessage())
                ->danger()
                ->send();
        }
    }
}
