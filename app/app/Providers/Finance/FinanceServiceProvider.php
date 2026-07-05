<?php

declare(strict_types=1);

namespace App\Providers\Finance;

use App\Contracts\Finance\InvoiceServiceInterface;
use App\Contracts\Finance\LedgerServiceInterface;
use App\Services\Finance\InvoiceService;
use App\Services\Finance\LedgerService;
use Illuminate\Support\ServiceProvider;

class FinanceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(LedgerServiceInterface::class, LedgerService::class);
        $this->app->bind(InvoiceServiceInterface::class, InvoiceService::class);
    }
}
