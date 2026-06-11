---
type: module
domain: Procurement
domain-key: procurement
panel: operations
module-key: procurement.approvals
status: planned
priority: p3
depends-on: [core.billing, core.rbac, core.notifications]
soft-depends: [procurement.requisitions, procurement.purchase-orders]
fires-events: []
consumes-events: []
patterns: []
tables: [proc_approval_rules, proc_approval_delegations]
permission-prefix: procurement.approvals
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Procurement Approvals

Configurable approval workflows and authority matrix for requisitions and POs based on amount thresholds, category, and department. Builds first in Procurement — requisitions route through it.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, escalation notifications |
| Soft | requisitions / procurement POs | the consumers of the matrix |

---

## Core Features

- Approval matrix: who approves what, by amount threshold and category
- Multi-level chains: e.g. <€1k manager, €1k–10k director, >€10k CFO
- Delegation: approver can delegate while away (date-ranged)
- Approval routing: `ApprovalMatrix::chainFor(type, amount, category)` — the API consumers call
- Approval audit trail (in consumer approval tables; matrix changes audited here)
- Escalation: auto-escalate if not actioned within SLA (default 3 business days *(assumed)*) — notify next level + original approver's manager
- Approval dashboard: pending approvals per user (across requisitions + POs)

---

## Data Model

### proc_approval_rules

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| applies_to | string | requisition / po |
| min_amount_cents / max_amount_cents | bigint | max nullable = ∞; ranges must not overlap per (applies_to, category, level) |
| category | string nullable | null = all |
| approver_role | string | spatie role name (or user_id *(assumed: role-based v1)*) |
| level | int | chain order |
| escalation_days | int default 3 | |
| is_active | boolean | |

### proc_approval_delegations — id, company_id (indexed), delegator_id FK, delegate_id FK (≠), start_date/end_date (end ≥ start); overlapping delegations per delegator rejected

---

## DTOs

### CreateApprovalRuleData — applies_to (in set), min/max_amount_cents (min < max; no overlap with existing rules at same level/category — "Amount ranges may not overlap."), category?, approver_role (exists), level (min:1), escalation_days
### CreateDelegationData — delegate_id (≠ self), start/end dates

## Services & Actions

- `ApprovalMatrix::chainFor(string $type, Money $amount, ?string $category): array` — ordered approver levels; resolves delegations at act time
- `ApprovalMatrix::resolveApprover(string $role, ?string $userId): User` — delegation-aware
- `EscalateStaleApprovalsCommand` — pending past escalation_days → notify, once per level (flag)

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `EscalateStaleApprovalsCommand` | notifications | daily 09:00 | once-per-level escalation flag |

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ApprovalRuleResource` | #1 CRUD resource | matrix table grouped by applies_to/level |
| `ApprovalDelegationResource` | #1 CRUD resource | own delegations |
| `PendingApprovalsPage` | #1-style custom page | unified queue (requisitions + POs) |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('procurement.approvals.view-any') && BillingService::hasModule('procurement.approvals')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`procurement.approvals.manage-rules` · `procurement.approvals.delegate-own`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] `chainFor` picks correct levels per amount/category fixtures
- [ ] Overlapping amount ranges rejected at save
- [ ] Delegation: delegate can act within date range; outside range rejected
- [ ] Escalation fires once per level after SLA
- [ ] No matching rule → sensible default (owner approves *(assumed)*)

---

## Build Manifest

```
database/migrations/xxxx_create_proc_approval_rules_table.php
database/migrations/xxxx_create_proc_approval_delegations_table.php
app/Models/Procurement/{ApprovalRule,ApprovalDelegation}.php
app/Data/Procurement/{CreateApprovalRuleData,CreateDelegationData}.php
app/Support/Procurement/ApprovalMatrix.php
app/Console/Commands/Procurement/EscalateStaleApprovalsCommand.php
app/Filament/Operations/Resources/{ApprovalRuleResource,ApprovalDelegationResource}.php
app/Filament/Operations/Pages/PendingApprovalsPage.php
database/factories/Procurement/ApprovalRuleFactory.php
tests/Feature/Procurement/{ApprovalMatrixTest,DelegationTest}.php
```

---

## Related

- [[domains/procurement/requisitions]]
- [[domains/procurement/purchase-orders]]
