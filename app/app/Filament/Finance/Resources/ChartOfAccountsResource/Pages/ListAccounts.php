<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\ChartOfAccountsResource\Pages;

use App\Filament\Finance\Resources\ChartOfAccountsResource;
use App\Models\Finance\Account;
use App\Services\Finance\LedgerService;
use App\Support\Services\CompanyContext;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListAccounts extends ListRecords
{
    protected static string $resource = ChartOfAccountsResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
            Action::make('seedDefaults')
                ->label('Seed default chart')
                ->icon('heroicon-o-sparkles')
                ->color('gray')
                ->visible(fn (): bool => Account::query()->count() === 0)
                ->action(function (): void {
                    LedgerService::ensureDefaultChartOfAccounts(app(CompanyContext::class)->current()->id);
                    Notification::make()->success()->title('Default chart of accounts seeded')->send();
                }),
        ];
    }
}
