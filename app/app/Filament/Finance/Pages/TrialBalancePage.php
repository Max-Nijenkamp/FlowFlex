<?php

declare(strict_types=1);

namespace App\Filament\Finance\Pages;

use App\Contracts\Finance\LedgerServiceInterface;
use App\Models\Finance\Account;
use App\Models\User;
use App\Services\BillingService;
use Filament\Pages\Page;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Url;

/**
 * @property-read Collection<int, array{account: Account, debit_cents: int, credit_cents: int}> $rows
 * @property-read Collection<string, array{rows: Collection<int, array{account: Account, debit_cents: int, credit_cents: int}>, debit: int, credit: int}> $groups
 *
 * Trial balance report (finance.ledger/trial-balance). One-click period
 * presets + custom range; rows grouped per account type with subtotals;
 * debits must equal credits or the ledger itself is broken.
 */
class TrialBalancePage extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-scale';

    protected static string|\UnitEnum|null $navigationGroup = 'Ledger';

    protected static ?string $navigationLabel = 'Trial balance';

    protected static ?string $title = 'Trial balance';

    protected static ?string $slug = 'trial-balance';

    protected static ?int $navigationSort = 4;

    protected string $view = 'filament.finance.pages.trial-balance';

    public const TYPE_ORDER = ['asset', 'liability', 'equity', 'revenue', 'expense'];

    #[Url]
    public string $from = '';

    #[Url]
    public string $until = '';

    #[Url]
    public string $preset = 'this-year';

    public static function canAccess(): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('finance.ledger.view')
            && app(BillingService::class)->hasModule('finance.ledger');
    }

    public function mount(): void
    {
        abort_unless(static::canAccess(), 403);

        if ($this->from === '' || $this->until === '') {
            $this->applyPreset($this->preset);
        }
    }

    public function applyPreset(string $preset): void
    {
        $this->preset = $preset;

        [$from, $until] = match ($preset) {
            'this-month' => [now()->startOfMonth(), now()],
            'last-month' => [now()->subMonthNoOverflow()->startOfMonth(), now()->subMonthNoOverflow()->endOfMonth()],
            'this-quarter' => [now()->startOfQuarter(), now()],
            'last-quarter' => [now()->subQuarter()->startOfQuarter(), now()->subQuarter()->endOfQuarter()],
            'last-year' => [now()->subYear()->startOfYear(), now()->subYear()->endOfYear()],
            default => [now()->startOfYear(), now()],
        };

        $this->from = $from->toDateString();
        $this->until = $until->toDateString();
    }

    /** Manual date edits switch the chips to "custom". */
    public function updatedFrom(): void
    {
        $this->preset = 'custom';
    }

    public function updatedUntil(): void
    {
        $this->preset = 'custom';
    }

    /** @return Collection<int, array{account: Account, debit_cents: int, credit_cents: int}> */
    public function getRowsProperty(): Collection
    {
        return app(LedgerServiceInterface::class)->trialBalance(
            Carbon::parse($this->from !== '' ? $this->from : now()->startOfYear()->toDateString()),
            Carbon::parse($this->until !== '' ? $this->until : now()->toDateString()),
        );
    }

    /** Rows grouped per account type (chart order) with per-type subtotals. */
    public function getGroupsProperty(): Collection
    {
        return $this->rows
            ->groupBy(fn (array $row): string => $row['account']->type)
            ->sortBy(fn ($rows, string $type): int => (int) array_search($type, self::TYPE_ORDER, true))
            ->map(fn ($rows) => [
                'rows' => $rows->values(),
                'debit' => (int) $rows->sum('debit_cents'),
                'credit' => (int) $rows->sum('credit_cents'),
            ]);
    }

    /** @return array{debit: int, credit: int} */
    public function getTotalsProperty(): array
    {
        return [
            'debit' => (int) $this->rows->sum('debit_cents'),
            'credit' => (int) $this->rows->sum('credit_cents'),
        ];
    }
}
