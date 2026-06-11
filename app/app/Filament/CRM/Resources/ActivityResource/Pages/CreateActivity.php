<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\ActivityResource\Pages;

use App\Filament\CRM\Resources\ActivityResource;
use Filament\Resources\Pages\CreateRecord;

class CreateActivity extends CreateRecord
{
    protected static string $resource = ActivityResource::class;
}
