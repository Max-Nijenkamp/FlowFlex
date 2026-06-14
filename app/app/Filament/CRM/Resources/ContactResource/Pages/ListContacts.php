<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\ContactResource\Pages;

use App\Filament\CRM\Resources\ContactResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Illuminate\Database\Eloquent\Builder;

class ListContacts extends ListRecords
{
    protected static string $resource = ContactResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }

    /** Lead pipeline at a glance — lifecycle tabs (founder request 2026-06-12). */
    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'leads' => Tab::make('Leads')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('lifecycle_stage', ['lead', 'mql', 'sql'])),
            'opportunities' => Tab::make('Opportunities')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('lifecycle_stage', 'opportunity')),
            'customers' => Tab::make('Customers')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereIn('lifecycle_stage', ['customer', 'evangelist'])),
        ];
    }
}
