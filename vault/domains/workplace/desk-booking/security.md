---
domain: workplace
module: desk-booking
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Desk Booking — Security

## Permissions

| Permission | Grants |
|---|---|
| `workplace.desks.view-any` | View desks + floor map + team view |
| `workplace.desks.book` | Book a desk, check in / cancel own booking (all users) |
| `workplace.desks.manage` | CRUD desks, edit any booking |

**Verb / transition → permission** (per the frozen [[../../../_meta/spec-template]] verb-per-command rule):

| Command / transition | Permission |
|---|---|
| Book a desk (create `booked`) | `workplace.desks.book` |
| Check-in (stamp `checked_in_at`) | `workplace.desks.book` (own booking) |
| Cancel own booking | `workplace.desks.book` |
| No-show release (`booked → released`) | system command — no user permission (`ReleaseDeskNoShowsCommand`) |
| Desk CRUD / edit any booking | `workplace.desks.manage` |

See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('workplace.desks.view-any')
        && BillingService::hasModule('workplace.desks');
}
```

## Rate Limiting

- The click-to-book hotspot action on `DeskBookingPage` is a capacity/slot-decrement panel action, so it names the `panel-action` rate limiter per the Spatial blueprint ([[../../../architecture/patterns/page-blueprints#Spatial / Floor Map]]) and [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]. Prevents rapid book/cancel churn on shared desks.

## Tenant Isolation

- `wp_desks` + `wp_desk_bookings` carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains all queries.
- Dual-uniqueness checks and the floor map are scoped to the acting company.

See [[../../../security/tenancy-isolation]].

## Module Gating

`BillingService::hasModule('workplace.desks')` gates panel access. See [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None. Bookings reference internal employees; no external PII.

## Team-view Privacy

The team view exposes where colleagues sit on a given day. This is intra-company only (`company_id`-scoped) and shows same-day bookings; whether an employee can opt out of appearing is an open question — see [[unknowns]].
