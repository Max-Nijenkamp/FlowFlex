<x-filament-panels::page>
    <div wire:init="loadData">
        @if (! $readyToLoad)
            <x-skeleton.list :rows="8" />
        @else

    @php($tree = $this->getTree())
    @if (empty($tree))
        <p class="text-gray-500">No employees yet.</p>
    @else
        <div class="space-y-2">
            @foreach ($tree as $node)
                @include('filament.hr.pages.partials.org-node', ['node' => $node, 'depth' => 0])
            @endforeach
        </div>
    @endif

        @endif
    </div>
</x-filament-panels::page>
