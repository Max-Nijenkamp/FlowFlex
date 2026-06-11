<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\QuoteResource\Pages;

use App\Filament\CRM\Resources\QuoteResource;
use Filament\Resources\Pages\ListRecords;

class ListQuotes extends ListRecords
{
    protected static string $resource = QuoteResource::class;
}
