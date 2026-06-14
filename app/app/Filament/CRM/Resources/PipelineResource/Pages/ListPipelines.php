<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\PipelineResource\Pages;

use App\Filament\CRM\Resources\PipelineResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPipelines extends ListRecords
{
    protected static string $resource = PipelineResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
