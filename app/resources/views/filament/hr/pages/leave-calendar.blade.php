<x-filament-panels::page>
    <div class="ff-cal">
        <div class="ff-cal-toolbar">
            <button type="button" class="ff-cal-nav" wire:click="previousMonth">‹</button>
            <span class="ff-cal-month">{{ $monthLabel }}</span>
            <button type="button" class="ff-cal-nav" wire:click="nextMonth">›</button>
            <span class="ff-cal-hint">Approved leave solid · pending striped</span>
        </div>

        <div class="ff-cal-grid">
            @foreach (['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'] as $weekday)
                <div class="ff-cal-head">{{ $weekday }}</div>
            @endforeach

            @for ($blank = 1; $blank < $firstWeekday; $blank++)
                <div class="ff-cal-cell ff-blank"></div>
            @endfor

            @for ($day = 1; $day <= $daysInMonth; $day++)
                <div @class(['ff-cal-cell', 'ff-today' => $today === $day])>
                    <span class="ff-cal-day">{{ $day }}</span>
                    @foreach (array_slice($byDay[$day] ?? [], 0, 3) as $event)
                        <span
                            @class(['ff-cal-event', 'ff-pending' => $event['pending']])
                            style="--ev-c: {{ $event['color'] }}"
                            title="{{ $event['name'] }} — {{ $event['type'] }}{{ $event['pending'] ? ' (pending)' : '' }}"
                        >{{ $event['name'] }}</span>
                    @endforeach
                    @if (count($byDay[$day] ?? []) > 3)
                        <span class="ff-cal-more">+{{ count($byDay[$day]) - 3 }} more</span>
                    @endif
                </div>
            @endfor
        </div>
    </div>
</x-filament-panels::page>
