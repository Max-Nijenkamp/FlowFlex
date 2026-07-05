<?php

declare(strict_types=1);

namespace App\Providers\Filament;

/** CRM & Sales panel — /crm, Rose (domain-panels.md). */
class CrmPanelProvider extends DomainPanelProvider
{
    protected string $panelId = 'crm';

    protected string $panelPath = 'crm';

    protected string $accentHex = '#E11D48';

    protected string $domainNamespace = 'Crm';
}
