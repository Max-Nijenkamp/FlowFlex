<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\HeadcountPlanResource\Pages;

use App\Filament\HR\Resources\HeadcountPlanResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHeadcountPlans extends ListRecords
{
    protected static string $resource = HeadcountPlanResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
