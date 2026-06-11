<?php

declare(strict_types=1);

namespace App\Filament\HR\Resources\FeedbackResource\Pages;

use App\Filament\HR\Resources\FeedbackResource;
use Filament\Resources\Pages\ListRecords;

class ListFeedback extends ListRecords
{
    protected static string $resource = FeedbackResource::class;
}
