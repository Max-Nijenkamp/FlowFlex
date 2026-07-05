<?php

declare(strict_types=1);

namespace App\Filament\Crm\Resources\PipelineResource\Pages;

use App\Filament\Crm\Resources\PipelineResource;
use Filament\Resources\Pages\ListRecords;

class ListPipelines extends ListRecords
{
    protected static string $resource = PipelineResource::class;
}
