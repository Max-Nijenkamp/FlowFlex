<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\LeaveRequestResource\Pages;

use App\Contracts\HR\LeaveServiceInterface;
use App\Data\HR\SubmitLeaveRequestData;
use App\Filament\HR\Resources\LeaveRequestResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Database\Eloquent\Model;

class CreateLeaveRequest extends CreateRecord
{
    protected static string $resource = LeaveRequestResource::class;

    /** Submission goes through LeaveService (balance + state machine + event). */
    protected function handleRecordCreation(array $data): Model
    {
        return app(LeaveServiceInterface::class)->submit(SubmitLeaveRequestData::from($data));
    }
}
