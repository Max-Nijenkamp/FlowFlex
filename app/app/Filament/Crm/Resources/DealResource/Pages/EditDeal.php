<?php

declare(strict_types=1);

namespace App\Filament\Crm\Resources\DealResource\Pages;

use App\Filament\Crm\Resources\DealResource;
use App\Models\Crm\Deal;
use App\Models\Crm\PipelineStage;
use Filament\Resources\Pages\EditRecord;

class EditDeal extends EditRecord
{
    protected static string $resource = DealResource::class;

    /** @param  array<string, mixed>  $data @return array<string, mixed> */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        /** @var Deal $record */
        $record = $this->record;

        // Stage changed through the form: same semantics as a board move.
        if (($data['stage_id'] ?? null) !== null && $data['stage_id'] !== $record->stage_id) {
            $data['stage_entered_at'] = now();
            $data['probability'] = PipelineStage::query()->find($data['stage_id'])->probability_default ?? $record->probability;
        }

        return $data;
    }
}
