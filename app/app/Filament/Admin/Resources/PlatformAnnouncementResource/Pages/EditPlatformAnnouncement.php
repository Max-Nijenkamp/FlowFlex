<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PlatformAnnouncementResource\Pages;

use App\Filament\Admin\Resources\PlatformAnnouncementResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditPlatformAnnouncement extends EditRecord
{
    protected static string $resource = PlatformAnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
