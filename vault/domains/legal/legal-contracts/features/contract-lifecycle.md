---
domain: legal
module: legal-contracts
feature: contract-lifecycle
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Contract Lifecycle

State machine + scheduled transitions + renewal/notice alerting across a contract's life.

## Behaviour

- States: `draft → in_review → signed → active → expired | terminated` (renew keeps `active`). Full table: [[../architecture]].
- `LegalContractLifecycleCommand` (daily 05:45): activates signed contracts on start date; expires active contracts past end date with no renewal; fires notice-deadline alerts at 90/30d (once each via `alerted_levels`); fires obligation overdue alerts.
- Notice deadline = `renewal_date − notice_period_days`.
- Renew resets alert guards and audits the change.
- Terminate requires a reason.

## UI

- **Kind**: custom-page
- **Page**: "Renewals & Lifecycle" (`/legal/contracts/lifecycle`) — plus a `ContractRenewalWidget` on the panel dashboard.
- **Layout**: board/queue of contracts approaching notice deadline or expiry, grouped by urgency (overdue · ≤30d · ≤90d); each card shows counterparty, renewal date, days-to-notice, action buttons.
- **Key interactions**: click card → slide-over with sign / renew / terminate; renew opens a date form; bulk "acknowledge" to snooze noise.
- **States**: empty ("No renewals in the next 90 days") · loading (skeleton columns) · error (toast + retry) · selected (card highlighted, slide-over open).
- **Gating**: view `legal.contracts.view-any`; sign `legal.contracts.sign-off`; terminate `legal.contracts.terminate`.

## Data

- Owns / writes: `legal_contracts` (status, dates, `alerted_levels`, `signed_at`), `legal_contract_obligations.alerted`.
- Reads: none cross-domain.
- Cross-domain writes: none — alerts dispatched via `core.notifications` (its own tables) ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: renewal/overdue notifications via `core.notifications`.
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Notice deadline math: `renewal_date − notice_period_days` computed correctly across month boundaries
- [ ] Urgency bucketing (overdue · ≤30d · ≤90d) assigns each contract to exactly one band
- [ ] `alerted_levels` guard treats 90 and 30 as independent once-flags

### Feature (Pest)
- [ ] Lifecycle command activates `signed` contracts on start date and expires `active` past end date with no renewal
- [ ] Notice-deadline alert fires once per level (re-running command same day does not re-alert)
- [ ] Renew resets `alerted_levels`, sets new dates, keeps state `active`, and writes an audit entry
- [ ] Terminate requires a reason; missing reason rejected
- [ ] Concurrent transition uses `lockForUpdate` — second writer sees re-read state, no double transition

### Livewire
- [ ] `ContractLifecyclePage` renders urgency-grouped queue and gates on `legal.contracts.view-any`
- [ ] Slide-over sign/renew/terminate actions gate on `sign-off` / `renew` / `terminate` permissions
- [ ] `ContractRenewalWidget` hidden when module inactive

## Unknowns

- Alert cadence 90/30d `*(assumed)*`; auto-extend vs explicit renew dates open — [[../unknowns]].

## Related

- [[../_module|Legal Contracts]] · [[../architecture]] · [[./obligation-tracking]]
