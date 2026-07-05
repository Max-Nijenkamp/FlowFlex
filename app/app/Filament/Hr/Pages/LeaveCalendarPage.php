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
 * Team leave calendar (hr.leave/team-calendar). Custom month grid —
 * saade/filament-fullcalendar has no Filament 5 build (ADR custom-over-
 * missing-plugins). Approved + pending leave per day, coloured by type.
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
    public string $month = '';

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
    }

    public function previousMonth(): void
    {
        $this->month = Carbon::parse($this->month.'-01')->subMonthNoOverflow()->format('Y-m');
    }

    public function nextMonth(): void
    {
        $this->month = Carbon::parse($this->month.'-01')->addMonthNoOverflow()->format('Y-m');
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $start = Carbon::parse($this->month.'-01');
        $end = $start->copy()->endOfMonth();

        /** @var Collection<int, LeaveRequest> $requests */
        $requests = LeaveRequest::query()
            ->with(['employee', 'leaveType'])
            ->whereIn('status', ['submitted', 'approved'])
            ->where('start_date', '<=', $end->toDateString())
            ->where('end_date', '>=', $start->toDateString())
            ->get();

        // day-of-month => list of {name, color, pending}
        $byDay = [];
        foreach ($requests as $request) {
            $cursor = $request->start_date->copy()->max($start);
            $last = $request->end_date->copy()->min($end);

            while ($cursor->lessThanOrEqualTo($last)) {
                if (! $cursor->isWeekend()) {
                    $byDay[$cursor->day][] = [
                        'name' => $request->employee()->first()->full_name ?? '—',
                        'type' => $request->leaveType()->first()->name ?? '',
                        'color' => $request->leaveType()->first()->color ?? '#4ADE80',
                        'pending' => (string) $request->status === 'submitted',
                    ];
                }
                $cursor->addDay();
            }
        }

        return [
            'monthLabel' => $start->format('F Y'),
            'daysInMonth' => $start->daysInMonth,
            'firstWeekday' => (int) $start->dayOfWeekIso, // 1 = Monday
            'byDay' => $byDay,
            'today' => now()->format('Y-m') === $this->month ? now()->day : null,
        ];
    }
}
