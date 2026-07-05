<x-filament-panels::page>
    <div class="ff-onb">
        <div class="ff-onb-progress">
            <div class="ff-onb-bar"><span style="width: {{ $progress }}%"></span></div>
            <span class="ff-onb-pct">{{ $progress }}% complete</span>
        </div>

        <div class="ff-onb-list">
            @foreach ($tasks as $planTask)
                @php
                    $task = $planTask->task()->first();
                    $due = $task?->due_days_after_start !== null
                        ? $startedAt->copy()->addDays($task->due_days_after_start)
                        : null;
                @endphp
                <div @class(['ff-onb-row', 'ff-done' => $planTask->status !== 'pending']) wire:key="task-{{ $planTask->id }}">
                    <span @class(['ff-onb-check', 'ff-on' => $planTask->status === 'complete', 'ff-skip' => $planTask->status === 'skipped'])>
                        @if ($planTask->status === 'complete') ✓ @elseif ($planTask->status === 'skipped') – @endif
                    </span>
                    <span class="ff-onb-meta">
                        <span class="ff-onb-title">{{ $task?->title }}</span>
                        <span class="ff-onb-sub">
                            {{ strtoupper($task?->assigned_role ?? '') }}
                            @if ($due) · due {{ $due->format('d M') }} @endif
                            @if ($planTask->completed_at) · {{ $planTask->status }} {{ $planTask->completed_at->format('d M') }} by {{ $planTask->completedBy()->first()->full_name ?? '—' }} @endif
                        </span>
                    </span>
                    @if ($planTask->status === 'pending')
                        <span class="ff-onb-actions">
                            <button type="button" class="ff-onb-do" wire:click="completeTask('{{ $planTask->id }}')">Complete</button>
                            <button type="button" class="ff-onb-skip" wire:click="skipTask('{{ $planTask->id }}')">Skip</button>
                        </span>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-filament-panels::page>
