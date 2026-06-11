<x-filament-panels::page>
    <div class="grid gap-4" style="grid-template-columns: repeat({{ max($this->getStages()->count(), 1) }}, minmax(16rem, 1fr)); overflow-x: auto;">
        @forelse ($this->getStages() as $stage)
            <div class="rounded-xl bg-gray-50 dark:bg-gray-800/50 p-3">
                <h3 class="font-semibold text-sm mb-3 flex items-center justify-between">
                    {{ $stage->name }}
                    <span class="text-xs text-gray-400">{{ $this->getDealsFor($stage->id)->count() }}</span>
                </h3>
                <div class="space-y-2">
                    @foreach ($this->getDealsFor($stage->id) as $deal)
                        <div class="rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-3">
                            <div class="font-medium text-sm">{{ $deal->name }}</div>
                            <div class="text-xs text-gray-500 mt-1">
                                €{{ number_format($deal->value_cents / 100, 2) }} · {{ $deal->probability }}%
                            </div>
                            <div class="mt-2 flex gap-1">
                                @foreach ($this->getStages() as $target)
                                    @if ($target->id !== $stage->id)
                                        <button wire:click="moveDeal('{{ $deal->id }}', '{{ $target->id }}')"
                                                class="text-xs px-1.5 py-0.5 rounded bg-gray-100 dark:bg-gray-800 hover:bg-primary-100"
                                                title="Move to {{ $target->name }}">
                                            {{ Str::limit($target->name, 8) }}
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @empty
            <p class="text-gray-500">No pipeline stages configured yet.</p>
        @endforelse
    </div>
</x-filament-panels::page>
