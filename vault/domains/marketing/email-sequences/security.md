---
domain: marketing
module: email-sequences
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Email Sequences — Security

Parent: [[_module]]

Automated outbound email — no public surfaces of its own (unsubscribe is handled by the shared campaigns endpoint).

## Permissions

| Permission | Grants |
|---|---|
| `marketing.sequences.view-any` | Sequence + enrolment list |
| `marketing.sequences.create` | Create a sequence |
| `marketing.sequences.update` | Edit a sequence / step; pause / resume (`is_active` toggle) |
| `marketing.sequences.delete` | Soft-delete a sequence |
| `marketing.sequences.enrol` | Manual enrol / unenrol a contact |

Pause / resume are `update` (they flip `is_active`); trigger-driven and scheduled enrolments run system-side under `CompanyContext`, not a user permission. Seeded in `PermissionSeeder`. Resources gate on `canAccess()` ([[../../../architecture/patterns/policy]]).

**Verb-per-command check:** enrolment-state flips exit via `exit()` (unsubscribe / customer / manual) — the user-triggered path (manual unenrol) uses `marketing.sequences.enrol`; automated exits are system-side. Pause/resume map to `.update`. All covered.

## Rate limiting

| Action | Category | Limiter |
|---|---|---|
| Manual enrol / unenrol (panel action) | initiates a comms flow | `panel-action` ([[../../../architecture/security]]) |

Step sends run on the `notifications` queue and are throttled at the queue/mail-transport layer ([[../../../architecture/queue-jobs]], [[../../../architecture/email]]); the scheduled advancement command is not a user-facing action. The `panel-action` limiter guards the manual enrol trigger.

## Suppression & consent

- Enrolment and every advancement re-check `mkt_unsubscribes` (owned by [[../campaigns/_module|campaigns]]) — a suppressed contact is never enrolled or advanced.
- Marketing consent to enter a nurture flow is assumed to be established upstream (form consent checkbox / segment cleanliness). A first-class consent gate is a known gap — see [[unknowns]] and [[../_opportunities]].

## Listener safety

`EnrolFromFormListener` runs `ShouldQueue` + `WithCompanyContext`, writing only its own enrolment rows under the event's `company_id`. It never writes forms or CRM tables ([[../../../security/data-ownership]]).

## Tenant scoping

All three tables carry `company_id`; the advancement sweep is company-scoped via `CompanyContext` on the queued command ([[../../../security/tenancy-isolation]]).

## Related

- [[_module]] · [[api]] · [[../../../security/authn-authz]] · [[../../../architecture/event-bus]]
