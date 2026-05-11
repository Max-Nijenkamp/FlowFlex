<?php

declare(strict_types=1);

namespace App\Filament\Hr\Resources\LeavePolicyResource\Pages;

use App\Filament\Hr\Resources\LeavePolicyResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditLeavePolicy extends EditRecord
{
    protected static string $resource = LeavePolicyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
