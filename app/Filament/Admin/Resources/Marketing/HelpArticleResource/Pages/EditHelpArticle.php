<?php

namespace App\Filament\Admin\Resources\Marketing\HelpArticleResource\Pages;

use App\Filament\Admin\Resources\Marketing\HelpArticleResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditHelpArticle extends EditRecord
{
    protected static string $resource = HelpArticleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
