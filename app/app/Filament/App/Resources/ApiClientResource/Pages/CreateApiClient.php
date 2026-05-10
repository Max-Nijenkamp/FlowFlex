<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ApiClientResource\Pages;

use App\Filament\App\Resources\ApiClientResource;
use App\Support\Services\CompanyContext;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateApiClient extends CreateRecord
{
    protected static string $resource = ApiClientResource::class;

    private string $plaintextSecret = '';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $company = app(CompanyContext::class)->current();

        $this->plaintextSecret = Str::random(64);

        $data['company_id']    = $company->id;
        $data['created_by']    = auth()->id();
        $data['client_id']     = Str::random(32);
        $data['client_secret'] = hash('sha256', $this->plaintextSecret);

        return $data;
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->title('API client created')
            ->body("Save this secret now — it won't be shown again: {$this->plaintextSecret}")
            ->warning()
            ->persistent()
            ->send();
    }
}
