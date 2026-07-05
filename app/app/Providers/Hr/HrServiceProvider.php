<?php

declare(strict_types=1);

namespace App\Providers\Hr;

use App\Contracts\Hr\EmployeeServiceInterface;
use App\Services\Hr\EmployeeService;
use Illuminate\Support\ServiceProvider;

class HrServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->bind(EmployeeServiceInterface::class, EmployeeService::class);
    }
}
