---
type: architecture
category: infra
pattern-key: email
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Transactional Email

All transactional emails in FlowFlex. Mail provider, template system, queue configuration, per-domain email inventory, and testing.

---

## Provider

| Environment | Provider | Config |
|---|---|---|
| Local dev | Mailpit | `MAIL_HOST=mailpit, MAIL_PORT=1025` — UI at `localhost:8025` |
| Staging | Resend (test mode) or Mailpit | Same SMTP config as local |
| Production | Resend | `MAIL_MAILER=smtp, MAIL_HOST=smtp.resend.com` |

**Why Resend**: developer-friendly, reliable EU deliverability, webhook-based bounce/complaint handling, low cost for transactional volume.

Alternative: Postmark (higher deliverability SLA, more expensive).

Laravel config (`config/mail.php`):
```php
'default' => env('MAIL_MAILER', 'smtp'),
'from' => [
    'address' => env('MAIL_FROM_ADDRESS', 'no-reply@flowflex.io'),
    'name' => env('MAIL_FROM_NAME', 'FlowFlex'),
],
```

---

## Template System

All emails use Laravel's Markdown Mailable with a custom FlowFlex theme:

```
resources/
└── views/
    └── emails/
        ├── layouts/
        │   └── base.blade.php       ← base layout with FlowFlex logo + footer
        ├── auth/
        │   ├── welcome.blade.php
        │   ├── reset-password.blade.php
        │   └── verify-email.blade.php
        ├── hr/
        │   ├── leave-approved.blade.php
        │   ├── leave-rejected.blade.php
        │   ├── onboarding-started.blade.php
        │   └── payslip.blade.php
        ├── finance/
        │   ├── invoice.blade.php
        │   ├── invoice-reminder.blade.php
        │   └── payment-received.blade.php
        └── core/
            ├── invitation.blade.php
            └── module-activated.blade.php
```

Custom theme registered in `AppServiceProvider`:

```php
Blade::componentNamespace('FlowFlex\\Mail\\Components', 'mail');
```

---

## Mailable Pattern

All mailables extend a base class that injects company branding:

```php
namespace App\Mail\HR;

use App\Mail\FlowFlexMailable;
use App\Models\HR\LeaveRequest;

class LeaveApprovedMail extends FlowFlexMailable
{
    public function __construct(
        public readonly LeaveRequest $request,
    ) {}

    public function content(): Content
    {
        return new Content(
            markdown: 'emails.hr.leave-approved',
            with: [
                'employeeName' => $this->request->employee->full_name,
                'startDate' => $this->request->start_date->format('d M Y'),
                'endDate' => $this->request->end_date->format('d M Y'),
                'daysCount' => $this->request->days_requested,
            ],
        );
    }
}
```

`FlowFlexMailable` injects `$company->name` and `$company->logo_url` into every mail for branding.

---

## Queue Configuration

All mailables implement `ShouldQueue` and are dispatched on the `notifications` queue:

```php
Mail::to($employee->email)
    ->queue(new LeaveApprovedMail($request));

// Or explicitly:
Mail::to($employee->email)
    ->onQueue('notifications')
    ->send(new LeaveApprovedMail($request));
```

Never `Mail::send()` (synchronous) in web requests — always queue. Prevents slow response times when the mail server is slow.

---

## Email Inventory Per Domain

### Auth / Core

| Email | Trigger | Recipient |
|---|---|---|
| Welcome (new company) | Company created in `/admin` | Company owner |
| Email verification | Registration | New user |
| Password reset | Forgot password form | User |
| Team invitation | Owner invites team member | Invited email |
| 2FA backup codes | 2FA enabled | User |
| New login from unknown device | Login from new IP/device | User |
| Module activated | Owner activates module | Owner |
| Invoice (FlowFlex billing) | Monthly billing run | Company owner |
| Payment failed | Stripe webhook | Company owner |
| Trial expiring in 3 days | Scheduled job | Company owner |

### HR

| Email | Trigger | Recipient |
|---|---|---|
| Leave request submitted | Employee submits leave | Manager |
| Leave approved | Manager approves | Employee |
| Leave rejected | Manager rejects | Employee |
| Onboarding started | Employee hired | Employee (welcome + task list) |
| Payslip available | Payroll run approved | Employee |
| Performance review due | Review cycle starts | Employee + Manager |
| Document signature request | Contract sent | Employee |

### Finance

| Email | Trigger | Recipient |
|---|---|---|
| Invoice sent | Admin sends invoice | Customer email on invoice |
| Invoice reminder (overdue) | Scheduled dunning job | Customer |
| Payment received | Payment recorded | Customer (receipt) |
| Expense approved | Finance approves | Employee |
| Expense rejected | Finance rejects | Employee |

### CRM

| Email | Trigger | Recipient |
|---|---|---|
| Quote sent | Rep sends quote | Contact email |
| Quote accepted / declined | Contact responds | Rep owner |
| Deal assigned | Deal ownership changed | New owner |
| Activity reminder | Due date approaching | Rep |

### Communications / Support

| Email | Trigger | Recipient |
|---|---|---|
| Ticket opened (confirmation) | Ticket created via email/form | Customer |
| Ticket assigned | Agent assigned | Agent |
| Ticket resolved | Status set to resolved | Customer |
| New message in inbox | Channel message received | Assigned agent |

---

## Testing Emails

```php
use Illuminate\Support\Facades\Mail;

it('sends leave approved email to employee', function () {
    Mail::fake();

    $request = LeaveRequest::factory()->approved()->for($company)->create();

    ApproveLeaveRequest::run($request, approvedBy: $manager);

    Mail::assertQueued(LeaveApprovedMail::class, function ($mail) use ($request) {
        return $mail->request->id === $request->id
            && $mail->hasTo($request->employee->email);
    });
});
```

Never `Mail::send()` in tests — always `Mail::fake()` + `assertQueued()`.

---

## Unsubscribe / Preference Management

Each email type has a corresponding entry in `notification_preferences` (see [[domains/core/notifications]]). Users can opt out of non-critical email types via their notification preferences page. Critical emails (password reset, billing, security) are always sent regardless of preferences.

---

## Bounce / Complaint Handling

Configure Resend webhook at `POST /api/resend/webhook` to receive bounce and complaint events:

```php
class ResendWebhookController extends Controller
{
    public function handle(Request $request): Response
    {
        $event = $request->json('type');

        match ($event) {
            'email.bounced' => HandleEmailBounce::run($request->json('data')),
            'email.complained' => HandleEmailComplaint::run($request->json('data')),
            default => null,
        };

        return response('', 200);
    }
}
```

On hard bounce: mark `users.email_deliverable = false`, stop sending to that address, alert the company admin.
