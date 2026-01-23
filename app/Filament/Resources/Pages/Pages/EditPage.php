<?php

namespace App\Filament\Resources\Pages\Pages;

use App\Filament\Resources\Pages\PageResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;
use Filament\Support\Icons\Heroicon;

class EditPage extends EditRecord
{
    protected static string $resource = PageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('preview')
                ->label('Preview')
                ->icon(Heroicon::OutlinedEye)
                ->color('gray')
                ->url(fn (): string => route('page.show', $this->record->slug))
                ->openUrlInNewTab(),
            DeleteAction::make(),
        ];
    }
}
