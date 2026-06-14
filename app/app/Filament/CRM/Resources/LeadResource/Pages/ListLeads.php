<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\LeadResource\Pages;

use App\Filament\CRM\Resources\LeadResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListLeads extends ListRecords
{
    protected static string $resource = LeadResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()->label('New lead'),
        ];
    }
}
