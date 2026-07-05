<?php

declare(strict_types=1);

namespace App\Filament\Finance\Resources\BankAccountResource\Pages;

use App\Filament\Finance\Resources\BankAccountResource;
use App\Models\Finance\BankAccount;
use App\Models\Finance\BankTransaction;
use App\Models\User;
use App\Services\BillingService;
use App\Services\Finance\BankService;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;

/**
 * Two-panel reconciliation (finance.bank/reconciliation, ui-strategy
 * two-panel matcher): unreconciled statement rows on the left, exact-
 * amount ±5-day journal suggestions per selected row on the right.
 */
class ReconcileBankAccount extends Page
{
    protected static string $resource = BankAccountResource::class;

    protected string $view = 'filament.finance.pages.reconcile-bank-account';

    public BankAccount $record;

    public ?string $selectedTransactionId = null;

    public static function canAccess(array $parameters = []): bool
    {
        $user = Auth::user();

        return $user instanceof User
            && $user->can('finance.bank.reconcile')
            && app(BillingService::class)->hasModule('finance.bank');
    }

    public function getTitle(): string
    {
        return "Reconcile — {$this->record->name}";
    }

    public function selectTransaction(string $transactionId): void
    {
        $this->selectedTransactionId = $transactionId;
    }

    public function reconcile(string $transactionId, string $journalLineId): void
    {
        $transaction = BankTransaction::query()->findOrFail($transactionId);
        app(BankService::class)->reconcile($transaction, $journalLineId);
        $this->selectedTransactionId = null;

        Notification::make()->success()->title('Matched')->send();
    }

    public function unreconcile(string $transactionId): void
    {
        $transaction = BankTransaction::query()->findOrFail($transactionId);
        app(BankService::class)->unreconcile($transaction);

        Notification::make()->success()->title('Unmatched')->send();
    }

    /** @return array<string, mixed> */
    protected function getViewData(): array
    {
        $open = $this->record->transactions()
            ->whereNull('reconciled_at')
            ->orderBy('transaction_date')
            ->get();

        $matched = $this->record->transactions()
            ->whereNotNull('reconciled_at')
            ->orderByDesc('reconciled_at')
            ->limit(10)
            ->get();

        $selected = $this->selectedTransactionId !== null
            ? BankTransaction::query()->find($this->selectedTransactionId)
            : null;

        return [
            'open' => $open,
            'matched' => $matched,
            'selected' => $selected,
            'suggestions' => $selected instanceof BankTransaction
                ? app(BankService::class)->suggestMatches($selected)
                : collect(),
        ];
    }
}
