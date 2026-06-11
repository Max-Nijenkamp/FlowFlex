<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\CRM\ContactServiceInterface;
use App\Contracts\CRM\DealServiceInterface;
use App\Services\CRM\ContactService;
use App\Services\CRM\DealService;
use App\Support\Privacy\PersonalDataRegistry;
use Illuminate\Support\ServiceProvider;

class CrmServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ContactServiceInterface::class, ContactService::class);
        $this->app->singleton(DealServiceInterface::class, DealService::class);
    }

    public function boot(): void
    {
        app(PersonalDataRegistry::class)->register('crm.contacts', [
            'crm_contacts' => [
                'email_column' => 'email',
                'fields' => ['first_name', 'last_name', 'email', 'phone'],
                'erasure' => 'anonymise',
            ],
        ]);
    }
}
