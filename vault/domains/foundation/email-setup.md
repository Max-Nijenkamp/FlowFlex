---
type: module
domain: Foundation
panel: (scaffold)
module-key: foundation.email
status: planned
color: "#4ADE80"
---

# Email Setup

Transactional email configuration: Mailpit in local dev, Resend in production. Mail queue config. Base mailable class with company branding injection.

---

## Core Features

- `FlowFlexMailable` base class — injects company name, logo URL, primary color into every email
- Markdown Mailable template system with custom FlowFlex theme
- All mailables implement `ShouldQueue` — dispatched on `notifications` queue
- Local dev: Mailpit captures all outgoing mail (`localhost:8025`)
- Production: Resend SMTP (`smtp.resend.com:587`)
- Bounce/complaint webhook handler: `POST /api/resend/webhook`
- Hard bounce detection: sets `users.email_deliverable = false`, alerts company admin
- Email queue retry: 3 attempts, 10s/60s/5min backoff on `notifications` queue

---

## Data Model

| Column | On table | Purpose |
|---|---|---|
| `email_deliverable` | `users` | `false` after hard bounce — stops sending to this address |
| `email_verified_at` | `users` | Standard Laravel email verification timestamp |

---

## Filament

No Filament resources — infrastructure only.

See [[architecture/email]] for the full transactional email inventory and template structure.

---

## Related

- [[domains/foundation/laravel-scaffold]]
- [[domains/foundation/queue-workers]]
- [[architecture/email]]
- [[architecture/queue-jobs]]
