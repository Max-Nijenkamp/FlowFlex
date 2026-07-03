---
domain: procurement
module: approvals
feature: approval-matrix
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
---

# Approval Matrix & Routing

The authority matrix: rules mapping `(applies_to, category, amount range, level)` → approver role. Exposes `ApprovalMatrix::chainFor(...)`, the single routing API requisitions and POs call.

## Behaviour

- Each rule: applies_to (requisition|po), optional category, `[min,max)` amount range, level, approver_role, escalation_days.
- `chainFor(type, amount, category)` returns the ordered list of levels whose range contains `amount` (category-specific rules override null-category ones).
- Ranges within the same `(applies_to, category, level)` may **not** overlap — enforced at save.
- No matching rule → owner approves *(assumed)*.

## UI

- **Kind**: simple-resource
- **Page**: "Approval Rules" (`/operations` → Procurement → Settings → Approval Rules)
- **Layout**: table grouped by applies_to then level; columns amount range, category, approver role, escalation days, active.
- **Key interactions**: create/edit rule form (amount range, category select, role select, level, escalation days); overlap validation inline.
- **States**: empty ("No rules yet — spend routes to the owner" CTA) · loading (table skeleton) · error (toast + retry) · saved (row flash).
- **Gating**: view `procurement.approvals.view-any`; edit `procurement.approvals.manage-rules`.

## Data

- Owns / writes: `proc_approval_rules`.
- Reads: `core.rbac` role names; (soft) `hr.org` departments for category/dept context.
- Cross-domain writes: none — routing service only ([[../../../../security/data-ownership]]).

## Relations

- Feeds: `ApprovalMatrix::chainFor` consumed by [[../../requisitions/_module|requisitions]] + [[../../purchase-orders/_module|procurement POs]] (synchronous read).
- Shared entity: RBAC roles (owned by core.rbac).

## Test Checklist

### Unit
- [ ] `chainFor(type, amount, category)`: overlapping rules resolve by level order; no matching rule -> defined fallback
- [ ] Amount-range boundaries inclusive/exclusive per spec

### Feature (Pest)
- [ ] Rule CRUD company-scoped + permission-gated; chain reflects rules at call time
- [ ] Tenant isolation: rules never leak across companies

### Livewire
- [ ] `ApprovalRuleResource` validates range overlaps *(assumed warning)*; hidden without `procurement.approvals` permission/module

## Unknowns

- Per-user rule targets deferred `*(assumed: role-based)*`. Category taxonomy = free string `*(assumed)*`.

## Related

- [[../_module|Approvals]] · [[delegation]] · [[escalation]] · [[../api]]
