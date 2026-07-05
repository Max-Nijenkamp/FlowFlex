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
            {{-- Teams-style month grid: fixed weekday header, full weeks,
                 adjacent-month days dimmed, today = accent day chip --}}
            <div class="ff-tmo">
                <div class="ff-tmo-head">
                    @foreach (['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'] as $weekday)
                        <div class="ff-tmo-head-cell">{{ $weekday }}</div>
                    @endforeach
                </div>
                <div class="ff-tmo-body">
                    @foreach ($weeks as $weekIndex => $week)
                        <div class="ff-tmo-row" wire:key="week-{{ $weekIndex }}">
                            @foreach ($week as $day)
                                <div
                                    wire:key="day-{{ $day['date'] }}"
                                    @class([
                                        'ff-tmo-cell',
                                        'ff-out' => ! $day['inMonth'],
                                        'ff-weekend' => $day['isWeekend'],
                                        'ff-today' => $day['isToday'],
                                    ])
                                >
                                    <span class="ff-tmo-daynum">{{ $day['day'] }}</span>
                                    <div class="ff-tmo-events">
                                        @foreach (array_slice($day['events'], 0, 3) as $event)
                                            <span
                                                @class(['ff-tmo-event', 'ff-pending' => $event['pending']])
                                                style="--ev-c: {{ $event['color'] }}"
                                                title="{{ $event['name'] }} — {{ $event['type'] }}{{ $event['pending'] ? ' (pending)' : '' }}"
                                            >{{ $event['name'] }}</span>
                                        @endforeach
                                        @if (count($day['events']) > 3)
                                            <span class="ff-tmo-more">+{{ count($day['events']) - 3 }} more</span>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            {{-- Teams-style week: day headers, all-day banner row (leave is
                 all-day), scrollable 24h grid with a live now-line --}}
            <div
                class="ff-twk"
                x-data="{
                    nowPct: 0,
                    nowLabel: '',
                    tick() {
                        const now = new Date();
                        this.nowPct = (now.getHours() * 60 + now.getMinutes()) / 1440 * 100;
                        this.nowLabel = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
                    },
                }"
                x-init="
                    tick();
                    setInterval(() => tick(), 30000);
                    $nextTick(() => { $refs.canvas.scrollTop = $refs.canvas.scrollHeight * 7 / 24 });
                "
            >
                <div class="ff-twk-head">
                    <div class="ff-twk-gutterhead"></div>
                    @foreach ($days as $day)
                        <div @class(['ff-twk-dayhead', 'ff-today' => $day['isToday'], 'ff-weekend' => $day['isWeekend']])>
                            <span class="ff-twk-dayname">{{ $day['dayName'] }}</span>
                            <span class="ff-twk-daynum">{{ $day['dayNumber'] }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="ff-twk-allday">
                    <div class="ff-twk-gutterlabel">All day</div>
                    @foreach ($days as $day)
                        <div @class(['ff-twk-alldaycell', 'ff-today' => $day['isToday'], 'ff-weekend' => $day['isWeekend']])>
                            @foreach ($day['events'] as $event)
                                <span
                                    @class(['ff-twk-banner', 'ff-pending' => $event['pending']])
                                    style="--ev-c: {{ $event['color'] }}"
                                    title="{{ $event['name'] }} — {{ $event['type'] }}{{ $event['pending'] ? ' (pending)' : '' }}"
                                >{{ $event['name'] }}</span>
                            @endforeach
                        </div>
                    @endforeach
                </div>

                <div class="ff-twk-canvas" x-ref="canvas">
                    <div class="ff-twk-grid">
                        <div class="ff-twk-gutter">
                            @for ($hour = 0; $hour < 24; $hour++)
                                <div class="ff-twk-hour">{{ str_pad((string) $hour, 2, '0', STR_PAD_LEFT) }}:00</div>
                            @endfor
                        </div>
                        @foreach ($days as $day)
                            <div @class(['ff-twk-daycol', 'ff-today' => $day['isToday'], 'ff-weekend' => $day['isWeekend']])>
                                @for ($hour = 0; $hour < 24; $hour++)
                                    <div class="ff-twk-slot"></div>
                                @endfor
                            </div>
                        @endforeach

                        @if ($todayIndex !== null)
                            <div class="ff-twk-nowline" x-bind:style="`top: ${nowPct}%`">
                                <span class="ff-twk-nowchip" x-text="nowLabel"></span>
                                <span class="ff-twk-nowdot" style="left: calc(var(--gutter) + (100% - var(--gutter)) / 7 * {{ $todayIndex }})"></span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endif
    </div>
</x-filament-panels::page>
