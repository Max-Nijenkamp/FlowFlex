---
domain: crm
module: sales-sequences
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Sales Sequences — Security

## Permissions

| Permission | Grants |
|---|---|
| crm.sequences.view-any | List/view sequences and enrolments |
| crm.sequences.create | Create sequences |
| crm.sequences.update | Edit sequences and steps |
| crm.sequences.enrol | Enrol contacts/deals |
| crm.sequences.pause | Pause an active enrolment |
| crm.sequences.resume | Resume a paused enrolment |
| crm.sequences.unenrol | Remove a contact/deal from a sequence |
| crm.sequences.manage-team | Manage team (non-personal) sequences |

**Rate limiting:** manual enrol (`crm.sequences.enrol`) and the pause/resume/unenrol lifecycle actions are panel actions behind the named `panel-action` rate limiter; outbound step sends (`SequenceStepMail`, comms) run on the throttled `notifications` mail queue. Per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]].

## Access Contract

```php
public static function canAccess(): bool
{
    return auth()->user()?->can('crm.sequences.view-any')
        && hasModule('crm.sequences');
}
```

## Tenant Isolation

All three tables carry `company_id` and are scoped by `CompanyScope`. The advance query filters `(company_id, status, next_step_at)`. Consuming listeners (`DealWon`, `InvoicePaid`) run with `WithCompanyContext` so queued advancement resolves the correct tenant — see [[../../../security/tenancy-isolation]].

## Module Gating

Gated on `crm.sequences` via `hasModule()` in `canAccess()`. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None.

## Notes

- Rich-text sanitize (medium): HTMLPurifier runs on sequence email-step template HTML on save, consistent with crm.email body purification — prevents stored XSS in outbound step content. See [[../../../security/threat-model]].
