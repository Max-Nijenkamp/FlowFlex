---
type: adr
date: 2026-06-20
status: decided
domain: All
color: "#F97316"
updated: 2026-06-20
---

# Cross-domain write resolutions (bounded-context annotations & erasure)

## Context

The full-mapping pass surfaced four places where a spec wanted to write **another domain's** table —
violating [[../security/data-ownership|data ownership]]. They were flagged UNVERIFIED in-note; this ADR
gives the one canonical resolution they all point to.

## Decision — two rules

**Rule A — annotate via your own projection, never a foreign column.** When domain A needs a field derived
from or attached to domain B's entity, A owns its **own** table keyed by B's id (`{b_entity}_id`). A never
adds a column to B's table. To *change* B's entity, A calls B's service or fires an event B's own listener
applies.

**Rule B — erasure is domain-local.** GDPR erasure is not a cross-domain write. The privacy orchestrator
([[../domains/core/data-privacy/_module|core.privacy]]) fires an erasure request; **each domain's own
eraser** (registered in a `PersonalDataRegistry`) deletes/anonymises **its own** tables. The orchestrator
coordinates and tracks completion; it never writes `hr_*` / `crm_*` / etc.

## The four cases, resolved

| Flagged tension | Resolution |
|---|---|
| CRM forecasting writing `crm_deals.forecast_category` | **Rule A** — forecasting owns `crm_forecast_categories` keyed by `deal_id`; no column on `crm_deals`. |
| Procurement writing `procurement_approved_at` on the ops-owned PO table | **Rule A** — procurement owns `proc_po_approvals` keyed by `po_id`; ops PO stays ops-owned. |
| core.privacy erasure writing `hr_employees` (and other domains) directly | **Rule B** — HR's own eraser erases `hr_*`; privacy only orchestrates via `PersonalDataRegistry`. |
| DMS retention acting on `dms_documents` (owned by dms.library) | Already correct — retention commands library's `DocumentService`; documented, no change. |

## Consequences

- The affected `unknowns.md` UNVERIFIED flags are resolved by this ADR (they can cite it).
- Adds a couple of small projection tables — a fair price for hard bounded-context walls.
- Arch-test at build: fail any `Services/{A}` write to `Models/{B}` (A≠B, non-platform).

## Related

- [[../security/data-ownership]] · [[decision-2026-06-20-full-mapping-conventions]] · [[../architecture/cross-domain-relations]]
