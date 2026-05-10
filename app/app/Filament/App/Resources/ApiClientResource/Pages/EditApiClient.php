<?php

declare(strict_types=1);

namespace App\Filament\App\Resources\ApiClientResource\Pages;

use App\Filament\App\Resources\ApiClientResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditApiClient extends EditRecord
{
    protected static string $resource = ApiClientResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
