<?php

declare(strict_types=1);

namespace App\Filament\Crm\Resources\ActivityResource\Pages;

use App\Filament\Crm\Resources\ActivityResource;
use App\Support\Services\CompanyContext;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreateActivity extends CreateRecord
{
    protected static string $resource = ActivityResource::class;

    /** @param  array<string, mixed>  $data @return array<string, mixed> */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['company_id'] = app(CompanyContext::class)->currentId();
        $data['owner_id'] = Auth::id();
        $data['is_complete'] = $data['type'] !== 'task';

        return $data;
    }
}
