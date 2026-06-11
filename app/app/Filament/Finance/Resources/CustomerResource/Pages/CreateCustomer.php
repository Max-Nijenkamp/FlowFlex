<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\CustomerResource\Pages;

use App\Filament\Finance\Resources\CustomerResource;
use Filament\Resources\Pages\CreateRecord;

class CreateCustomer extends CreateRecord
{
    protected static string $resource = CustomerResource::class;
}
