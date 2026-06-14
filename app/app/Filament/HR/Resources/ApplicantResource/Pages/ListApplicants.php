<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\ApplicantResource\Pages;

use App\Filament\HR\Resources\ApplicantResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListApplicants extends ListRecords
{
    protected static string $resource = ApplicantResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
