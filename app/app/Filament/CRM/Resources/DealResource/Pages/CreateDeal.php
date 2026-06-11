<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\DealResource\Pages;

use App\Contracts\CRM\DealServiceInterface;
use App\Filament\CRM\Resources\DealResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateDeal extends CreateRecord
{
    protected static string $resource = DealResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        return app(DealServiceInterface::class)->create(
            name: $data['name'],
            stageId: $data['stage_id'],
            valueCents: (int) $data['value_cents'],
            contactId: $data['contact_id'] ?? null,
            expectedCloseDate: $data['expected_close_date'] ?? null,
        );
    }
}
