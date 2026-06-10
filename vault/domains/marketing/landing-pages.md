---
type: module
domain: Marketing
domain-key: marketing
panel: marketing
module-key: marketing.landing-pages
status: planned
priority: p3
depends-on: [core.billing, core.rbac, core.files]
soft-depends: [marketing.forms, marketing.utm]
fires-events: []
consumes-events: []
patterns: []
tables: [mkt_landing_pages]
permission-prefix: marketing.landing-pages
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Landing Pages

Build and publish standalone landing pages with embedded forms. Hosted on FlowFlex paths (custom domains = later ADR *(assumed)*).

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] | gating, permissions, images |
| Soft | [[domains/marketing/forms\|marketing.forms]] | form block embeds; pages work without forms |
| Soft | [[domains/marketing/utm-tracking\|marketing.utm]] | visit UTM capture |

---

## Core Features

- Page builder: section blocks (hero, features, testimonial, CTA, form, footer) — typed block registry
- Rich content editing per block (purified)
- Embed a form (from Forms module) into a page
- Slug + publish: page live at `/p/{company-slug}/{page-slug}` *(assumed)*
- Custom domain support deferred (CNAME — ADR later)
- SEO: meta title, description, OG image per page
- Mobile-responsive output (block templates responsive by construction)
- Page analytics: visits, conversions (form submits on page)
- Templates: pre-built page layouts (seeded)
- Draft / published status

---

## Data Model

### mkt_landing_pages

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| slug | string | sluggable, unique per company |
| blocks | jsonb | [{type, config}] — types in registry |
| meta_title / meta_description / og_image | string nullable | |
| status | string default `draft` | draft / published |
| published_at | timestamp nullable | |
| visit_count | int default 0 | |
| deleted_at | timestamp nullable | |

---

## DTOs

### CreateLandingPageData — name, blocks[] (types in registry, configs schema-validated per type; form block: form exists + active), meta fields

## Services & Actions

- `LandingPageService::publish(...)` / `unpublish(...)`
- `RecordVisitAction` — public, increments + UTM capture (soft)
- Conversion attribution: form submission with page ref counts as page conversion

---

## Filament

**Nav group:** Landing Pages

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `LandingPageResource` | #1 CRUD resource | block repeater builder, preview, publish; visit/conversion columns |

Public rendering: Vue + Inertia block renderer — ui-strategy row #16.

---

## Permissions

`marketing.landing-pages.view-any` · `marketing.landing-pages.create` · `marketing.landing-pages.update` · `marketing.landing-pages.publish`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Draft page 404 publicly; published renders all block types
- [ ] Invalid block type/config rejected at save
- [ ] Form block submits → conversion counted for page
- [ ] Visit count increments; SEO meta rendered
- [ ] Block content purified

---

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

---

## Related

- [[domains/marketing/forms]]
- [[frontend/_index]]
- [[domains/marketing/utm-tracking]]
