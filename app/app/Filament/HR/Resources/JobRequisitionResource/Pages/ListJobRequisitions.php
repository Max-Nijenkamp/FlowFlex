<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\JobRequisitionResource\Pages;

use App\Filament\HR\Resources\JobRequisitionResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListJobRequisitions extends ListRecords
{
    protected static string $resource = JobRequisitionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
