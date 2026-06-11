<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\BookingResource\Pages;

use App\Filament\CRM\Resources\BookingResource;
use Filament\Resources\Pages\ListRecords;

class ListBookings extends ListRecords
{
    protected static string $resource = BookingResource::class;
}
