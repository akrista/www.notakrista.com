<?php

declare(strict_types=1);

namespace App\Filament\Resources\WarframeAccounts\Pages;

use App\Filament\Resources\WarframeAccounts\WarframeAccountResource;
use App\Services\AlecaFrameImportService;
use App\Services\WfcdCatalogSyncService;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Override;

final class ListWarframeAccounts extends ListRecords
{
    #[Override]
    protected static string $resource = WarframeAccountResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            Action::make('importAlecaFrame')
                ->label('Import AlecaFrame Data')
                ->icon(Heroicon::OutlinedArrowUpTray)
                ->color('primary')
                ->schema([
                    FileUpload::make('file')
                        ->label('AlecaFrame lastData.dat File')
                        ->required(),
                ])
                ->action(function (array $data, AlecaFrameImportService $importService): void {
                    $fileInput = $data['file'];
                    $content = null;
                    $tempPath = null;

                    if ($fileInput instanceof UploadedFile) {
                        $content = file_get_contents($fileInput->getRealPath());
                    } elseif (is_string($fileInput)) {
                        $possiblePaths = [
                            $fileInput,
                            storage_path('app/' . $fileInput),
                            storage_path('app/public/' . $fileInput),
                            storage_path('app/livewire-tmp/' . $fileInput),
                        ];

                        foreach ($possiblePaths as $path) {
                            if (file_exists($path)) {
                                $tempPath = $path;
                                $content = file_get_contents($path);

                                break;
                            }
                        }

                        if ($content === null && Storage::exists($fileInput)) {
                            $content = Storage::get($fileInput);
                            Storage::delete($fileInput);
                        }
                    }

                    if ($tempPath && file_exists($tempPath)) {
                        @unlink($tempPath);
                    }

                    if (empty($content)) {
                        Notification::make()
                            ->title('Failed to read uploaded file')
                            ->danger()
                            ->send();

                        return;
                    }

                    $user = auth()->user();

                    if ($user === null) {
                        return;
                    }

                    $account = $importService->import($content, $user);

                    Notification::make()
                        ->title('Warframe inventory updated')
                        ->body(sprintf('Account Mastery Rank %d with %d items imported.', $account->mastery_rank, $account->userItems()->count()))
                        ->success()
                        ->send();
                }),

            Action::make('syncWfcdCatalog')
                ->label('Sync WFCD Catalog')
                ->icon(Heroicon::OutlinedArrowPath)
                ->color('secondary')
                ->action(function (WfcdCatalogSyncService $syncService): void {
                    $syncedCount = $syncService->sync();

                    Notification::make()
                        ->title('WFCD Item Catalog Synced')
                        ->body(sprintf('%d game items synced to catalog.', $syncedCount))
                        ->success()
                        ->send();
                }),
        ];
    }
}
