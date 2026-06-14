<x-filament-panels::page>
    <div wire:init="loadData">
        @if (! $readyToLoad)
            <x-skeleton.list :rows="8" />
        @else
            @php
                $tree = $this->getTree();

                $countNodes = function (array $nodes) use (&$countNodes): int {
                    $total = 0;

                    foreach ($nodes as $node) {
                        $total += 1 + $countNodes($node['children']);
                    }

                    return $total;
                };

                $totalEmployees = $countNodes($tree);
            @endphp

            <x-filament::section>
                <x-slot name="heading">Organisation</x-slot>
                <x-slot name="description">{{ $totalEmployees }} {{ Str::plural('employee', $totalEmployees) }}</x-slot>

                @if (empty($tree))
                    <p class="text-sm text-gray-500 dark:text-gray-400">No employees yet.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($tree as $node)
                            @include('filament.hr.pages.partials.org-node', ['node' => $node])
                        @endforeach
                    </div>
                @endif
            </x-filament::section>
        @endif
    </div>
</x-filament-panels::page>
