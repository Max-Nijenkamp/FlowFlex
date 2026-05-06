<?php

namespace App\Filament\Workspace\Pages;

use App\Models\Module;
use App\Notifications\ModuleToggledNotification;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class ManageModules extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-puzzle-piece';

    protected static ?string $navigationLabel = 'Modules';

    protected static ?string $title = 'Modules';

    protected static ?int $navigationSort = 10;

    protected string $view = 'filament.workspace.pages.manage-modules';

    public array $enabledModuleIds = [];

    public function mount(): void
    {
        $this->enabledModuleIds = $this->company()
            ->modules()
            ->wherePivot('is_enabled', true)
            ->pluck('modules.id')
            ->toArray();
    }

    public function toggleModule(string $moduleId): void
    {
        $module = Module::find($moduleId);

        if (! $module || $module->is_core || ! $module->is_available) {
            return;
        }

        $company   = $this->company();
        $isEnabled = in_array($moduleId, $this->enabledModuleIds);

        $company->modules()->syncWithoutDetaching([
            $moduleId => [
                'is_enabled'  => ! $isEnabled,
                'enabled_at'  => ! $isEnabled ? now() : null,
                'disabled_at' => ! $isEnabled ? null : now(),
            ],
        ]);

        if ($module->panel_id) {
            Cache::forget("company:{$company->id}:panel:{$module->panel_id}:access");
        }

        if ($isEnabled) {
            $this->enabledModuleIds = array_values(
                array_filter($this->enabledModuleIds, fn ($id) => $id !== $moduleId)
            );
        } else {
            $this->enabledModuleIds[] = $moduleId;
        }

        Notification::make()
            ->success()
            ->title($isEnabled ? "{$module->name} disabled" : "{$module->name} enabled")
            ->send();

        auth('tenant')->user()->notify(
            new ModuleToggledNotification($module->name, ! $isEnabled)
        );
    }

    public function getViewData(): array
    {
        $modulesByDomain = Module::where('is_available', true)
            ->where('is_core', false)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('domain');

        return [
            'modulesByDomain' => $modulesByDomain,
            'domainLabels'    => $this->domainLabels(),
        ];
    }

    private function company()
    {
        return auth('tenant')->user()->company;
    }

    private function domainLabels(): array
    {
        return [
            'hr'             => 'HR & People',
            'projects'       => 'Projects & Work',
            'finance'        => 'Finance & Accounting',
            'crm'            => 'CRM & Sales',
            'marketing'      => 'Marketing & Content',
            'operations'     => 'Operations & Field Service',
            'analytics'      => 'Analytics & BI',
            'it'             => 'IT & Security',
            'legal'          => 'Legal & Compliance',
            'ecommerce'      => 'E-commerce',
            'communications' => 'Communications',
            'learning'       => 'Learning & Development',
        ];
    }
}
