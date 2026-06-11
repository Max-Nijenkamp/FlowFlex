<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\QuoteResource\Pages;

use App\Contracts\CRM\QuoteServiceInterface;
use App\Data\CRM\CreateQuoteData;
use App\Filament\CRM\Resources\QuoteResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateQuote extends CreateRecord
{
    protected static string $resource = QuoteResource::class;

    /** Totals computed by QuoteService (brick/money). */
    protected function handleRecordCreation(array $data): Model
    {
        return app(QuoteServiceInterface::class)->create(CreateQuoteData::from($data));
    }
}
