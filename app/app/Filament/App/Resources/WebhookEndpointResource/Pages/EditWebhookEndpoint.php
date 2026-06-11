<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\WebhookEndpointResource\Pages;

use App\Filament\App\Resources\WebhookEndpointResource;
use Filament\Resources\Pages\EditRecord;

class EditWebhookEndpoint extends EditRecord
{
    protected static string $resource = WebhookEndpointResource::class;
}
