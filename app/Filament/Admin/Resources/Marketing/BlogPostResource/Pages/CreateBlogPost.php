<?php

namespace App\Filament\Admin\Resources\Marketing\BlogPostResource\Pages;

use App\Filament\Admin\Resources\Marketing\BlogPostResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBlogPost extends CreateRecord
{
    protected static string $resource = BlogPostResource::class;
}
