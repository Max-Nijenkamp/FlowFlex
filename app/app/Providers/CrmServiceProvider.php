<?php

declare(strict_types=1);

namespace App\Providers;

use App\Contracts\CRM\ContactServiceInterface;
use App\Contracts\CRM\DealServiceInterface;
use App\Contracts\CRM\QuoteServiceInterface;
use App\Contracts\CRM\SequenceServiceInterface;
use App\Services\CRM\ContactService;
use App\Services\CRM\DealService;
use App\Services\CRM\QuoteService;
use App\Services\CRM\SequenceService;
use App\Support\Privacy\PersonalDataRegistry;
use Illuminate\Support\ServiceProvider;

class CrmServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(ContactServiceInterface::class, ContactService::class);
        $this->app->singleton(DealServiceInterface::class, DealService::class);
        $this->app->singleton(QuoteServiceInterface::class, QuoteService::class);
        $this->app->singleton(SequenceServiceInterface::class, SequenceService::class);
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

        app(PersonalDataRegistry::class)->register('crm.email', [
            'crm_emails' => [
                'email_column' => null,
                'fields' => ['subject', 'body'],
                'erasure' => 'delete', // personal correspondence purged on DSAR erasure *(assumed)*
            ],
        ]);
    }
}
