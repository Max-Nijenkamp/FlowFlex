---
domain: marketing
module: utm-tracking
feature: attribution
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Attribution

Report which sources/campaigns drive contacts and revenue, first- vs last-touch.

## Behaviour

- `UtmService::attribution(model, from, to)` aggregates touches → contacts → deal value by source/medium/campaign.
- Toggle first-touch vs last-touch model.
- Revenue join is read-only through CRM deals.

## UI

- **Kind**: widget
- **Page**: attribution tables rendered inside the [[../../marketing-analytics/_module|Marketing Analytics]] dashboard (not a standalone page) — first/last toggle.
- **Layout**: grouped table (source / medium / campaign → contacts, deal value) + model toggle.
- **Key interactions**: switch model; change date range (inherited from dashboard); drill by dimension.
- **States**: empty (no touches in range → "no attribution data") · loading (aggregate query) · error · selected (row drill).
- **Gating**: `marketing.utm.view` (+ dashboard's `marketing.analytics.view`).

## Data

- Owns / writes: none at read time (reads own `mkt_utm_touches`).
- Reads: contacts + deal values from CRM (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Reads: [[../../../crm/contacts/_module|crm.contacts]] + [[../../../crm/deals/_module|crm.deals]] (read-only revenue join).
- Consumed by: [[../../marketing-analytics/_module|Marketing Analytics]] dashboard.

## Test Checklist

### Unit
- [ ] First- vs last-touch toggle attributes the same contact to different sources correctly

### Feature (Pest)
- [ ] Aggregation joins touches -> contacts -> deal value read-only via CRM (no writes to crm tables)
- [ ] Tenant isolation: attribution over own-company touches only

### Livewire
- [ ] Attribution tables render inside the Marketing Analytics dashboard with the first/last toggle

## Unknowns

- Multi-touch models (linear/time-decay) beyond first/last. See [[../unknowns]].

## Related

- [[../_module|UTM Tracking]] · [[touch-capture]] · [[../architecture]]
