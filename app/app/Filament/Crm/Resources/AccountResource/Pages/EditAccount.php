<?php

declare(strict_types=1);

namespace App\Filament\Crm\Resources\AccountResource\Pages;

use App\Filament\Crm\Resources\AccountResource;
use Filament\Resources\Pages\EditRecord;

class EditAccount extends EditRecord
{
    protected static string $resource = AccountResource::class;
}
