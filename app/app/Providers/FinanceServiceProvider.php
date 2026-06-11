<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\Finance\ApServiceInterface;
use App\Contracts\Finance\ArServiceInterface;
use App\Contracts\Finance\BankServiceInterface;
use App\Contracts\Finance\BudgetServiceInterface;
use App\Contracts\Finance\ExpenseServiceInterface;
use App\Contracts\Finance\InvoiceServiceInterface;
use App\Contracts\Finance\LedgerServiceInterface;
use App\Contracts\Finance\ReportingServiceInterface;
use App\Services\Finance\ApService;
use App\Services\Finance\ArService;
use App\Services\Finance\BankService;
use App\Services\Finance\BudgetService;
use App\Services\Finance\ExpenseService;
use App\Services\Finance\InvoiceService;
use App\Services\Finance\LedgerService;
use App\Services\Finance\ReportingService;
use Illuminate\Support\ServiceProvider;

class FinanceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(LedgerServiceInterface::class, LedgerService::class);
        $this->app->singleton(InvoiceServiceInterface::class, InvoiceService::class);
        $this->app->singleton(ExpenseServiceInterface::class, ExpenseService::class);
        $this->app->singleton(BankServiceInterface::class, BankService::class);
        $this->app->singleton(ArServiceInterface::class, ArService::class);
        $this->app->singleton(ApServiceInterface::class, ApService::class);
        $this->app->singleton(BudgetServiceInterface::class, BudgetService::class);
        $this->app->singleton(ReportingServiceInterface::class, ReportingService::class);
    }
}
