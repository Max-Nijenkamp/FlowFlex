<?php

namespace App\Filament\Admin\Resources\Marketing\ContactSubmissionResource\Pages;

use App\Filament\Admin\Resources\Marketing\ContactSubmissionResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditContactSubmission extends EditRecord
{
    protected static string $resource = ContactSubmissionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
