---
domain: events
module: sponsors
feature: sponsor-revenue
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Sponsor Revenue

A per-event revenue summary of sponsorship value, split committed vs. paid by tier.

## Behaviour

- `SponsorService::revenue(eventId)` aggregates `amount_cents` across sponsors, splitting committed vs. paid (brick/money), grouped by tier.
- Feeds the event analytics revenue section.

## UI

- **Kind**: widget
- **Page**: revenue summary widget on the `SponsorResource` list / event dashboard.
- **Layout**: stat cards (total committed, total paid) + a per-tier breakdown bar.
- **Key interactions**: event selector filters the widget; click a tier → filter the sponsor list.
- **States**: empty (no sponsors → zeroed cards) · loading (skeleton stats) · error (retry) · selected (tier filter active).
- **Gating**: `events.sponsors.view-any`.

## Data

- Owns / writes: nothing (read aggregation over `ev_sponsors`).
- Reads: own `ev_sponsors`.
- Cross-domain writes: NONE ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: sponsorship revenue → [[../../event-analytics/_module|Event Analytics]].
- Shared entity: none.

## Test Checklist

### Unit
- [ ] Revenue split committed vs paid per tier via brick/money integers

### Feature (Pest)
- [ ] Summary reads own-company sponsors only; reflects invoice-paid state when finance active

### Livewire
- [ ] Revenue widget renders split; hidden without permission/module

## Unknowns

- Sponsor ROI (beyond revenue) — see [[../unknowns]] + [[../../_opportunities]].

## Related

- [[../_module|Sponsors]] · [[sponsor-management]] · [[../../event-analytics/_module|Event Analytics]]
