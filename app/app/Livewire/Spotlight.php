<?php

declare(strict_types=1);

namespace App\Livewire;

use Filament\Facades\Filament;
use Filament\Panel;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Spotlight ⌘K palette (filament-patterns item 14, panel-chrome §4).
 * Panel-scoped: navigation (pages + resources, canAccess-filtered),
 * quick-create actions, and record results via the panel's global search
 * provider. Injected on every authenticated panel page via BODY_END.
 */
class Spotlight extends Component
{
    public string $query = '';

    public string $panelId = '';

    public function mount(): void
    {
        $this->panelId = Filament::getCurrentPanel()?->getId() ?? 'app';
    }

    /**
     * Livewire update requests don't run panel routing — restore the panel
     * context before touching panel-scoped APIs (tenant-context-pitfalls).
     */
    protected function panel(): Panel
    {
        $panel = Filament::getPanel($this->panelId);

        Filament::setCurrentPanel($panel);

        return $panel;
    }

    /** Result caps per spec (keyboard-palette): nav 8 · quick-create 5 · 6 per search category. */
    public const NAV_CAP = 8;

    public const QUICK_CREATE_CAP = 5;

    public const SEARCH_CATEGORY_CAP = 6;

    /** @return array<int, array{group: string, label: string, url: string, icon: string}> */
    public function getResultsProperty(): array
    {
        $panel = $this->panel();
        $query = trim($this->query);
        $needle = mb_strtolower($query);
        $matches = fn (string $label): bool => $needle === '' || str_contains(mb_strtolower($label), $needle);

        /** @var list<array{group: string, label: string, url: string, icon: string}> $nav */
        $nav = [];

        /** @var list<array{group: string, label: string, url: string, icon: string}> $quickCreate */
        $quickCreate = [];

        foreach ($panel->getPages() as $page) {
            $label = (string) $page::getNavigationLabel();

            if (! $page::canAccess() || ! $matches($label)) {
                continue;
            }

            $nav[] = [
                'group' => 'Pages',
                'label' => $label,
                'url' => $page::getUrl(panel: $this->panelId),
                'icon' => 'page',
            ];
        }

        if (filled($profileUrl = $panel->getProfileUrl()) && $matches('Profile')) {
            $nav[] = [
                'group' => 'Account',
                'label' => 'Profile',
                'url' => $profileUrl,
                'icon' => 'page',
            ];
        }

        foreach ($panel->getResources() as $resource) {
            if (! $resource::canAccess()) {
                continue;
            }

            $label = (string) $resource::getNavigationLabel();

            if ($matches($label)) {
                $nav[] = [
                    'group' => 'Resources',
                    'label' => $label,
                    'url' => $resource::getUrl('index', panel: $this->panelId),
                    'icon' => 'list',
                ];
            }

            $createLabel = 'New '.str($resource::getModelLabel())->lower();

            if ($resource::hasPage('create') && $resource::canCreate() && $matches($createLabel)) {
                $quickCreate[] = [
                    'group' => 'Quick create',
                    'label' => $createLabel,
                    'url' => $resource::getUrl('create', panel: $this->panelId),
                    'icon' => 'plus',
                ];
            }
        }

        $items = [
            ...array_slice($nav, 0, self::NAV_CAP),
            ...array_slice($quickCreate, 0, self::QUICK_CREATE_CAP),
        ];

        if (mb_strlen($query) >= 2) {
            $records = $panel->getGlobalSearchProvider()?->getResults($query);

            foreach ($records?->getCategories() ?? [] as $category => $results) {
                $categoryCount = 0;

                foreach ($results as $result) {
                    if (++$categoryCount > self::SEARCH_CATEGORY_CAP) {
                        break;
                    }

                    $items[] = [
                        'group' => (string) $category,
                        'label' => (string) $result->title,
                        'url' => (string) $result->url,
                        'icon' => 'record',
                    ];
                }
            }
        }

        return $items;
    }

    public function render(): View
    {
        return view('livewire.spotlight');
    }
}
