<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\WebhookEndpointResource\Pages;

use App\Filament\App\Resources\WebhookEndpointResource;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Str;

class CreateWebhookEndpoint extends CreateRecord
{
    protected static string $resource = WebhookEndpointResource::class;

    private string $plainSecret = '';

    /**
     * @param  array<string, mixed>  $data
     * @return array<string, mixed>
     */
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $this->plainSecret = 'whsec_'.Str::random(40);
        $data['secret'] = $this->plainSecret;

        return $data;
    }

    protected function afterCreate(): void
    {
        Notification::make()
            ->success()
            ->title('Signing secret (copy now — shown once)')
            ->body($this->plainSecret)
            ->persistent()
            ->send();
    }
}
