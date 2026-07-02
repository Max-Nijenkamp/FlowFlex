---
domain: customer-success
module: success-analytics
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Success Analytics — Unknowns & Assumed Items

## Assumed Items (*(assumed)* markers from spec)

- **Churned = lifecycle_stage churned** — retention/churn math keys off a CRM account lifecycle transition to "churned". The exact stage definition is assumed and owned by `crm.contacts`.
- **NRR definition** — expansion vs churn from invoice revenue per account; the precise NRR formula (cohort window, contraction handling) is assumed pending a finance definition.
- **CSM = account `owner_id`** — CSM-performance grouping keys on the account owner. Shared CS assumption ([[../health-scores/unknowns]]).
- **Section-hidden-when-inactive** — soft sections (NRR, NPS, effectiveness, at-risk) are omitted when their source module is off, mirroring health's renormalisation philosophy.

## Open Questions

- Is a persisted metric snapshot (for fast historical trends without recomputation) wanted? v1 computes live + caches; if snapshots are later needed, this module must own that table (never write another's).
- Export format (CSV via maatwebsite/laravel-excel vs PDF) — not specified; CSV assumed.
- CSM-performance fairness across mid-period account reassignments — not specified.

## Implementation Notes

- No tables, no writes — the arch test (a service references only its own domain's models) applies trivially: this service references **no** models, only other services' read APIs.
- `brick/money` for NRR arithmetic — never raw float math.
- Aggregations cached per [[../../../architecture/caching]]; keys namespaced per company.
