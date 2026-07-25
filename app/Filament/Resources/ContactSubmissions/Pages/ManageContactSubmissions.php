<?php

namespace App\Filament\Resources\ContactSubmissions\Pages;

use App\Filament\Resources\ContactSubmissions\ContactSubmissionResource;
use App\Models\ContactSubmission;
use App\Models\ContactSubmissionTab;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ManageContactSubmissions extends ManageRecords
{
    protected static string $resource = ContactSubmissionResource::class;

    /** @return array<object> */
    protected function getHeaderActions(): array
    {
        return [];
    }

    /**
     * "All" and "Unassigned", then one tab per row in `contact_submission_tabs`. Add or rename a
     * row under "Contact tabs" and the tab bar follows — no code change needed.
     *
     * @return array<string, Tab>
     */
    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('All')
                ->badge(fn (): int => ContactSubmission::query()->count()),
            'unassigned' => Tab::make('Unassigned')
                ->badge(fn (): int => ContactSubmission::query()->whereNull('contact_submission_tab_id')->count())
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->whereNull('contact_submission_tab_id')),
        ];

        foreach (ContactSubmissionTab::query()->withCount('submissions')->orderBy('name')->get() as $tab) {
            $tabs['tab-'.$tab->getKey()] = Tab::make($tab->name)
                ->badge($tab->submissions_count)
                ->modifyQueryUsing(fn (Builder $query): Builder => $query->where('contact_submission_tab_id', $tab->getKey()));
        }

        return $tabs;
    }
}
