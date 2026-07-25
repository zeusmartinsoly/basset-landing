<?php

namespace App\Filament\Resources\ContactSubmissionTabs\Pages;

use App\Filament\Resources\ContactSubmissionTabs\ContactSubmissionTabResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageContactSubmissionTabs extends ManageRecords
{
    protected static string $resource = ContactSubmissionTabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
