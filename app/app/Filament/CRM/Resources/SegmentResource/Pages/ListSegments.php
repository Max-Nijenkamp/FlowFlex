<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\SegmentResource\Pages;

use App\Filament\CRM\Resources\SegmentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSegments extends ListRecords
{
    protected static string $resource = SegmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
