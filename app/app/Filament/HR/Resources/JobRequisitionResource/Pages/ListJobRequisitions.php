<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\JobRequisitionResource\Pages;

use App\Filament\HR\Resources\JobRequisitionResource;
use Filament\Resources\Pages\ListRecords;

class ListJobRequisitions extends ListRecords
{
    protected static string $resource = JobRequisitionResource::class;
}
