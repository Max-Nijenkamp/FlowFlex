<div style="margin-left: {{ $depth * 1.5 }}rem"
     class="rounded-lg border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-4 py-2 inline-block min-w-64">
    <div class="font-medium">{{ $node['name'] }}</div>
    <div class="text-sm text-gray-500">{{ $node['title'] }}</div>
</div>
@foreach ($node['children'] as $child)
    @include('filament.hr.pages.partials.org-node', ['node' => $child, 'depth' => $depth + 1])
@endforeach
