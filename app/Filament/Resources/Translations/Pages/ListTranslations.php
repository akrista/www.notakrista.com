<?php

declare(strict_types=1);

namespace App\Filament\Resources\Translations\Pages;

use App\Actions\Translations\SyncTranslationsFromLangFilesAction;
use App\Data\SyncTranslationsResult;
use App\Filament\Resources\Translations\TranslationResource;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Filament\Support\Icons\Heroicon;
use Override;

final class ListTranslations extends ListRecords
{
    #[Override]
    protected static string $resource = TranslationResource::class;

    #[Override]
    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            $this->syncFromLangFilesAction(),
        ];
    }

    private function syncFromLangFilesAction(): Action
    {
        return Action::make('syncFromLangFiles')
            ->label(__('app.sync_translations_from_files'))
            ->icon(Heroicon::OutlinedArrowPath)
            ->color('primary')
            ->modalHeading(__('sections.sync_translations_from_files'))
            ->modalDescription(__('sections.sync_translations_from_files_desc'))
            ->modalSubmitActionLabel(__('app.sync_translations_from_files'))
            ->requiresConfirmation()
            ->action(function (SyncTranslationsFromLangFilesAction $syncAction): void {
                $result = $syncAction->handle(lang_path());

                $this->notifyResult($result);

                if (! $result->isEmpty()) {
                    $this->redirect(self::getResource()::getUrl('index'), navigate: true);
                }
            });
    }

    private function notifyResult(SyncTranslationsResult $result): void
    {
        if ($result->isEmpty()) {
            Notification::make()
                ->title(__('app.sync_translations_no_results', ['locale' => 'lang/']))
                ->warning()
                ->send();

            return;
        }

        Notification::make()
            ->title(__('app.sync_translations_completed'))
            ->body(__('app.sync_translations_completed_body', [
                'files' => $result->filesProcessed,
                'created' => $result->created,
                'updated' => $result->updated,
                'skipped' => $result->skipped,
            ]))
            ->success()
            ->send();
    }
}
