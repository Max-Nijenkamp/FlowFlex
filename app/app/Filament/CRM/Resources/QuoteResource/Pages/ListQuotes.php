<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\QuoteResource\Pages;

use App\Filament\CRM\Resources\QuoteResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListQuotes extends ListRecords
{
    protected static string $resource = QuoteResource::class;

    protected function getHeaderActions(): array
    {
        return [CreateAction::make()];
    }
}
