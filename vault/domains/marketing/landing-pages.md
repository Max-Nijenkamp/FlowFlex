---
type: module
domain: Marketing
panel: marketing
module-key: marketing.landing-pages
status: planned
color: "#4ADE80"
---

# Landing Pages

Build and publish standalone landing pages with embedded forms. Hosted on FlowFlex subdomains or custom domains.

## Core Features

- Page builder: section blocks (hero, features, testimonial, CTA, form, footer)
- Rich content editing per block
- Embed a form (from Forms module) into a page
- Slug + publish: page goes live at a public URL
- Custom domain support (advanced — CNAME)
- SEO: meta title, description, OG image per page
- Mobile-responsive output
- Page analytics: visits, conversions (form submits)
- Templates: pre-built page layouts
- Draft / published status

## Data Model

| Table | Key Columns |
|---|---|
| `mkt_landing_pages` | company_id, name, slug, blocks (json), meta_title, meta_description, og_image, status, published_at, visit_count |

## Filament

**Nav group:** Landing Pages

- `LandingPageResource` — build (block repeater), preview, publish
- Public rendering via Vue + Inertia (see [[frontend/_index]])

## Cross-Domain

- Embeds Forms; form submits tracked as page conversions

## Related

- [[domains/marketing/forms]]
- [[frontend/_index]]
- `spatie/laravel-sluggable`
