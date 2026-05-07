<?php

namespace App\Filament\Admin\Resources\Marketing\BlogCategoryResource\Pages;

use App\Filament\Admin\Resources\Marketing\BlogCategoryResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListBlogCategories extends ListRecords
{
    protected static string $resource = BlogCategoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
