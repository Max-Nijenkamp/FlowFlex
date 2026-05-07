<?php

namespace App\Filament\Admin\Resources\Marketing\HelpArticleResource\Pages;

use App\Filament\Admin\Resources\Marketing\HelpArticleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListHelpArticles extends ListRecords
{
    protected static string $resource = HelpArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
