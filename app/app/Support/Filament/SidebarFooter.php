<?php

declare(strict_types=1);

namespace App\Support\Filament;

use App\Models\User;
use Filament\Facades\Filament;

/**
 * Sidebar footer per the Switchboard+ panel design: "Your panels" switcher
 * chips (26px mono squares, active outlined in the panel color) + user card.
 * Rendered via the SIDEBAR_FOOTER hook; plain CSS classes only (rule:
 * provider HTML is not scanned by panel themes).
 */
final class SidebarFooter
{
    private const array CHIPS = [
        'app' => 'APP',
        'hr' => 'HR',
        'finance' => 'FIN',
        'crm' => 'CRM',
    ];

    public static function render(): string
    {
        $panel = Filament::getCurrentPanel();
        $user = Filament::auth()->user();

        if ($panel === null || $user === null) {
            return '';
        }

        $isStaff = $panel->getAuthGuard() === 'admin';
        $chips = $isStaff ? '' : self::chips($panel->getId(), $user);

        $name = e($user->full_name ?? $user->name ?? $user->email);
        $context = $isStaff
            ? 'FlowFlex staff'
            : e($user->company?->name ?? 'Workspace');
        $initial = e(mb_strtoupper(mb_substr((string) ($user->full_name ?? $user->name ?? $user->email), 0, 1)));

        $chipsBlock = $chips === '' ? '' :
            '<div class="ff-panels-label">Your panels</div><div class="ff-panels">'.$chips.'</div>';

        return '<div class="ff-side-foot-wrp">'
            .$chipsBlock
            .'<div class="ff-side-foot">'
            .'<span class="ff-side-ava">'.$initial.'</span>'
            .'<span class="ff-side-id"><span class="nm">'.$name.'</span><span class="co">'.$context.'</span></span>'
            .'</div></div>';
    }

    private static function chips(string $currentPanelId, mixed $user): string
    {
        $html = '';

        foreach (self::CHIPS as $panelId => $label) {
            $panel = Filament::getPanel($panelId);

            if (! $user instanceof User || ! $user->canAccessPanel($panel)) {
                continue;
            }

            $active = $panelId === $currentPanelId;
            $class = $active ? 'ff-panel-chip on' : 'ff-panel-chip';
            $html .= '<a href="'.e($panel->getUrl()).'" class="'.$class.'" title="'.e($panel->getId()).'">'.$label.'</a>';
        }

        return $html;
    }
}
