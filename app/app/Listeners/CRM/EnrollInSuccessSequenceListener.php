<?php

declare(strict_types=1);

namespace App\Listeners\CRM;

use App\Contracts\CRM\SequenceServiceInterface;
use App\Events\CRM\DealWon;
use App\Support\Services\CompanyContext;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

/** DealWon → enrol the deal contact in deal-won sequences (event-bus contract). */
class EnrollInSuccessSequenceListener implements ShouldQueue
{
    use InteractsWithQueue;

    public string $queue = 'domain-events';

    public int $tries = 3;

    public int $backoff = 30;

    public function handle(DealWon $event): void
    {
        if ($event->contact_id === null) {
            return;
        }

        app(CompanyContext::class)->setById($event->company_id);

        app(SequenceServiceInterface::class)->enrolByTrigger('deal-won', $event->contact_id, $event->deal_id);
    }
}
