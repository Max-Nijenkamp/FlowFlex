<?php

declare(strict_types=1);

namespace App\Filament\App\Pages;

use App\Models\CompanyModuleSubscription;
use App\Models\ModuleCatalog;
use App\Support\Services\CompanyContext;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class ModuleMarketplace extends Page
{
    public static function getNavigationIcon(): string
    {
        return 'heroicon-o-puzzle-piece';
    }

    public static function getNavigationLabel(): string
    {
        return 'Modules & Billing';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Settings';
    }

    public static function getNavigationSort(): ?int
    {
        return 20;
    }

    public function getTitle(): string
    {
        return 'Module Marketplace';
    }

    public function getView(): string
    {
        return 'filament.app.pages.module-marketplace';
    }

    public function getViewData(): array
    {
        $company = app(CompanyContext::class)->current();

        $activeKeys = collect($company->activeModuleKeys());

        $modules = ModuleCatalog::where('is_active', true)
            ->orderBy('domain')
            ->orderBy('name')
            ->get()
            ->groupBy('domain');

        return [
            'modules'    => $modules,
            'activeKeys' => $activeKeys,
        ];
    }

    public function enableModule(string $moduleKey): void
    {
        $company = app(CompanyContext::class)->current();

        $catalog = ModuleCatalog::where('module_key', $moduleKey)->where('is_active', true)->first();

        if (! $catalog) {
            Notification::make()->title('Module not found')->danger()->send();

            return;
        }

        CompanyModuleSubscription::withoutGlobalScopes()->updateOrCreate(
            ['company_id' => $company->id, 'module_key' => $moduleKey],
            ['status' => 'active', 'activated_at' => now()],
        );

        Notification::make()
            ->title("Module '{$catalog->name}' enabled")
            ->success()
            ->send();
    }

    public function disableModule(string $moduleKey): void
    {
        $company = app(CompanyContext::class)->current();

        CompanyModuleSubscription::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->where('module_key', $moduleKey)
            ->update(['status' => 'inactive', 'deactivated_at' => now()]);

        Notification::make()
            ->title('Module disabled — your data has been preserved')
            ->warning()
            ->send();
    }
}
