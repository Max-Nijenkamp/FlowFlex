<?php

namespace App\Notifications\Hr;

use App\Models\Hr\Payslip;
use App\Notifications\FlowFlexNotification;
use Illuminate\Notifications\Messages\MailMessage;

class PayslipGeneratedNotification extends FlowFlexNotification
{
    public function __construct(public readonly Payslip $payslip) {}

    public function notificationType(): string
    {
        return 'hr.payroll.payslip';
    }

    public function toMail(object $notifiable): MailMessage
    {
        $periodStart = $this->payslip->period_start?->toDateString();
        $periodEnd = $this->payslip->period_end?->toDateString();

        return (new MailMessage)
            ->subject('Your Payslip is Ready')
            ->greeting('Hello!')
            ->line('Your payslip for the pay period is now available.')
            ->when($periodStart && $periodEnd, function (MailMessage $mail) use ($periodStart, $periodEnd) {
                return $mail->line("Period: {$periodStart} to {$periodEnd}.");
            })
            ->action('View Payslip', url('/hr/payslips/' . $this->payslip->id))
            ->salutation('The FlowFlex Platform');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'       => $this->notificationType(),
            'payslip_id' => $this->payslip->id,
            'pay_run_id' => $this->payslip->pay_run_id,
        ];
    }
}
