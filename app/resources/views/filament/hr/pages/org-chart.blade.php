<x-filament-panels::page>
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
</x-filament-panels::page>
