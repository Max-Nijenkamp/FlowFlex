<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CompanyResource\RelationManagers;

use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\ModuleCatalogEntry;
use App\Models\User;
use App\Services\BillingService;
use App\Support\Services\CompanyContext;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Throwable;

/**
 * Per-company module management for staff (core.staff-console). All writes
 * go through BillingService under the TARGET company's context, set and
 * forgotten per call.
 */
class ModulesRelationManager extends RelationManager
{
    protected static string $relationship = 'moduleSubscriptions';

    protected static ?string $title = 'Modules';

    /** Runs $fn with the owner company's context, always restoring after. */
    protected function withCompanyContext(callable $fn): void
    {
        /** @var Company $company */
        $company = $this->getOwnerRecord();

        try {
            app(CompanyContext::class)->set($company);
            $fn();
        } catch (Throwable $e) {
            Notification::make()->danger()->title($e->getMessage())->send();
        } finally {
            app(CompanyContext::class)->forget();
        }
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('module_key')->label('Module'),
                TextColumn::make('activated_at')->dateTime('d M Y')->sortable(),
                TextColumn::make('status')
                    ->badge()
                    ->state(fn (CompanyModuleSubscription $record): string => $record->deactivated_at === null ? 'Active' : 'Deactivated')
                    ->color(fn (string $state): string => $state === 'Active' ? 'success' : 'gray'),
            ])
            ->defaultSort('activated_at', 'desc')
            ->headerActions([
                Action::make('activate')
                    ->label('Activate module')
                    ->schema([
                        Select::make('module_key')
                            ->options(fn (): array => ModuleCatalogEntry::query()
                                ->where('is_active', true)
                                ->orderBy('module_key')
                                ->pluck('module_key', 'module_key')
                                ->all())
                            ->required()
                            ->searchable(),
                    ])
                    ->action(function (array $data): void {
                        $this->withCompanyContext(function () use ($data): void {
                            /** @var Company $company */
                            $company = $this->getOwnerRecord();

                            /** @var User $actor */
                            $actor = $company->users()->firstOrFail();
                            app(BillingService::class)->activateModule((string) $data['module_key'], $actor);
                            Notification::make()->success()->title('Module activated')->send();
                        });
                    }),
            ])
            ->recordActions([
                Action::make('deactivate')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (CompanyModuleSubscription $record): bool => $record->deactivated_at === null)
                    ->action(function (CompanyModuleSubscription $record): void {
                        $this->withCompanyContext(function () use ($record): void {
                            app(BillingService::class)->deactivateModule($record->module_key);
                            Notification::make()->success()->title('Module deactivated')->send();
                        });
                    }),
            ]);
    }
}
