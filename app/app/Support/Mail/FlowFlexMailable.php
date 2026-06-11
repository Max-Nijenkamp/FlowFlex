<?php

declare(strict_types=1);

namespace App\Support\Mail;

use App\Support\Services\CompanyContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

/**
 * Base mailable for all FlowFlex transactional email.
 * - Always queued on the `notifications` queue.
 * - Injects company branding (name, primary color, logo) resolved from CompanyContext
 *   at render time, so queued mail renders under the correct tenant (via WithCompanyContext).
 */
abstract class FlowFlexMailable extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public function __construct()
    {
        $this->onQueue('notifications');
    }

    /**
     * Branding view data merged into every FlowFlex email.
     *
     * @return array<string, string|null>
     */
    protected function branding(): array
    {
        $context = app(CompanyContext::class);

        return [
            'companyName' => $context->has() ? $context->current()->name : (string) config('app.name'),
            'primaryColor' => '#38BDF8',
        ];
    }
}
