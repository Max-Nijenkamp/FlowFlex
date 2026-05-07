<?php

namespace App\Filament\Admin\Resources\Marketing\BlogPostResource\Pages;

use App\Filament\Admin\Resources\Marketing\BlogPostResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditBlogPost extends EditRecord
{
    protected static string $resource = BlogPostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
