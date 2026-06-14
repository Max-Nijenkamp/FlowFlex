<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\AccountResource\Pages;

use App\Filament\CRM\Resources\AccountResource;
use Filament\Resources\Pages\CreateRecord;

class CreateAccount extends CreateRecord
{
    protected static string $resource = AccountResource::class;
}
