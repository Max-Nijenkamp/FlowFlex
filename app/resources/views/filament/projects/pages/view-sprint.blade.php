<x-filament-panels::page>
    {{-- Sprint meta --}}
    <div class="mb-6 space-y-4">
        @if($this->record->goal)
            <div class="rounded-lg bg-blue-50 dark:bg-blue-950/30 border border-blue-200 dark:border-blue-800 p-4">
                <p class="text-sm font-medium text-blue-800 dark:text-blue-300">Sprint Goal</p>
                <p class="mt-1 text-blue-700 dark:text-blue-400">{{ $this->record->goal }}</p>
            </div>
        @endif

        <div class="flex flex-wrap gap-6 items-center">
            {{-- Progress bar --}}
            <div class="flex-1 min-w-48">
                <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                    <span>Progress</span>
                    <span>{{ $this->getSprintProgress() }}%</span>
                </div>
                <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                    <div class="bg-green-500 h-2 rounded-full transition-all" style="width: {{ $this->getSprintProgress() }}%"></div>
                </div>
            </div>

            {{-- Dates --}}
            @if($this->record->start_date || $this->record->end_date)
                <div class="text-sm text-gray-600 dark:text-gray-400 flex items-center gap-1">
                    <x-heroicon-o-calendar class="w-4 h-4" />
                    <span>
                        {{ $this->record->start_date?->format('M j') ?? '—' }}
                        &rarr;
                        {{ $this->record->end_date?->format('M j, Y') ?? '—' }}
                    </span>
                </div>
            @endif

            {{-- Status badge --}}
            <span @class([
                'inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium',
                'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' => $this->record->status === 'active',
                'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' => $this->record->status === 'planning',
                'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' => $this->record->status === 'completed',
                'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' => $this->record->status === 'cancelled',
            ])>
                {{ ucfirst($this->record->status) }}
            </span>
        </div>
    </div>

    {{-- Kanban board --}}
    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-4">
        @foreach($this->getTasksByStatus() as $status => $column)
            <div class="flex flex-col">
                {{-- Column header --}}
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                        {{ $column['label'] }}
                    </h3>
                    <span class="inline-flex items-center justify-center w-5 h-5 text-xs font-medium rounded-full bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300">
                        {{ count($column['tasks']) }}
                    </span>
                </div>

                {{-- Task cards --}}
                <div class="flex flex-col gap-2 min-h-24 rounded-lg bg-gray-50 dark:bg-gray-900/50 p-2">
                    @forelse($column['tasks'] as $task)
                        <div class="rounded-lg bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 p-3 shadow-sm hover:shadow-md transition-shadow">
                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100 mb-2">{{ $task->title }}</p>

                            <div class="flex items-center justify-between gap-2">
                                <div class="flex items-center gap-1.5">
                                    {{-- Priority badge --}}
                                    @if($task->priority)
                                        <span @class([
                                            'inline-flex items-center px-1.5 py-0.5 rounded text-xs font-medium',
                                            'bg-red-100 text-red-700 dark:bg-red-900/50 dark:text-red-300' => $task->priority === 'urgent',
                                            'bg-orange-100 text-orange-700 dark:bg-orange-900/50 dark:text-orange-300' => $task->priority === 'high',
                                            'bg-blue-100 text-blue-700 dark:bg-blue-900/50 dark:text-blue-300' => $task->priority === 'medium',
                                            'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300' => $task->priority === 'low',
                                        ])>
                                            {{ ucfirst($task->priority) }}
                                        </span>
                                    @endif

                                    {{-- Story points --}}
                                    @if($task->story_points)
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-xs bg-purple-100 text-purple-700 dark:bg-purple-900/50 dark:text-purple-300 font-medium">
                                            {{ $task->story_points }}pt
                                        </span>
                                    @endif
                                </div>

                                {{-- Assignee avatar --}}
                                @if($task->assignee)
                                    <div class="w-6 h-6 rounded-full bg-primary-500 flex items-center justify-center text-white text-xs font-bold flex-shrink-0" title="{{ $task->assignee->first_name }} {{ $task->assignee->last_name }}">
                                        {{ strtoupper(substr($task->assignee->first_name ?? '?', 0, 1)) }}{{ strtoupper(substr($task->assignee->last_name ?? '', 0, 1)) }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-gray-400 dark:text-gray-500 text-center py-4">No tasks</p>
                    @endforelse
                </div>
            </div>
        @endforeach
    </div>
</x-filament-panels::page>
