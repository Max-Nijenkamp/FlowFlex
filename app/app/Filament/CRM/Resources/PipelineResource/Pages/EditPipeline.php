<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\PipelineResource\Pages;

use App\Filament\CRM\Resources\PipelineResource;
use Filament\Resources\Pages\EditRecord;

class EditPipeline extends EditRecord
{
    protected static string $resource = PipelineResource::class;
}
