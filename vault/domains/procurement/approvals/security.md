---
domain: procurement
module: approvals
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-02
---

# Approvals — Security

## Access contract

Every artifact: `canAccess() = Auth::user()->can('procurement.approvals.view-any') && BillingService::hasModule('procurement.approvals')` — [[../../../architecture/filament-patterns]] #1.

## Permissions

| Permission | Grants |
|---|---|
| `procurement.approvals.view-any` | see rules, delegations, pending queue |
| `procurement.approvals.manage-rules` | create/edit/deactivate matrix rules |
| `procurement.approvals.delegate-own` | create delegations where delegator = self |

Module-scoped: only assignable while the module is active — [[../../core/rbac/_module]].

## Data ownership

Writes **only** `proc_approval_rules`, `proc_approval_delegations`. It **reads** RBAC roles + (soft) HR departments, and **fires** notifications; it never writes another domain's tables — [[../../../security/data-ownership]]. Approval actions are written by each consumer into that consumer's own tables.

## Tenancy & audit

- All rows carry `company_id`, enforced by CompanyScope — [[../../../security/tenancy-isolation]].
- Matrix rule changes audited (activity log): a changed threshold alters who can authorise spend.
- Delegation `delegator_id` is forced to the acting user unless caller holds `manage-rules`.

## Related

- [[_module]] · [[../../../security/data-ownership]] · [[../../../architecture/filament-patterns]]
