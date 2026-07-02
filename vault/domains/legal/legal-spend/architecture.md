---
domain: legal
module: legal-spend
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Legal Spend — Architecture

No state machine — a lightweight `pending / approved / rejected` status on expenses.

## Approval flow

```mermaid
flowchart LR
    submit[submit expense] --> pending
    pending -->|approve, approver≠submitter| approved
    pending -->|reject| rejected
    approved -->|optional manual| ap[finance.ap bill link]
```

## Services & Actions

- `LegalSpendService::approve / reject` — approver must differ from submitter.
- `LegalSpendService::matterSpend(matterId): Money` — sums **approved only** (brick/money).
- `LegalSpendService::variance(?matterId, period): VarianceData` — approved actual vs `legal_budgets`, over-budget flag.
- Reads matters via `MatterService::accessibleFor` so spend inherits confidentiality.

## Filament Artifacts

**Nav group:** Spend

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `LegalExpenseResource` | #1 CRUD resource | tweaks: state-badge-column (pending/approved/rejected), custom-header-actions (approve / reject) | list filters: matter, vendor, status, period; matter picker limited to `accessibleFor` matters ([[./features/expense-records]]) |
| `ApprovalQueuePage` *(assumed — not yet in build manifest)* | #17 custom page | [[../../../architecture/patterns/page-blueprints#Gallery / Directory Grid]] — pending expenses grouped by matter/vendor, bulk approve | "Approval queue" at `/legal/spend/approvals`; approve blocked when actor is submitter ([[./features/invoice-approval]]) |
| `LegalSpendDashboardPage` | #6 dashboard page | [[../../../architecture/patterns/page-blueprints#Dashboard]] | KPI row + budget-vs-actual bars + vendor breakdown (apex); widget polling 30–60s ([[./features/budget-vs-actual]]) |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('legal.spend.view-any') && BillingService::hasModule('legal.spend')`
per [[../../../architecture/filament-patterns]] #1. `ApprovalQueuePage` and `LegalSpendDashboardPage` are custom
pages and MUST state this explicitly. **All spend surfaces inherit matter confidentiality** — expense/variance
reads flow through `MatterService::accessibleFor`, so spend on a confidential matter is hidden from users outside
its owner + access-list ([[./security]]).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Expense CRUD (form, API) | Optimistic | `updated_at` stale-check → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Budget CRUD | Optimistic | `updated_at` stale-check ([[../../../architecture/patterns/optimistic-locking]]) |
| Expense approve / reject (money-affecting; separation-of-duties) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read status, assert `approved_by ≠ submitter`, write — prevents concurrent double-approve ([[../../../architecture/patterns/optimistic-locking]] money tier) |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Patterns

- `money` (all amounts via brick/money), `custom-pages` (dashboard).
