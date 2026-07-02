---
domain: lms
module: mentoring
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Mentoring — Security

## Permissions

| Permission | Grants |
|---|---|
| `lms.mentoring.participate` | Everyone — browse directory, request, run own mentorships + sessions |
| `lms.mentoring.view-pairings` | HR — see **pairings only**, never session notes |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('lms.mentoring.participate')
        && BillingService::hasModule('lms.mentoring');
}
```

## Session Privacy (headline control)

- `lms_mentorship_sessions.notes` + `action_items` are visible to the **mentor and mentee only**.
- Enforced at the **query layer** — sessions are scoped to participants, not merely hidden in the UI.
- `lms.mentoring.view-pairings` (HR) exposes the existence of a pairing (who mentors whom) but **never** session content. This is the `SessionPrivacyTest`.

## Tenant Isolation

All three tables carry `company_id` (indexed); `CompanyScope` applies. Mentor/mentee are same-company employees.

See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('lms.mentoring')`. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None specified. Session notes are protected by participant-scoping rather than encryption *(assumed)* — see [[unknowns]].
