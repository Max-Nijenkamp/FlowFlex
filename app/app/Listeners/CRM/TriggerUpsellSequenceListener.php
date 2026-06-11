<?php

declare(strict_types=1);

namespace App\Listeners\CRM;

use App\Contracts\CRM\SequenceServiceInterface;
use App\Events\Finance\InvoicePaid;
use App\Models\CRM\Account;
use App\Models\CRM\Contact;
use App\Support\Services\CompanyContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/** InvoicePaid → enrol the account's primary contact in invoice-paid (upsell) sequences. */
class TriggerUpsellSequenceListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'domain-events';

    public int $tries = 3;

    public int $backoff = 30;

    public function handle(InvoicePaid $event): void
    {
        if ($event->crm_account_id === null) {
            return;
        }

        app(CompanyContext::class)->setById($event->company_id);

        $account = Account::query()->find($event->crm_account_id);
        $contact = $account !== null
            ? Contact::query()->where('account_id', $account->id)->first()
            : null;

        if ($contact !== null) {
            app(SequenceServiceInterface::class)->enrolByTrigger('invoice-paid', $contact->id);
        }
    }
}
