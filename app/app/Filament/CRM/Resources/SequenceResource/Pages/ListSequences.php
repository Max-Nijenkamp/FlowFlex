<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\SequenceResource\Pages;

use App\Filament\CRM\Resources\SequenceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListSequences extends ListRecords
{
    protected static string $resource = SequenceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
