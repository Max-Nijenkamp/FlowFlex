<?php

declare(strict_types=1);

namespace App\Contracts\Finance;

use Carbon\CarbonImmutable;

interface ReportingServiceInterface
{
    /** @return array{revenue_cents: int, expense_cents: int, net_profit_cents: int, by_account: array<string, int>} */
    public function profitLoss(CarbonImmutable $from, CarbonImmutable $to): array;

    /** @return array{assets_cents: int, liabilities_cents: int, equity_cents: int} */
    public function balanceSheet(CarbonImmutable $asOf): array;

    /** @return array{net_profit_cents: int, cash_delta_cents: int} */
    public function cashFlow(CarbonImmutable $from, CarbonImmutable $to): array;
}
