<?php

declare(strict_types=1);

namespace App\Providers\Filament;

/** Finance & Accounting panel — /finance, Emerald (domain-panels.md). */
class FinancePanelProvider extends DomainPanelProvider
{
    protected string $panelId = 'finance';

    protected string $panelPath = 'finance';

    protected string $accentHex = '#059669';

    protected string $domainNamespace = 'Finance';
}
