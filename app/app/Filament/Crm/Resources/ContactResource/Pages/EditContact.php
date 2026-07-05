<?php

declare(strict_types=1);

namespace App\Filament\Crm\Resources\ContactResource\Pages;

use App\Filament\Crm\Resources\ContactResource;
use Filament\Resources\Pages\EditRecord;

class EditContact extends EditRecord
{
    protected static string $resource = ContactResource::class;
}
