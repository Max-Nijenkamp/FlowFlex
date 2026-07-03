---
domain: customer-success
module: success-analytics
feature: retention-nrr
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Retention & NRR

Compute retention rate, churn rate, and net revenue retention over a date window from lifecycle and invoice-revenue signals.

## Behaviour

- Retention/churn are derived from CRM account lifecycle transitions over `[from, to]` (`churned` = lifecycle stage churned *(assumed)*).
- NRR = expansion vs churn from invoice revenue per account, computed with `brick/money` (never raw floats). **Only shown when `finance.invoicing` is active** — otherwise the section is omitted.
- All numbers are read-only aggregations; nothing is persisted.
- Results are cached (1 h historical / 15 min current) and namespaced per company.

## UI

- **Kind**: widget — `RetentionWidget` + `NrrWidget`, composed on the [[./cs-dashboard|CS Dashboard]].
- **Page**: fragments on `CsDashboardPage` (`/crm/cs-dashboard`).
- **Layout**: retention/churn as trend lines; NRR as a single headline % with expansion/contraction/churn breakdown.
- **Key interactions**: respond to the dashboard's date-range filter; NRR widget hidden when invoicing inactive.
- **States**: empty (no accounts in window → "no data for this period") · loading (chart skeleton) · error (source read fails → widget shows soft error, siblings still render) · n/a selected.
- **Gating**: `cs.analytics.view`.

## Data

- Owns / writes: nothing (no tables).
- Reads: account lifecycle (`crm.contacts`), invoice revenue per account (`finance.invoicing`) — via read APIs, never their tables.
- Cross-domain writes: none — read-only ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `crm.contacts` lifecycle, `finance.invoicing` revenue (read APIs).
- Feeds: nothing (leaf consumer; renders on the dashboard).
- Shared entity: `crm_accounts` + invoices (read-only).

## Test Checklist

### Unit
- [ ] Retention/churn rate math over lifecycle windows; NRR arithmetic via brick/money integers (no float)

### Feature (Pest)
- [ ] NRR section requires `finance.invoicing` active; revenue read through the finance read API only
- [ ] Tenant isolation: rates computed per company

### Livewire
- [ ] Retention/NRR widgets render; hidden without the analytics permission/module

## Unknowns

- NRR formula + churned-stage definition assumed — [[../unknowns]].

## Related

- [[../_module|Success Analytics]] · [[./cs-dashboard|CS Dashboard]]
- [[../../../finance/invoicing/_module|finance.invoicing]] · [[../../../../security/data-ownership]]
