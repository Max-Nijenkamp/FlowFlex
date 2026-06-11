---
type: module
domain: Foundation
domain-key: foundation
panel: (scaffold)
module-key: foundation.email
status: complete
priority: v1-core
depends-on: [foundation.scaffold, foundation.queues]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [email]
tables: []
permission-prefix: ""
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Email Setup

Transactional email configuration: Mailpit in local dev, Resend in production. Mail queue config. Base mailable class with company branding injection.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/foundation/laravel-scaffold\|foundation.scaffold]] | mail config + users table |
| Hard | [[domains/foundation/queue-workers\|foundation.queues]] | all mail queues on `notifications` |

---

## Core Features

- `FlowFlexMailable` base class — injects company name, logo URL, primary color into every email
- Markdown Mailable template system with custom FlowFlex theme
- All mailables implement `ShouldQueue` — dispatched on `notifications` queue
- Local dev: Mailpit captures all outgoing mail (`localhost:8025`)
- Production: Resend SMTP (`smtp.resend.com:587`)
- Bounce/complaint webhook handler: `POST /api/resend/webhook` (signature-verified *(assumed: Resend svix signature)*)
- Hard bounce detection: sets `users.email_deliverable = false`, alerts company admin
- Email queue retry: 3 attempts, 10s/60s/5min backoff on `notifications` queue

---

## Data Model

| Column | On table | Purpose |
|---|---|---|
| `email_deliverable` | `users` | `false` after hard bounce — stops sending to this address |
| `email_verified_at` | `users` | Standard Laravel email verification timestamp |

(Columns created in the scaffold migration.)

## DTOs

None — webhook payload handled as raw verified array in the controller, dispatched to an Action.

## Services & Actions

- `HandleEmailBounceAction::run(string $email, string $bounceType): void` — hard bounce → flag user, notify admin; soft bounce → log only *(assumed)*
- `FlowFlexMailable` (abstract): resolves branding from `CompanyContext` at render time; queued mails carry `company_id` for `WithCompanyContext`

## Filament / Permissions

None — infrastructure. Mail templates inventory: [[architecture/email]].

---

## Test Checklist

- [ ] Mailable extending `FlowFlexMailable` renders company name + color in the template
- [ ] Mail is queued on `notifications`, never sent synchronously (`Mail::assertNothingSent`)
- [ ] Bounce webhook with valid signature flags `email_deliverable = false`
- [ ] Bounce webhook with invalid signature → 400, no change
- [ ] Sending to a user with `email_deliverable = false` is skipped + logged
- [ ] Tenant isolation: queued mail rendered under the right company branding via `WithCompanyContext`

---

## Build Manifest

```
app/Support/Mail/FlowFlexMailable.php
resources/views/vendor/mail/ (published + FlowFlex theme)
app/Http/Controllers/Webhooks/ResendWebhookController.php
app/Actions/Foundation/HandleEmailBounceAction.php
routes/api.php (webhook route, CSRF-exempt, signature-verified)
config/mail.php
tests/Feature/Foundation/{MailBrandingTest,BounceWebhookTest}.php
```

---


**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Rate limiter** (medium): Add a throttle middleware to the webhook route (e.g. `throttle:resend-webhook` with a per-IP/per-source limit) in routes/api.php and document it in the Build Manifest alongside the signature-verification middleware.
- **Webhook verification** (HIGH): Promote signature verification from *(assumed)* to a concrete requirement: specify the Svix/Resend signature header, the secret env var (e.g. RESEND_WEBHOOK_SECRET), and a dedicated verification middleware on the route that rejects unsigned/invalid requests with 403 before reaching the controller.…

---

## Related

- [[architecture/email]] — full transactional email inventory and template structure
- [[domains/foundation/queue-workers]]
- [[architecture/queue-jobs]]
