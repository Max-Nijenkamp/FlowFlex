<?php

namespace App\Notifications\Hr;

use App\Models\Hr\OnboardingFlow;
use App\Notifications\FlowFlexNotification;
use Illuminate\Notifications\Messages\MailMessage;

class OnboardingStartedNotification extends FlowFlexNotification
{
    public function __construct(public readonly OnboardingFlow $flow) {}

    public function notificationType(): string
    {
        return 'hr.onboarding.started';
    }

    public function toMail(object $notifiable): MailMessage
    {
        $templateName = $this->flow->template?->name ?? 'Onboarding';

        return (new MailMessage)
            ->subject('Your Onboarding Has Started')
            ->greeting('Welcome!')
            ->line("Your onboarding process has officially started.")
            ->line("You are enrolled in the \"{$templateName}\" onboarding program.")
            ->line('Please complete the tasks assigned to you as part of your onboarding.')
            ->action('View Onboarding', url('/hr/onboarding/' . $this->flow->id))
            ->salutation('The FlowFlex Platform');
    }

    public function toArray(object $notifiable): array
    {
        return [
            'type'          => $this->notificationType(),
            'flow_id'       => $this->flow->id,
            'template_name' => $this->flow->template?->name,
        ];
    }
}
