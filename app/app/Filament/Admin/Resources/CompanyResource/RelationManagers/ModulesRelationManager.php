<?php

declare(strict_types=1);

namespace App\Filament\Admin\Resources\CompanyResource\RelationManagers;

use App\Contracts\BillingServiceInterface;
use App\Data\ActivateModuleData;
use App\Filament\Admin\Concerns\RunsInCompanyContext;
use App\Models\Company;
use App\Models\CompanyModuleSubscription;
use App\Models\ModuleCatalog;
use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class ModulesRelationManager extends RelationManager
{
    use RunsInCompanyContext;

    protected static string $relationship = 'subscriptions';

    protected static ?string $title = 'Modules';

    public function table(Table $table): Table
    {
        return $table
            ->modifyQueryUsing(fn ($query) => $query->whereNull('deactivated_at')->latest('activated_at'))
            ->columns([
                TextColumn::make('module_key')->label('Module')
                    ->description(fn (CompanyModuleSubscription $r): string => (string) (ModuleCatalog::entry($r->module_key)['name'] ?? '')),
                TextColumn::make('price')->label('Per user / month')
                    ->state(fn (CompanyModuleSubscription $r): int => ModuleCatalog::priceCents($r->module_key))
                    ->money('EUR', divideBy: 100),
                TextColumn::make('activated_at')->dateTime()->sortable(),
            ])
            ->headerActions([
                Action::make('activate')
                    ->label('Activate module')
                    ->icon(Heroicon::OutlinedPlus)
                    ->schema([
                        Select::make('module_key')
                            ->label('Module')
                            ->options(function (): array {
                                /** @var Company $company */
                                $company = $this->getOwnerRecord();

                                $active = $company->subscriptions()
                                    ->whereNull('deactivated_at')
                                    ->pluck('module_key')
                                    ->all();

                                return ModuleCatalog::query()
                                    ->where('is_active', true)
                                    ->whereNotIn('module_key', $active)
                                    ->orderBy('module_key')
                                    ->pluck('name', 'module_key')
                                    ->all();
                            })
                            ->searchable()
                            ->required(),
                    ])
                    ->action(function (array $data): void {
                        /** @var Company $company */
                        $company = $this->getOwnerRecord();

                        $this->withCompanyContext($company, function () use ($data): void {
                            app(BillingServiceInterface::class)
                                ->activateModule(new ActivateModuleData(module_key: (string) $data['module_key']));
                        });

                        Notification::make()->success()->title('Module activated')->send();
                    }),
            ])
            ->recordActions([
                Action::make('deactivate')
                    ->icon(Heroicon::OutlinedXMark)
                    ->color('danger')
                    ->requiresConfirmation()
                    ->visible(fn (CompanyModuleSubscription $r): bool => ! ModuleCatalog::isFreeCore($r->module_key))
                    ->action(function (CompanyModuleSubscription $record): void {
                        /** @var Company $company */
                        $company = $this->getOwnerRecord();

                        $this->withCompanyContext($company, function () use ($record): void {
                            app(BillingServiceInterface::class)->deactivateModule($record->module_key);
                        });

                        Notification::make()->success()->title('Module deactivated')->send();
                    }),
            ]);
    }
}
