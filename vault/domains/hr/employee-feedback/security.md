---
domain: hr
module: employee-feedback
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Employee Feedback — Security

Intended controls. See [[../../../security/authn-authz]], [[../../../security/encryption]], [[../../../security/tenancy-isolation]].

## Permissions

`hr.feedback.view-any` (HR) · `hr.feedback.give` · `hr.feedback.view-own` · `hr.feedback.one-on-one`

## Authorization

Every Filament artifact gates on `canAccess() = Auth::user()->can('hr.feedback.view-any') && BillingService::hasModule('hr.feedback')` per [[../../../architecture/patterns/custom-pages]] / filament-patterns #1. Custom pages state the gate explicitly.

## Visibility & Confidentiality

Enforced in query scopes (not just UI):

| Record | Who can read |
|---|---|
| Praise feedback | public-capable — team, plus recognition feed |
| Constructive feedback | private — sender + recipient (+ HR `view-any`); never on the feed |
| Coaching note | manager chain + HR only; invisible to the recipient's peers *(assumed forced by type)* |
| 1-on-1 notes | the two participants only (manager + employee); not HR by default |

Note the confidentiality split: **HR `view-any` can read feedback, but 1-on-1 agenda/notes are participant-only.** Coaching notes are visible up the manager chain but not to peers.

## Tenancy

All rows scoped by `company_id` via `BelongsToCompany` / CompanyScope. Cross-tenant reads must be impossible. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None. No columns require the `encrypted` cast in this module.
