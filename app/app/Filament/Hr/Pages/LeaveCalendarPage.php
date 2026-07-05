<?php

declare(strict_types=1);

namespace App\Filament\Hr\Pages;

use App\Models\Hr\LeaveRequest;
use App\Models\User;
use App\Services\BillingService;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

/**
 * Team leave calendar (hr.leave/team-calendar), Teams/Outlook-style:
 * month = full-week grid with adjacent-month days dimmed and event
 * bars; week = all-day banner row above a scrollable 24h hour grid
 * with a live current-time line. Custom build — fullcalendar has no
 * Filament 5 release (ADR custom-over-missing-plugins).
 */
class LeaveCalendarPage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-calendar';

    protected static string|\UnitEnum|null $navigationGroup = 'Leave';

    protected static ?string $navigationLabel = 'Calendar';

    protected static ?string $title = 'Team calendar';

    protected static ?string $slug = 'leave-calendar';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.hr.pages.leave-calendar';

    #[Url]
    public string $view_mode = 'month';

    #[Url]
    public string $month = '';

    #[Url]
    public string $week = ''; // Monday of the shown week (Y-m-d)

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('hr.leave.view-any')
            && app(BillingService::class)->hasModule('hr.leave');
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);

        $this->month = $this->month !== '' ? $this->month : now()->format('Y-m');
        $this->week = $this->week !== '' ? $this->week : now()->startOfWeek()->toDateString();

        if (! in_array($this->view_mode, ['month', 'week'], true)) {
            $this->view_mode = 'month';
        }
    }

    public function setViewMode(string $mode): void
    {
        $this->view_mode = in_array($mode, ['month', 'week'], true) ? $mode : 'month';
    }

    public function previous(): void
    {
        if ($this->view_mode === 'week') {
            $this->week = Carbon::parse($this->week)->subWeek()->toDateString();
        } else {
            $this->month = Carbon::parse($this->month.'-01')->subMonthNoOverflow()->format('Y-m');
        }
    }

    public function next(): void
    {
        if ($this->view_mode === 'week') {
            $this->week = Carbon::parse($this->week)->addWeek()->toDateString();
        } else {
            $this->month = Carbon::parse($this->month.'-01')->addMonthNoOverflow()->format('Y-m');
        }
    }

    public function today(): void
    {
        $this->month = now()->format('Y-m');
        $this->week = now()->startOfWeek()->toDateString();
    }

    /** @return Collection<int, LeaveRequest> */
    private function requestsBetween(Carbon $start, Carbon $end): Collection
    {
        /** @var Collection<int, LeaveRequest> $requests */
        $requests = LeaveRequest::query()
            ->with(['employee', 'leaveType'])
            ->whereIn('status', ['submitted', 'approved'])
            ->where('start_date', '<=', $end->toDateString())
            ->where('end_date', '>=', $start->toDateString())
            ->get();

        return $requests;
    }

    /** @return array{name: string, type: string, color: string, pending: bool} */
    private function eventFor(LeaveRequest $request): array
    {
        return [
            'name' => $request->employee()->first()->full_name ?? '—',
            'type' => $request->leaveType()->first()->name ?? '',
            'color' => $request->leaveType()->first()->color ?? '#4ADE80',
            'pending' => (string) $request->status === 'submitted',
        ];
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        return $this->view_mode === 'week' ? $this->weekData() : $this->monthData();
    }

    /**
     * Teams-style month: full weeks including dimmed adjacent-month
     * days; events keyed per date.
     *
     * @return array<string, mixed>
     */
    private function monthData(): array
    {
        $monthStart = Carbon::parse($this->month.'-01');
        $gridStart = $monthStart->copy()->startOfWeek();
        $gridEnd = $monthStart->copy()->endOfMonth()->endOfWeek();

        $byDate = [];
        foreach ($this->requestsBetween($gridStart, $gridEnd) as $request) {
            $cursor = $request->start_date->copy()->max($gridStart);
            $last = $request->end_date->copy()->min($gridEnd);

            while ($cursor->lessThanOrEqualTo($last)) {
                if (! $cursor->isWeekend()) {
                    $byDate[$cursor->toDateString()][] = $this->eventFor($request);
                }
                $cursor->addDay();
            }
        }

        $weeks = [];
        $cursor = $gridStart->copy();

        while ($cursor->lessThanOrEqualTo($gridEnd)) {
            $week = [];

            for ($dayIndex = 0; $dayIndex < 7; $dayIndex++) {
                $week[] = [
                    'day' => $cursor->day,
                    'date' => $cursor->toDateString(),
                    'inMonth' => $cursor->month === $monthStart->month,
                    'isToday' => $cursor->isToday(),
                    'isWeekend' => $cursor->isWeekend(),
                    'events' => $byDate[$cursor->toDateString()] ?? [],
                ];
                $cursor->addDay();
            }

            $weeks[] = $week;
        }

        return [
            'mode' => 'month',
            'rangeLabel' => $monthStart->format('F Y'),
            'weeks' => $weeks,
        ];
    }

    /**
     * Teams-style week: all-day banner row (leave is all-day) above a
     * 24h hour grid; the blade draws the live time line.
     *
     * @return array<string, mixed>
     */
    private function weekData(): array
    {
        $start = Carbon::parse($this->week)->startOfWeek();
        $end = $start->copy()->addDays(6);

        $requests = $this->requestsBetween($start, $end);

        $days = [];
        $cursor = $start->copy();

        while ($cursor->lessThanOrEqualTo($end)) {
            $events = [];

            foreach ($requests as $request) {
                if ($request->start_date->lessThanOrEqualTo($cursor) && $request->end_date->greaterThanOrEqualTo($cursor)) {
                    $events[] = $this->eventFor($request);
                }
            }

            $days[] = [
                'dayName' => $cursor->format('D'),
                'dayNumber' => $cursor->day,
                'date' => $cursor->toDateString(),
                'isToday' => $cursor->isToday(),
                'isWeekend' => $cursor->isWeekend(),
                'events' => $events,
            ];

            $cursor->addDay();
        }

        return [
            'mode' => 'week',
            'rangeLabel' => $start->format('d M').' — '.$end->format('d M Y'),
            'days' => $days,
            'todayIndex' => now()->betweenIncluded($start, $end->copy()->endOfDay()) ? (int) now()->dayOfWeekIso - 1 : null,
        ];
    }
}
