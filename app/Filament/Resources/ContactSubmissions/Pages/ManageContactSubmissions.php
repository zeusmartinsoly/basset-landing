<?php

namespace App\Filament\Resources\ContactSubmissions\Pages;

use App\Filament\Resources\ContactSubmissions\ContactSubmissionResource;
use Filament\Resources\Pages\ManageRecords;

class ManageContactSubmissions extends ManageRecords
{
    protected static string $resource = ContactSubmissionResource::class;

    /** @return array<object> */
    protected function getHeaderActions(): array
    {
        return [];
    }
}
