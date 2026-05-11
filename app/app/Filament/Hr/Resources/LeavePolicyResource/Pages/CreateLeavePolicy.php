<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\LeavePolicyResource\Pages;

use App\Filament\Hr\Resources\LeavePolicyResource;
use Filament\Resources\Pages\CreateRecord;

class CreateLeavePolicy extends CreateRecord
{
    protected static string $resource = LeavePolicyResource::class;
}
