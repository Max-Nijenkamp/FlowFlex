<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\AdminUserResource\Pages;

use App\Filament\Admin\Resources\AdminUserResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListAdminUsers extends ListRecords
{
    protected static string $resource = AdminUserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
