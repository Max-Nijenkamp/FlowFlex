<?php

declare(strict_types=1);

namespace App\Support\Filament;

use Filament\Facades\Filament;

/**
 * Topbar breadcrumb per the Switchboard+ panel design (§12): panel display
 * name + current section, left-aligned next to the sidebar toggle. The
 * page-level Filament breadcrumbs are hidden by the skin — this is the one
 * crumb line.
 */
final class TopbarCrumb
{
    private const array PANELS = [
        'app' => 'Workspace',
        'hr' => 'HR & people',
        'finance' => 'Finance',
        'crm' => 'CRM & sales',
        'admin' => 'Staff console',
    ];

    public static function render(): string
    {
        $panel = Filament::getCurrentPanel();

        if ($panel === null || ! Filament::auth()->check()) {
            return '';
        }

        $panelLabel = self::PANELS[$panel->getId()] ?? str(str_replace('-', ' ', $panel->getId()))->title()->toString();

        $segments = collect(request()->segments())
            ->skip(1) // panel path prefix
            ->reject(fn (string $s): bool => is_numeric($s) || mb_strlen($s) > 20 && ! str_contains($s, '-'))
            ->map(fn (string $s): string => str(str_replace('-', ' ', $s))->ucfirst()->toString())
            ->take(2);

        $here = $segments->isEmpty() ? 'Dashboard' : $segments->implode(' / ');

        return '<div class="ff-topbar-crumb"><span>'.e($panelLabel).'</span><span class="sep">/</span><span class="here">'.e($here).'</span></div>';
    }
}
