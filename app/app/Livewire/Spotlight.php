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

    /** @return array<int, array{group: string, label: string, url: string, icon: string}> */
    public function getResultsProperty(): array
    {
        $panel = $this->panel();
        $query = trim($this->query);

        /** @var list<array{group: string, label: string, url: string, icon: string}> $items */
        $items = [];

        foreach ($panel->getPages() as $page) {
            if (! $page::canAccess()) {
                continue;
            }

            $items[] = [
                'group' => 'Pages',
                'label' => (string) $page::getNavigationLabel(),
                'url' => $page::getUrl(panel: $this->panelId),
                'icon' => 'page',
            ];
        }

        if (filled($profileUrl = $panel->getProfileUrl())) {
            $items[] = [
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

            $items[] = [
                'group' => 'Resources',
                'label' => $label,
                'url' => $resource::getUrl('index', panel: $this->panelId),
                'icon' => 'list',
            ];

            if ($resource::hasPage('create') && $resource::canCreate()) {
                $items[] = [
                    'group' => 'Quick create',
                    'label' => 'New '.str($resource::getModelLabel())->lower(),
                    'url' => $resource::getUrl('create', panel: $this->panelId),
                    'icon' => 'plus',
                ];
            }
        }

        if ($query !== '') {
            $needle = mb_strtolower($query);
            $filtered = [];

            foreach ($items as $item) {
                if (str_contains(mb_strtolower($item['label']), $needle)) {
                    $filtered[] = $item;
                }
            }

            $items = $filtered;
        }

        if (mb_strlen($query) >= 2) {
            $records = $panel->getGlobalSearchProvider()?->getResults($query);

            foreach ($records?->getCategories() ?? [] as $category => $results) {
                foreach ($results as $result) {
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
