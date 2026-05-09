<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\PlatformAnnouncementResource\Pages;

use App\Filament\Admin\Resources\PlatformAnnouncementResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

class CreatePlatformAnnouncement extends CreateRecord
{
    protected static string $resource = PlatformAnnouncementResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        $data['created_by'] = Auth::guard('admin')->id();

        return $data;
    }
}
