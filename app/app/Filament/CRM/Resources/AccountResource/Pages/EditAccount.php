<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\AccountResource\Pages;

use App\Filament\CRM\Resources\AccountResource;
use Filament\Resources\Pages\EditRecord;

class EditAccount extends EditRecord
{
    protected static string $resource = AccountResource::class;
}
