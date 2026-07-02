---
domain: marketing
module: email-sequences
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Email Sequences — Security

Parent: [[_module]]

Automated outbound email — no public surfaces of its own (unsubscribe is handled by the shared campaigns endpoint).

## Permissions

`marketing.sequences.view-any` · `marketing.sequences.create` · `marketing.sequences.update` · `marketing.sequences.enrol`. Resources gate on `canAccess()` ([[../../../architecture/patterns/policy]]).

## Suppression & consent

- Enrolment and every advancement re-check `mkt_unsubscribes` (owned by [[../campaigns/_module|campaigns]]) — a suppressed contact is never enrolled or advanced.
- Marketing consent to enter a nurture flow is assumed to be established upstream (form consent checkbox / segment cleanliness). A first-class consent gate is a known gap — see [[unknowns]] and [[../_opportunities]].

## Listener safety

`EnrolFromFormListener` runs `ShouldQueue` + `WithCompanyContext`, writing only its own enrolment rows under the event's `company_id`. It never writes forms or CRM tables ([[../../../security/data-ownership]]).

## Tenant scoping

All three tables carry `company_id`; the advancement sweep is company-scoped via `CompanyContext` on the queued command ([[../../../security/tenancy-isolation]]).

## Related

- [[_module]] · [[api]] · [[../../../security/authn-authz]] · [[../../../architecture/event-bus]]
