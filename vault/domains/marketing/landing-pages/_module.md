---
domain: marketing
module: landing-pages
type: module
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Landing Pages

Build and publish standalone landing pages from typed content blocks, with embedded forms. Hosted on FlowFlex paths (custom domains = later ADR *(assumed)*).

- **module-key:** `marketing.landing-pages` · **panel:** marketing · **priority:** p3
- **fires-events:** none · **consumes-events:** none
- **tables:** `mkt_landing_pages`

## Module-key

**Priority:** p3
**Panel:** /marketing
**Permission prefix:** `marketing.landing-pages`
**Tables:** `mkt_landing_pages`

## What it does

- Block builder: typed registry (hero, features, testimonial, CTA, form, footer); rich content per block (purified).
- Embed a form (from [[../forms/_module|Forms]]) as a block; page works without forms.
- Slug + publish: live at `/p/{company-slug}/{page-slug}` *(assumed)*; draft 404s publicly.
- SEO: meta title/description/OG image per page. Mobile-responsive by block construction.
- Analytics: visit_count + conversions (form submits on the page). Seeded templates.

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../core/billing-engine/_module\|core.billing]] | gating |
| Hard | [[../../core/rbac/_module\|core.rbac]] | permissions |
| Hard | [[../../core/file-storage/_module\|core.files]] | block images / OG image |
| Soft | [[../forms/_module\|marketing.forms]] | form block; pages work without forms |
| Soft | [[../utm-tracking/_module\|marketing.utm]] | visit UTM capture |

## Sibling notes

- [[architecture]] — publish/visit services, block registry, public render
- [[data-model]] — `mkt_landing_pages` + ERD
- [[api]] — `CreateLandingPageData` DTO
- [[security]] — public render guard, purification, rate limit
- [[decisions]] · [[unknowns]]
- [[features/page-builder]] · [[features/publish-render]] · [[features/page-analytics]]

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | form definition | [[../forms/_module\|marketing.forms]] | embed form block (read-only) |
| Reads | media URL | [[../../core/file-storage/_module\|core.files]] | images (read-only) |
| Feeds | page ref on submit | [[../utm-tracking/_module\|marketing.utm]] | visit UTM capture via `RecordVisitAction` |

No cross-domain **domain events** fired or consumed ([[../../../architecture/event-bus]]).

**Data ownership:** writes **only** `mkt_landing_pages`. Form definitions + media are **read** from their owning modules; a form submit on a page is owned by [[../forms/_module|Forms]] (its event), attributed back read-only as a page conversion. Never writes forms/files tables ([[../../../security/data-ownership]]).

## Build Manifest

```
database/migrations/xxxx_create_mkt_landing_pages_table.php
app/Models/Marketing/LandingPage.php
app/Data/Marketing/CreateLandingPageData.php
app/Support/Marketing/BlockRegistry.php
app/Services/Marketing/LandingPageService.php
app/Actions/Marketing/RecordVisitAction.php
app/Http/Controllers/PublicLandingPageController.php + resources/js/Pages/Landing/Show.vue + block components
app/Filament/Marketing/Resources/LandingPageResource.php
database/factories/Marketing/LandingPageFactory.php
tests/Feature/Marketing/LandingPageTest.php
```

## Test Checklist

- [ ] Tenant isolation: company A cannot read or mutate company B's landing pages data
- [ ] Module gating: artifacts hidden when `marketing.landing-pages` inactive
- [ ] Publish validates every block against `BlockRegistry`; failure keeps draft
- [ ] Public `/p/{company-slug}/{page-slug}` renders published pages; draft 404s; per-IP throttle
- [ ] `RecordVisitAction` increments visit_count atomically; form submission with page ref counts one conversion

## Related

- [[../forms/_module|Forms]] · [[../utm-tracking/_module|UTM Tracking]] · [[../../../frontend/_index]]
