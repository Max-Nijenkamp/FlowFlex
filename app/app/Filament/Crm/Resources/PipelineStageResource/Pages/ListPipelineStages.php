<?php

declare(strict_types=1);

namespace App\Filament\Crm\Resources\PipelineStageResource\Pages;

use App\Filament\Crm\Resources\PipelineStageResource;
use App\Models\Crm\PipelineStage;
use App\Services\Crm\PipelineService;
use App\Support\Services\CompanyContext;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPipelineStages extends ListRecords
{
    protected static string $resource = PipelineStageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->mutateDataUsing(function (array $data): array {
                    $company = app(CompanyContext::class)->current();
                    $pipeline = PipelineService::ensureDefaultStages($company);

                    $data['company_id'] = $company->id;
                    $data['pipeline_id'] = $pipeline->id;
                    $data['order'] = (int) PipelineStage::query()->max('order') + 1;

                    return $data;
                }),
            Action::make('seedDefaults')
                ->label('Seed default stages')
                ->icon('heroicon-o-sparkles')
                ->color('gray')
                ->visible(fn (): bool => PipelineStage::query()->count() === 0)
                ->action(function (): void {
                    PipelineService::ensureDefaultStages(app(CompanyContext::class)->current());
                    Notification::make()->success()->title('Default pipeline seeded')->send();
                }),
        ];
    }
}
