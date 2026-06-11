<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\HR\EmployeeServiceInterface;
use App\Contracts\HR\LeaveServiceInterface;
use App\Contracts\HR\OnboardingServiceInterface;
use App\Contracts\HR\PayrollServiceInterface;
use App\Services\HR\EmployeeService;
use App\Services\HR\LeaveService;
use App\Services\HR\OnboardingService;
use App\Services\HR\PayrollService;
use App\Support\Privacy\PersonalDataRegistry;
use Illuminate\Support\ServiceProvider;

class HRServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(EmployeeServiceInterface::class, EmployeeService::class);
        $this->app->singleton(LeaveServiceInterface::class, LeaveService::class);
        $this->app->singleton(OnboardingServiceInterface::class, OnboardingService::class);
        $this->app->singleton(PayrollServiceInterface::class, PayrollService::class);
    }

    public function boot(): void
    {
        // PII registry — drives DSAR export + erasure cascade (core.privacy).
        app(PersonalDataRegistry::class)->register('hr.profiles', [
            'hr_employees' => [
                'email_column' => 'email',
                'fields' => ['first_name', 'last_name', 'email', 'phone', 'personal_email', 'national_id'],
                'erasure' => 'anonymise',
            ],
            'hr_emergency_contacts' => [
                'email_column' => 'email',
                'fields' => ['name', 'relationship', 'phone', 'email'],
                'erasure' => 'delete',
            ],
        ]);
    }
}
