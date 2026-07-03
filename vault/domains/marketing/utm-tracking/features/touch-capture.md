---
domain: marketing
module: utm-tracking
feature: touch-capture
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Touch Capture

Record first- and last-touch UTM for a contact from form submissions (and landing visits).

## Behaviour

- On `FormSubmissionReceived`, extract UTM from the submission payload/cookie.
- `UtmService::record`: create the first touch if none exists (immutable); upsert the last touch.
- Landing-page visits soft-capture via `RecordVisitAction`.

## UI

- **Kind**: background
- **Trigger**: `RecordUtmFromFormListener` on `FormSubmissionReceived` (+ soft `RecordVisitAction`). No page; captured touches surface in the [[attribution]] tables.
- **States**: n/a (background). Duplicate first-touch silently ignored; errors logged per-touch.
- **Gating**: system listener under `CompanyContext`; no user gate.

## Data

- Owns / writes: `mkt_utm_touches` (own module).
- Reads: incoming form event payload; contact ref — read-only.
- Cross-domain writes: none — reacts to `FormSubmissionReceived`, writes own touch rows ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `FormSubmissionReceived` from [[../../forms/_module|marketing.forms]].
- Reads: contact from [[../../../crm/contacts/_module|crm.contacts]].
- Feeds: touches consumed by [[attribution]].

## Test Checklist

### Unit
- [ ] UTM extraction from submission payload/cookie fields *(assumed hidden fields)*

### Feature (Pest)
- [ ] First touch created once and never overwritten -- two raced `FormSubmissionReceived` events yield one immutable first touch
- [ ] Last touch upserted on every subsequent submission
- [ ] Listener is queued (`ShouldQueue` + `WithCompanyContext`); tenant isolation on touch rows

### Livewire
- (none -- background listener)

## Unknowns

> [!warning] UNVERIFIED
> Cookie lawful basis + how anonymous pre-contact visits stitch to a contact. See [[../unknowns]].

## Related

- [[../_module|UTM Tracking]] · [[attribution]] · [[../api]]
