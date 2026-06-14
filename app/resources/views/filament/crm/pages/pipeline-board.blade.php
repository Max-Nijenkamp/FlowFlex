<x-filament-panels::page>
    <div wire:init="loadBoard">
        @if (! $readyToLoad)
            <x-skeleton.board :columns="4" :cards="3" />
        @else
            {{-- Pipeline switcher + value summary + manage-stages link --}}
            <div class="mb-4 flex flex-wrap items-center justify-between gap-3">
                <div class="flex flex-wrap items-center gap-2">
                    @foreach ($this->getPipelines() as $pipeline)
                        <button
                            type="button"
                            wire:click="switchPipeline('{{ $pipeline->id }}')"
                            @class([
                                'rounded-full px-4 py-1.5 text-sm font-medium transition duration-150 ease-out active:scale-[0.97]',
                                'bg-primary-600 text-white shadow-sm' => $pipelineId === $pipeline->id,
                                'bg-white text-gray-600 ring-1 ring-[#D8D4CA] hover:border-gray-300 hover:bg-[#FAF9F5] dark:bg-gray-900 dark:text-gray-300 dark:ring-gray-700' => $pipelineId !== $pipeline->id,
                            ])
                        >
                            {{ $pipeline->name }}
                        </button>
                    @endforeach
                </div>

                <div class="flex items-center gap-4 text-sm">
                    <span class="text-gray-500 dark:text-gray-400">
                        Open value:
                        <span class="font-mono font-semibold text-gray-950 dark:text-white">
                            €{{ number_format($this->getPipelineValueCents() / 100, 0) }}
                        </span>
                    </span>
                    @if ($pipelineId)
                        <a href="{{ \App\Filament\CRM\Resources\PipelineResource::getUrl('edit', ['record' => $pipelineId]) }}"
                            class="inline-flex items-center gap-1.5 text-sm font-semibold text-primary-600 hover:underline dark:text-primary-400">
                            <x-filament::icon icon="heroicon-o-adjustments-horizontal" class="h-4 w-4" />
                            Manage stages
                        </a>
                    @endif
                </div>
            </div>

            {{-- Board: x-data tracks whether a drag is in progress so every column
                 can show it's a live drop target (the old board looked dead). --}}
            <div
                x-data="{ dragging: false }"
                class="grid gap-4"
                style="grid-template-columns: repeat({{ max($this->getStages()->count(), 1) }}, minmax(16rem, 1fr)); overflow-x: auto;"
            >
                @forelse ($this->getStages() as $stage)
                    @php($stageDeals = $this->getDealsFor($stage->id))
                    <div
                        x-data="{ over: false }"
                        @dragover.prevent="over = true"
                        @dragenter.prevent="over = true"
                        @dragleave="over = false"
                        @drop.prevent="
                            over = false; dragging = false;
                            const card = document.getElementById('deal-' + $event.dataTransfer.getData('deal'));
                            if (card) { $el.querySelector('[data-cards]').prepend(card); }
                            $wire.moveDeal($event.dataTransfer.getData('deal'), '{{ $stage->id }}')
                        "
                        :class="{
                            'ring-2 ring-primary-500 bg-primary-50/60 dark:bg-primary-500/10': over,
                            'ring-1 ring-dashed ring-[#D8D4CA] dark:ring-gray-700': dragging && ! over,
                        }"
                        class="rounded-[14px] bg-[#F4F2EC] p-3 transition duration-150 ease-out dark:bg-gray-800/50"
                    >
                        <div class="mb-1 flex items-center justify-between">
                            <h3 class="flex items-center gap-2 text-sm font-semibold" style="font-family:'Archivo',sans-serif;">
                                <span class="h-2.5 w-2.5 rounded-[3px] bg-primary-500"></span>
                                {{ $stage->name }}
                            </h3>
                            <span class="font-mono text-xs text-gray-400">{{ $stageDeals->count() }}</span>
                        </div>
                        <p class="mb-3 font-mono text-[11px] text-gray-400">
                            €{{ number_format($stageDeals->sum('value_cents') / 100, 0) }} · {{ rtrim(rtrim(number_format($stage->probability_default, 1), '0'), '.') }}%
                        </p>
                        <div class="min-h-24 space-y-2 rounded-lg transition-colors" data-cards
                             :class="{ 'outline-dashed outline-2 outline-primary-300 outline-offset-2': over }">
                            @foreach ($stageDeals as $deal)
                                <div id="deal-{{ $deal->id }}"
                                     class="group cursor-grab rounded-lg border border-[#D8D4CA] bg-white p-3 shadow-[0_1px_2px_rgba(17,24,39,0.04)] transition duration-150 ease-out hover:-translate-y-0.5 hover:border-primary-300 hover:shadow-md active:cursor-grabbing active:scale-[0.98] dark:border-gray-700 dark:bg-gray-900 dark:hover:border-primary-500"
                                     draggable="true"
                                     x-data
                                     @dragstart="dragging = true; $event.dataTransfer.effectAllowed = 'move'; $event.dataTransfer.setData('deal', '{{ $deal->id }}'); $el.classList.add('opacity-40')"
                                     @dragend="dragging = false; $el.classList.remove('opacity-40')">
                                    <div class="flex items-start justify-between gap-2">
                                        <span class="text-sm font-medium">{{ $deal->name }}</span>
                                        <x-filament::icon icon="heroicon-o-arrows-pointing-out"
                                            class="mt-0.5 h-3.5 w-3.5 shrink-0 text-gray-300 opacity-0 transition group-hover:opacity-100" />
                                    </div>
                                    <div class="mt-1 font-mono text-[11px] text-gray-500">
                                        €{{ number_format($deal->value_cents / 100, 2) }} · {{ $deal->probability }}%
                                    </div>
                                </div>
                            @endforeach
                            @if ($stageDeals->isEmpty())
                                <p class="px-1 py-3 text-center text-[11px] text-gray-400" x-show="! over">Drop deals here</p>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="rounded-[14px] border border-dashed border-[#D8D4CA] p-8 text-center dark:border-gray-700">
                        <p class="font-semibold" style="font-family:'Archivo',sans-serif;">No stages in this pipeline yet</p>
                        <p class="mt-1 text-sm text-gray-500">A pipeline needs stages before deals can flow.</p>
                        @if ($pipelineId)
                            <a href="{{ \App\Filament\CRM\Resources\PipelineResource::getUrl('edit', ['record' => $pipelineId]) }}"
                                class="mt-4 inline-flex items-center gap-1.5 rounded-lg bg-primary-600 px-4 py-2 text-sm font-semibold text-white hover:bg-primary-700">
                                Add stages
                            </a>
                        @endif
                    </div>
                @endforelse
            </div>
        @endif
    </div>
</x-filament-panels::page>
