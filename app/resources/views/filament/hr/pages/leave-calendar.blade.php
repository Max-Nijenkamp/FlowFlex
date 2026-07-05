<x-filament-panels::page>
    <div class="ff-cal">
        <div class="ff-cal-toolbar">
            <div class="ff-cal-navgroup">
                <button type="button" class="ff-cal-nav" wire:click="previous" title="Previous">‹</button>
                <button type="button" class="ff-cal-nav ff-cal-today" wire:click="today">Today</button>
                <button type="button" class="ff-cal-nav" wire:click="next" title="Next">›</button>
            </div>
            <span class="ff-cal-month">{{ $rangeLabel }}</span>

            <div class="ff-chips ff-cal-modes">
                <button type="button" wire:click="setViewMode('month')" @class(['ff-chip', 'ff-on' => $mode === 'month'])>Month</button>
                <button type="button" wire:click="setViewMode('week')" @class(['ff-chip', 'ff-on' => $mode === 'week'])>Week</button>
            </div>

            <span class="ff-cal-hint">Approved solid · pending striped</span>
        </div>

        @if ($mode === 'month')
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
        @else
            <div class="ff-week">
                @foreach ($days as $day)
                    <div @class(['ff-week-col', 'ff-today' => $day['isToday'], 'ff-weekend' => $day['isWeekend']])>
                        <div class="ff-week-head">
                            <span class="ff-week-day">{{ $day['label'] }}</span>
                            @if ($day['isToday'])
                                <span class="ff-week-now" title="Refreshes with the page">
                                    <span class="ff-week-now-dot"></span>{{ $now->format('H:i') }}
                                </span>
                            @endif
                        </div>
                        <div class="ff-week-events">
                            @forelse ($day['events'] as $event)
                                <div
                                    @class(['ff-week-event', 'ff-pending' => $event['pending']])
                                    style="--ev-c: {{ $event['color'] }}"
                                >
                                    <span class="ff-week-event-name">{{ $event['name'] }}</span>
                                    <span class="ff-week-event-type">{{ $event['type'] }}{{ $event['pending'] ? ' · pending' : '' }}</span>
                                </div>
                            @empty
                                <span class="ff-week-empty">{{ $day['isWeekend'] ? 'Weekend' : '—' }}</span>
                            @endforelse
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-filament-panels::page>
