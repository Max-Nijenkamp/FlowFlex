<?php

namespace App\Filament\Workspace\Widgets;

use Filament\Widgets\Widget;

class ActiveModulesWidget extends Widget
{
    protected string $view = 'filament.workspace.widgets.active-modules';

    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected function getViewData(): array
    {
        $company = auth('tenant')->user()->company;

        $modules = $company->modules()
            ->wherePivot('is_enabled', true)
            ->orderBy('domain')
            ->orderBy('sort_order')
            ->get();

        $domainLabels = [
            'hr'             => 'HR & People',
            'projects'       => 'Projects & Work',
            'finance'        => 'Finance & Accounting',
            'crm'            => 'CRM & Sales',
            'marketing'      => 'Marketing & Content',
            'operations'     => 'Operations',
            'analytics'      => 'Analytics & BI',
            'it'             => 'IT & Security',
            'legal'          => 'Legal & Compliance',
            'ecommerce'      => 'E-commerce',
            'communications' => 'Communications',
            'learning'       => 'Learning & Dev',
            'core'           => 'Core Platform',
        ];

        return [
            'modulesByDomain' => $modules->groupBy('domain'),
            'domainLabels'    => $domainLabels,
            'totalActive'     => $modules->count(),
        ];
    }
}
