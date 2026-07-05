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
 *
 * Trial balance report (finance.ledger/trial-balance, ui-strategy report
 * page). Date-ranged aggregate over journal lines; debits always equal
 * credits or the ledger itself is broken.
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

    #[Url]
    public string $from = '';

    #[Url]
    public string $until = '';

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

        $this->from = $this->from !== '' ? $this->from : now()->startOfYear()->toDateString();
        $this->until = $this->until !== '' ? $this->until : now()->toDateString();
    }

    /** @return Collection<int, array{account: Account, debit_cents: int, credit_cents: int}> */
    public function getRowsProperty(): Collection
    {
        return app(LedgerServiceInterface::class)->trialBalance(
            Carbon::parse($this->from),
            Carbon::parse($this->until),
        );
    }

    public function getTotalsProperty(): array
    {
        $rows = $this->rows;

        return [
            'debit' => $rows->sum('debit_cents'),
            'credit' => $rows->sum('credit_cents'),
        ];
    }
}
