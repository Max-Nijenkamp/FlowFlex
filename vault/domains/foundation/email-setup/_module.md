---
domain: foundation
module: email-setup
type: module
build-status: complete
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Email Setup

`foundation.email` — transactional email. Mailpit in local dev, Resend in production. Branded base mailable, queued delivery, and a signature-verified bounce webhook.

## Module-key

`foundation.email`

**Priority:** v1-core (M0)  
**Panel:** none (backend — mail + inbound webhook)  
**Permission prefix:** none  
**Tables:** none owned (writes `users.email_deliverable` / `users.email_verified_at` — scaffold columns)

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../laravel-scaffold/_module\|foundation.scaffold]] | `users.email_deliverable` column + mail config |
| Hard | [[../queue-workers/_module\|foundation.queues]] | mailables `ShouldQueue` → `notifications` queue |
| Soft | [[../../core/company-settings/_module\|core.company-settings]] | branding (name / logo / colour); degrades to platform default |

## Core Features

- `FlowFlexMailable` base class — injects company name / logo / primary colour resolved from `CompanyContext` — see [[./features/branded-mailable|Branded Mailable]]
- All mailables `ShouldQueue` → `notifications` queue (never sent synchronously)
- Mailpit captures mail in local dev; Resend SMTP in production
- Signature-verified Resend bounce webhook → hard bounce sets `email_deliverable = false` — see [[./features/bounce-webhook|Bounce Webhook]]

## Core (verified)

- `FlowFlexMailable` (`app/Support/Mail/FlowFlexMailable.php`) — injects company name/logo/primary colour; resolves branding from `CompanyContext` at render time; queued mails carry `company_id` for `WithCompanyContext`.
- All mailables `ShouldQueue` → `notifications` queue ([[../queue-workers/_module|queues]]).
- Local: Mailpit captures mail at `mailpit:1025` (UI 8025, internal-only — [[../docker-environment/_module|docker]]).
- Production: Resend SMTP.
- Hard bounce → `users.email_deliverable = false` via `HandleEmailBounceAction`.

> [!note] Webhook promoted from *(assumed)* to verified
> The flat spec marked signature verification as assumed. It is concrete: `routes/api.php` mounts `VerifyResendSignature` middleware + `throttle:60,1` on `POST /resend/webhook` → `ResendWebhookController`. See [[api|API]] and [[security|Security]].

## Data Model

`email_deliverable` (bool, default true) and `email_verified_at` on `users` — both created in the scaffold migration. No tables owned here.

## Test Checklist (verified)

- [ ] Tenant isolation: a queued mail carries `company_id` so `WithCompanyContext` renders it with the sending company's branding — no cross-company branding leak
- [ ] Module gating: n/a — `foundation.email` is always-on platform infra, not a billable/gateable module
- [x] Mailable renders company name + colour (`tests/Feature/MailBrandingTest.php`)
- [x] Mail queued on `notifications`, never sent sync
- [x] Bounce webhook valid signature flags `email_deliverable = false` (`tests/Feature/BounceWebhookTest.php`)
- [x] Bounce webhook invalid signature rejected, no change

## Build Manifest

```
app/Support/Mail/FlowFlexMailable.php
app/Http/Controllers/Webhooks/ResendWebhookController.php
app/Http/Middleware/VerifyResendSignature.php
app/Actions/HandleEmailBounceAction.php
routes/api.php (webhook route — signature-verified + throttled)
config/mail.php
tests/Feature/{MailBrandingTest,BounceWebhookTest}.php
```

## Notes split out

- [[api|API]] — Resend webhook contract
- [[security|Security]] — signature verification + throttle

## Related

- [[../../../infrastructure/mail]]
- [[../../../security/webhooks-signing]]
- [[../../../architecture/email]] · [[../queue-workers/_module|Queue Workers]]
