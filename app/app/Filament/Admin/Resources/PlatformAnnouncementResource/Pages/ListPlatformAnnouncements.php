<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PlatformAnnouncementResource\Pages;

use App\Filament\Admin\Resources\PlatformAnnouncementResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListPlatformAnnouncements extends ListRecords
{
    protected static string $resource = PlatformAnnouncementResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
