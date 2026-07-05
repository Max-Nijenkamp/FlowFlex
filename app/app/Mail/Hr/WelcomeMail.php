<?php

declare(strict_types=1);

namespace App\Mail\Hr;

use App\Models\Hr\Employee;
use App\Support\Mail\FlowFlexMailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;

/** First-day welcome (hr.onboarding/welcome-email). */
class WelcomeMail extends FlowFlexMailable
{
    public function __construct(public readonly string $employeeId)
    {
        parent::__construct();
    }

    public function envelope(): Envelope
    {
        return new Envelope(subject: 'Welcome aboard 🎉');
    }

    public function content(): Content
    {
        $employee = Employee::query()->findOrFail($this->employeeId);

        return new Content(markdown: 'mail.hr.welcome', with: [
            'firstName' => $employee->first_name,
            'startDate' => $employee->hire_date->format('d M Y'),
            'jobTitle' => $employee->job_title,
        ]);
    }
}
