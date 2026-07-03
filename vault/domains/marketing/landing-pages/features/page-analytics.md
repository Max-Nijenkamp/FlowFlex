---
domain: marketing
module: landing-pages
feature: page-analytics
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Page Analytics

Count visits and conversions per page (funnel: visits → form submits on the page).

## Behaviour

- Each public render increments `visit_count` via `RecordVisitAction` (soft UTM capture alongside).
- A form submission carrying this page's ref counts as a conversion (read-only aggregation — submission owned by Forms).
- Conversion rate = conversions / visits.

## UI

- **Kind**: widget
- **Page**: visit/conversion columns on the `LandingPageResource` table + a small funnel on the page view; the cross-page roll-up lives in [[../../marketing-analytics/_module|Marketing Analytics]].
- **Layout**: two columns (visits, conversions) + computed rate; funnel bar on view page.
- **Key interactions**: sort/filter by conversion rate; click through to the page.
- **States**: empty (unpublished → dashes) · loading · error · selected (row).
- **Gating**: `marketing.landing-pages.view-any`.

## Data

- Owns / writes: `mkt_landing_pages.visit_count` (own module).
- Reads: form submissions with page ref (read-only, owned by Forms).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Reads: submission conversions from [[../../forms/_module|marketing.forms]].
- Feeds: page funnel consumed by [[../../marketing-analytics/_module|Marketing Analytics]].
- Shared entity: none written.

## Test Checklist

### Unit
- [ ] Conversion rate = conversions / visits; zero visits -> 0, not division error

### Feature (Pest)
- [ ] Public render increments `visit_count` atomically (parallel hits lose no counts)
- [ ] Form submission carrying the page ref counts as one conversion (read-only over Forms data)
- [ ] Tenant isolation: analytics scoped to own-company pages

### Livewire
- [ ] Visit/conversion columns render on `LandingPageResource`; hidden without `marketing.landing-pages.view-any`

## Unknowns

- Page-ref propagation from an embedded form submit. See [[../unknowns]].

## Related

- [[../_module|Landing Pages]] · [[publish-render]] · [[../../marketing-analytics/_module|Marketing Analytics]]
