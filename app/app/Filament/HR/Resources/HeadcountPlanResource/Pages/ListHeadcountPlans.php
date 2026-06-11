<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\HeadcountPlanResource\Pages;

use App\Filament\HR\Resources\HeadcountPlanResource;
use Filament\Resources\Pages\ListRecords;

class ListHeadcountPlans extends ListRecords
{
    protected static string $resource = HeadcountPlanResource::class;
}
