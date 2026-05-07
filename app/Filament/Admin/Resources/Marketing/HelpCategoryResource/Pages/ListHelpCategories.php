<?php

namespace App\Filament\Admin\Resources\Marketing\HelpCategoryResource\Pages;

use App\Filament\Admin\Resources\Marketing\HelpCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHelpCategories extends ListRecords
{
    protected static string $resource = HelpCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
