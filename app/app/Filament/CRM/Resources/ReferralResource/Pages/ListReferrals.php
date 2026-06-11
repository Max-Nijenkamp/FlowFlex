<?php

declare(strict_types=1);

namespace App\Filament\CRM\Resources\ReferralResource\Pages;

use App\Filament\CRM\Resources\ReferralResource;
use Filament\Resources\Pages\ListRecords;

class ListReferrals extends ListRecords
{
    protected static string $resource = ReferralResource::class;
}
