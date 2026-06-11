<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\ContactResource\Pages;

use App\Filament\CRM\Resources\ContactResource;
use Filament\Resources\Pages\CreateRecord;

class CreateContact extends CreateRecord
{
    protected static string $resource = ContactResource::class;
}
