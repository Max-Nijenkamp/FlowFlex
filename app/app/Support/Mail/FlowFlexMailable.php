<?php

declare(strict_types=1);

namespace App\Support\Mail;

use App\Models\User;
use App\Support\Services\CompanyContext;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Mail\Factory;
use Illuminate\Contracts\Mail\Mailer;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\SentMessage;
use Illuminate\Queue\SerializesModels;

/**
 * Base class for every FlowFlex mailable. Always queued on the notifications
 * queue; carries company_id so WithCompanyContext restores the tenant before
 * render; injects company branding (name/logo/colour) into every view.
 * Branding degrades to platform defaults until core.company-settings ships.
 */
abstract class FlowFlexMailable extends Mailable implements ShouldQueue
{
    use Queueable;
    use SerializesModels;

    public ?string $company_id = null;

    public function __construct()
    {
        $this->onQueue('notifications');
        $this->company_id = app(CompanyContext::class)->currentId();
    }

    /** @return array{name: string, logo_url: ?string, primary_color: string} */
    public function branding(): array
    {
        $hasContext = app(CompanyContext::class)->currentId() !== null;

        return [
            'name' => $hasContext ? app(CompanyContext::class)->current()->name : 'FlowFlex',
            'logo_url' => null, // core.company-settings supplies this later
            'primary_color' => '#38BDF8',
        ];
    }

    public function buildViewData(): array
    {
        return array_merge(parent::buildViewData(), ['branding' => $this->branding()]);
    }

    /**
     * Suppression list: never send to an address flagged undeliverable by the
     * bounce webhook. Runs at send time (inside the queued job, tenant context
     * already restored) so late flags still suppress queued mail.
     */
    /** @param  Mailer|Factory  $mailer */
    public function send($mailer): ?SentMessage
    {
        $this->to = array_values(array_filter(
            $this->to,
            fn (array $recipient): bool => ! User::query()
                ->where('email', $recipient['address'])
                ->where('email_deliverable', false)
                ->exists(),
        ));

        if ($this->to === []) {
            return null;
        }

        return parent::send($mailer);
    }
}
