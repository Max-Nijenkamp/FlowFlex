<?php

declare(strict_types=1);

namespace App\Livewire;

use Filament\Facades\Filament;
use Illuminate\Contracts\View\View;
use Livewire\Component;

/**
 * Panel-scoped quick-search palette (⌘K / Ctrl+K). Injected on every panel
 * via the BODY_END render hook. Searches the current panel's navigation
 * (resources + pages, canAccess-filtered), quick-create actions, and
 * globally-searchable records via Filament's global search provider.
 */
class Spotlight extends Component
{
    public string $panelId;

    public string $query = '';

    public function mount(string $panelId): void
    {
        $this->panelId = $panelId;
    }

    /** @return array<int, array{group: string, items: array<int, array{label: string, sub: string, url: string}>}> */
    public function getResultsProperty(): array
    {
        // Livewire update requests don't run panel routing — restore the
        // panel context before touching panel-aware APIs.
        $panel = Filament::getPanel($this->panelId);
        Filament::setCurrentPanel($panel);

        $needle = mb_strtolower(trim($this->query));
        $groups = [];

        $nav = [];
        $create = [];

        foreach ($panel->getResources() as $resource) {
            if (! $resource::canAccess()) {
                continue;
            }

            $label = (string) $resource::getNavigationLabel();

            if ($needle === '' || str_contains(mb_strtolower($label), $needle)) {
                $nav[] = ['label' => $label, 'sub' => 'Go to list', 'url' => $resource::getUrl()];
            }

            if ($needle !== '' && $resource::hasPage('create') && $resource::canCreate()) {
                $createLabel = 'New '.$resource::getModelLabel();

                if (str_contains(mb_strtolower($createLabel), $needle)) {
                    $create[] = ['label' => $createLabel, 'sub' => 'Create', 'url' => $resource::getUrl('create')];
                }
            }
        }

        foreach ($panel->getPages() as $page) {
            if (! $page::canAccess()) {
                continue;
            }

            $label = (string) $page::getNavigationLabel();

            if ($needle === '' || str_contains(mb_strtolower($label), $needle)) {
                $nav[] = ['label' => $label, 'sub' => 'Go to page', 'url' => $page::getUrl(panel: $this->panelId)];
            }
        }

        if ($nav !== []) {
            $groups[] = ['group' => 'Navigation', 'items' => array_slice($nav, 0, 8)];
        }

        if ($create !== []) {
            $groups[] = ['group' => 'Quick actions', 'items' => array_slice($create, 0, 5)];
        }

        if (mb_strlen($needle) >= 2) {
            $results = $panel->getGlobalSearchProvider()->getResults($this->query);

            foreach ($results?->getCategories() ?? [] as $category => $items) {
                $groups[] = [
                    'group' => (string) $category,
                    'items' => collect($items)
                        ->take(6)
                        ->map(fn ($result): array => [
                            'label' => (string) $result->title,
                            'sub' => implode(' · ', array_values($result->details)),
                            'url' => $result->url,
                        ])
                        ->all(),
                ];
            }
        }

        return $groups;
    }

    public function render(): View
    {
        return view('livewire.spotlight');
    }
}
