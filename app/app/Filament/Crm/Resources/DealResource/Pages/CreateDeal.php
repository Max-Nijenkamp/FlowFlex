<?php

declare(strict_types=1);

namespace App\Filament\Crm\Resources\DealResource\Pages;

use App\Filament\Crm\Resources\DealResource;
use App\Models\Crm\PipelineStage;
use App\Support\Services\CompanyContext;
use Filament\Resources\Pages\CreateRecord;

class CreateDeal extends CreateRecord
{
    protected static string $resource = DealResource::class;

    /** @param  array<string, mixed>  $data @return array<string, mixed> */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = app(CompanyContext::class)->currentId();
        $data['stage_entered_at'] = now();
        $data['probability'] = PipelineStage::query()->find($data['stage_id'])->probability_default ?? 0;

        return $data;
    }
}
