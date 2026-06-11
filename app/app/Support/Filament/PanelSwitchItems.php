<?php

declare(strict_types=1);

namespace App\Support\Filament;

use App\Models\User;
use Filament\Facades\Filament;
use Filament\Navigation\MenuItem;
use Filament\Support\Icons\Heroicon;
use Illuminate\Support\Facades\Auth;

/**
 * "Switch panel" entries for the tenant panels' user menus. Visibility
 * mirrors User::canAccessPanel — /app for everyone, domain panels behind
 * their access.{id}-panel permission.
 */
final class PanelSwitchItems
{
    private const array PANELS = [
        'app' => ['Workspace', Heroicon::OutlinedSquares2x2],
        'hr' => ['HR & People', Heroicon::OutlinedUsers],
        'finance' => ['Finance', Heroicon::OutlinedBanknotes],
        'crm' => ['CRM & Sales', Heroicon::OutlinedPresentationChartLine],
    ];

    /** @return list<MenuItem> */
    public static function make(string $currentPanelId): array
    {
        $items = [];

        foreach (self::PANELS as $panelId => [$label, $icon]) {
            if ($panelId === $currentPanelId) {
                continue;
            }

            $items[] = MenuItem::make()
                ->label("Switch to {$label}")
                ->icon($icon)
                ->url(fn (): string => Filament::getPanel($panelId)->getUrl())
                ->visible(function () use ($panelId): bool {
                    $user = Auth::guard('web')->user();

                    return $user instanceof User
                        && $user->canAccessPanel(Filament::getPanel($panelId));
                });
        }

        return $items;
    }
}
