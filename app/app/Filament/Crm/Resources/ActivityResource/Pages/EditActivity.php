<?php

declare(strict_types=1);

namespace App\Filament\Crm\Resources\ActivityResource\Pages;

use App\Filament\Crm\Resources\ActivityResource;
use Filament\Resources\Pages\EditRecord;

class EditActivity extends EditRecord
{
    protected static string $resource = ActivityResource::class;
}
