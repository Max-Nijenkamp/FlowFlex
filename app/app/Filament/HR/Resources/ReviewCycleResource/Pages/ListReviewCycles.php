<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\ReviewCycleResource\Pages;

use App\Filament\HR\Resources\ReviewCycleResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListReviewCycles extends ListRecords
{
    protected static string $resource = ReviewCycleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
