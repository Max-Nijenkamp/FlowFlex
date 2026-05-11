<?php

declare(strict_types=1);

namespace App\Providers\HR;

use App\Contracts\HR\EmployeeServiceInterface;
use App\Contracts\HR\LeaveServiceInterface;
use App\Contracts\HR\OnboardingServiceInterface;
use App\Contracts\HR\PayrollServiceInterface;
use App\Services\HR\EmployeeService;
use App\Services\HR\LeaveService;
use App\Services\HR\OnboardingService;
use App\Services\HR\PayrollService;
use Illuminate\Support\ServiceProvider;

class HrServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EmployeeServiceInterface::class, EmployeeService::class);
        $this->app->bind(LeaveServiceInterface::class, LeaveService::class);
        $this->app->bind(OnboardingServiceInterface::class, OnboardingService::class);
        $this->app->bind(PayrollServiceInterface::class, PayrollService::class);
    }

    public function boot(): void
    {
        //
    }
}
