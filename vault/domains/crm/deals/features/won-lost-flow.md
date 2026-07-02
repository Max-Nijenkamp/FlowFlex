---
domain: crm
module: deals
type: feature
feature: won-lost-flow
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Won/Lost Flow

## Purpose

Mark a deal as closed (won or lost), capture outcome metadata, fire domain events for downstream consumers, and lock the deal against further stage movement.

---

## Flow: Close as Won

1. User triggers `CloseDealAction` modal → selects outcome `won`
2. `DealService::close(CloseDealData)` called
3. State transition: `open → won` (spatie/laravel-model-states)
4. Side effects:
   - `actual_close_date` = today
   - `probability` = 100
   - `DealWon` event fired (see [[../architecture]] Events section for payload)
5. Audited via activitylog
6. `CreateInvoiceAction` becomes visible on the view page (if `finance.invoicing` active)

## Flow: Close as Lost

1. User triggers `CloseDealAction` modal → selects outcome `lost`
2. `lost_reason` is required (validation enforced)
3. `DealService::close(CloseDealData)` called
4. State transition: `open → lost`
5. Side effects:
   - `actual_close_date` = today
   - `probability` = 0
   - `lost_reason` + `lost_to` stored
   - `DealLost` event fired
6. Audited via activitylog

## Flow: Reopen *(assumed)*

1. User triggers reopen action (permission: `crm.deals.reopen`)
2. State transition: `won/lost → open`
3. `actual_close_date`, `lost_reason`, `lost_to` cleared
4. Audited
5. Any downstream effects (e.g. Finance invoice stub) are NOT reversed — see [[../decisions]]

## Guard: Closed Deal Immutability

A closed deal (won or lost) cannot move pipeline stages. `DealService::moveToStage()` throws `ClosedDealImmutableException` if `status != open`.

---

## UI

- **Kind**: simple-resource — close is a modal action on `DealResource`, not a separate page.
- **Page**: `CloseDealAction` modal on the `DealResource` view/edit page at `/crm/deals`.
- **Layout**: modal with outcome radio (won/lost); when `lost` selected, `lost_reason` + `lost_to` fields appear (required). Reopen is a separate gated action.
- **Key interactions**: modal action → outcome select → conditional fields → confirm; on won, `CreateInvoiceAction` becomes visible on the view page.
- **States**: empty (n/a) · loading (spinner during state transition) · error (`lost_reason` required validation; `ClosedDealImmutableException` if re-closing) · selected (chosen outcome highlighted in modal).
- **Gating**: close `crm.deals.update`; reopen `crm.deals.reopen`.

## Data

- Owns / writes: `crm_deals` (`status`, `actual_close_date`, `probability`, `lost_reason`, `lost_to`), `crm_deal_contacts`, `crm_deal_products`.
- Reads: `crm_pipeline_stages` (crm.pipeline) for stage state.
- Cross-domain writes: none — invoice creation is via the `DealWon` event, consumed by finance ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing on close itself.
- Feeds: `DealWon` → finance.invoicing (draft invoice stub), crm.contacts (lifecycle → customer), crm.forecasting; `DealLost` → crm.forecasting, crm.sequences.
- Shared entity: `crm_pipeline_stages` owned by crm.pipeline; deals only reference stage state.
