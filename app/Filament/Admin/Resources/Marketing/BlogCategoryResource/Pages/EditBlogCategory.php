<?php

namespace App\Filament\Admin\Resources\Marketing\BlogCategoryResource\Pages;

use App\Filament\Admin\Resources\Marketing\BlogCategoryResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBlogCategory extends EditRecord
{
    protected static string $resource = BlogCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
