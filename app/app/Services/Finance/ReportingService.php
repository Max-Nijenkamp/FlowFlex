<?php

declare(strict_types=1);

namespace App\Services\Finance;

use App\Contracts\Finance\ReportingServiceInterface;
use App\Exceptions\Finance\BalanceSheetImbalanceException;
use App\Models\Finance\Account;
use App\Models\Finance\JournalLine;
use Carbon\CarbonImmutable;

class ReportingService implements ReportingServiceInterface
{
    /**
     * @return array{revenue_cents: int, expense_cents: int, net_profit_cents: int, by_account: array<string, int>}
     */
    public function profitLoss(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $byAccount = [];
        $revenue = 0;
        $expense = 0;

        foreach (['revenue', 'expense'] as $type) {
            foreach (Account::query()->where('type', $type)->get() as $account) {
                $net = $this->netFor($account, $from, $to);
                if ($net === 0) {
                    continue;
                }
                // Revenue accounts are credit-normal: flip sign for display.
                $display = $type === 'revenue' ? -$net : $net;
                $byAccount[$account->code] = $display;
                if ($type === 'revenue') {
                    $revenue += $display;
                } else {
                    $expense += $display;
                }
            }
        }

        return [
            'revenue_cents' => $revenue,
            'expense_cents' => $expense,
            'net_profit_cents' => $revenue - $expense,
            'by_account' => $byAccount,
        ];
    }

    /**
     * @return array{assets_cents: int, liabilities_cents: int, equity_cents: int}
     */
    public function balanceSheet(CarbonImmutable $asOf): array
    {
        $assets = 0;
        $liabilities = 0;
        $equity = 0;

        foreach (Account::query()->whereIn('type', ['asset', 'liability', 'equity'])->get() as $account) {
            $net = $this->netFor($account, null, $asOf);
            match ($account->type) {
                'asset' => $assets += $net,
                'liability' => $liabilities += -$net,
                default => $equity += -$net,
            };
        }

        // Retained earnings: lifetime P&L folds into equity.
        $pl = $this->profitLoss(CarbonImmutable::parse('1970-01-01'), $asOf);
        $equity += $pl['net_profit_cents'];

        if ($assets !== $liabilities + $equity) {
            report(new BalanceSheetImbalanceException(
                "Assets {$assets} != liabilities {$liabilities} + equity {$equity}.",
            ));

            throw new BalanceSheetImbalanceException;
        }

        return [
            'assets_cents' => $assets,
            'liabilities_cents' => $liabilities,
            'equity_cents' => $equity,
        ];
    }

    /**
     * Indirect-method cash flow: net profit adjusted by working-capital deltas.
     * v1: net profit + cash account delta breakdown.
     *
     * @return array{net_profit_cents: int, cash_delta_cents: int}
     */
    public function cashFlow(CarbonImmutable $from, CarbonImmutable $to): array
    {
        $pl = $this->profitLoss($from, $to);

        $cash = Account::query()->where('code', '1000')->first();
        $delta = $cash !== null ? $this->netFor($cash, $from, $to) : 0;

        return [
            'net_profit_cents' => $pl['net_profit_cents'],
            'cash_delta_cents' => $delta,
        ];
    }

    /** Net debit − credit for an account within a window (null from = inception). */
    private function netFor(Account $account, ?CarbonImmutable $from, CarbonImmutable $to): int
    {
        return (int) JournalLine::query()
            ->where('account_id', $account->id)
            ->whereHas('entry', fn ($q) => $q
                ->when($from !== null, fn ($qq) => $qq->whereDate('entry_date', '>=', $from->toDateString()))
                ->whereDate('entry_date', '<=', $to->toDateString()))
            ->selectRaw('COALESCE(SUM(debit_cents - credit_cents), 0) as net')
            ->value('net');
    }
}
