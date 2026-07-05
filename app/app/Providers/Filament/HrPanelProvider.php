<?php

declare(strict_types=1);

namespace App\Providers\Filament;

/** HR & People panel — /hr, Violet (domain-panels.md). */
class HrPanelProvider extends DomainPanelProvider
{
    protected string $panelId = 'hr';

    protected string $panelPath = 'hr';

    protected string $accentHex = '#7C3AED';

    protected string $domainNamespace = 'Hr';
}
