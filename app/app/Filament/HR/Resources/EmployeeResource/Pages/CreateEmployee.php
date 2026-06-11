<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\EmployeeResource\Pages;

use App\Contracts\HR\EmployeeServiceInterface;
use App\Data\HR\CreateEmployeeData;
use App\Filament\HR\Resources\EmployeeResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateEmployee extends CreateRecord
{
    protected static string $resource = EmployeeResource::class;

    /** Hire goes through EmployeeService (number sequence + EmployeeHired event). */
    protected function handleRecordCreation(array $data): Model
    {
        return app(EmployeeServiceInterface::class)->hire(CreateEmployeeData::from($data));
    }
}
