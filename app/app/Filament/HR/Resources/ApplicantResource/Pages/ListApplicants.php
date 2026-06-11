<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\ApplicantResource\Pages;

use App\Filament\HR\Resources\ApplicantResource;
use Filament\Resources\Pages\ListRecords;

class ListApplicants extends ListRecords
{
    protected static string $resource = ApplicantResource::class;
}
