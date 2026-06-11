<x-filament-panels::page>
    <div wire:init="loadBoard">
        @if (! $readyToLoad)
            <x-skeleton.board :columns="4" :cards="3" />
        @else
            <div class="grid gap-4" style="grid-template-columns: repeat({{ max($this->getStages()->count(), 1) }}, minmax(16rem, 1fr)); overflow-x: auto;">
                @forelse ($this->getStages() as $stage)
                    <div class="rounded-xl bg-gray-50 dark:bg-gray-800/50 p-3 transition ease-out duration-200"
                         x-data
                         @dragover.prevent
                         {{-- Optimistic: card DOM node moves on drop; server call follows. On error the board re-renders from truth. --}}
                         @drop.prevent="
                            const card = document.getElementById('deal-' + $event.dataTransfer.getData('deal'));
                            if (card) { $el.querySelector('[data-cards]').prepend(card); }
                            $wire.moveDeal($event.dataTransfer.getData('deal'), '{{ $stage->id }}')
                         ">
                        <h3 class="font-semibold text-sm mb-3 flex items-center justify-between">
                            {{ $stage->name }}
                            <span class="text-xs text-gray-400">{{ $this->getDealsFor($stage->id)->count() }}</span>
                        </h3>
                        <div class="space-y-2 min-h-16" data-cards>
                            @foreach ($this->getDealsFor($stage->id) as $deal)
                                <div id="deal-{{ $deal->id }}"
                                     class="rounded-lg bg-white dark:bg-gray-900 border border-gray-200 dark:border-gray-700 p-3 cursor-grab active:cursor-grabbing transition ease-out duration-200"
                                     draggable="true"
                                     x-data
                                     @dragstart="$event.dataTransfer.setData('deal', '{{ $deal->id }}')">
                                    <div class="font-medium text-sm">{{ $deal->name }}</div>
                                    <div class="text-xs text-gray-500 mt-1">
                                        €{{ number_format($deal->value_cents / 100, 2) }} · {{ $deal->probability }}%
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <p class="text-gray-500">No pipeline stages configured yet.</p>
                @endforelse
            </div>
        @endif
    </div>
</x-filament-panels::page>
