---
type: module
domain: Marketing & Content
panel: marketing
cssclasses: domain-marketing
phase: 5
status: planned
migration_range: 400000–449999
last_updated: 2026-05-09
---

# Landing Page Builder

Conversion-optimised standalone pages for campaigns — no site navigation, single CTA, A/B testing built in. Different from CMS (which builds full website pages). Replaces Unbounce, Instapage, Leadpages, Swipe Pages.

**Panel:** `marketing`  
**Phase:** 5

---

## Why Not CMS

CMS builds website pages (with navigation, footer, blog, multiple CTAs). Landing pages are campaign-specific:
- No navigation (stops visitors leaving before converting)
- Single focused CTA (reduce decision paralysis)
- Matched message to ad (ad says "50% off chairs" → landing page header says "50% off chairs")
- A/B tested (run 2 variants simultaneously to find best performer)
- Removed from sitemap (Google shouldn't index campaign-specific pages)

---

## Features

### Page Builder
- Drag-and-drop block editor (hero, features, social proof, pricing, FAQ, CTA, form)
- Section-level and element-level editing
- Mobile preview (responsive at 375px, 768px, 1280px)
- Template library: lead gen, webinar registration, free trial, product launch, ebook download, event
- Custom HTML/CSS/JS block for advanced customisation
- Import from existing URL (scrape and recreate as editable template)

### Conversion Elements
- Hero with headline + subheadline + form + image/video
- Trust signals: customer logos, review stars, certifications
- Countdown timer (for limited offers)
- Social proof ticker ("142 people signed up in the last 24 hours")
- Video embed (YouTube, Vimeo, native upload)
- Exit-intent popup (detect mouse leaving viewport → show offer)
- Sticky CTA bar (scrolls with page)

### Forms
- Native form builder (name, email, phone, custom fields)
- Or embed Forms module form
- Post-submit: show thank you message / redirect to thank you page / trigger automation
- GDPR: consent checkbox with configurable text

### A/B Testing
- Create variant B of any page (duplicate, edit headline/CTA/image)
- Split traffic 50/50 (or custom split)
- Track: visits, form submissions, conversion rate per variant
- Statistical significance indicator (when to declare winner)
- Pause losing variant, promote winner to 100%

### URL & Publishing
- Publish on FlowFlex subdomain: `pages.company.com/my-campaign`
- Or custom domain: `offer.company.com`
- Or embed on existing website (iframe or JS)
- UTM parameter pass-through (preserve campaign attribution from ad click)
- Pixel support: Facebook Pixel, Google Tag Manager, LinkedIn Insight Tag

### Analytics (per page)
- Visits, unique visitors, form submissions, conversion rate
- Traffic source breakdown (organic, paid, direct, email)
- Device split (mobile vs desktop)
- Scroll depth heatmap
- Time on page
- Compare A/B variants

---

## Data Model

```erDiagram
    landing_pages {
        ulid id PK
        ulid company_id FK
        string name
        string slug
        string custom_domain
        string status
        json page_content
        boolean remove_from_sitemap
        ulid active_variant_a FK
        ulid active_variant_b FK
        integer ab_split_percent
        string ab_winner
    }

    landing_page_variants {
        ulid id PK
        ulid landing_page_id FK
        string label
        json content
        integer visits
        integer conversions
        decimal conversion_rate
    }
```

---

## Permissions

```
marketing.landing-pages.view
marketing.landing-pages.create
marketing.landing-pages.publish
marketing.landing-pages.manage-ab-tests
```

---

## Competitors Displaced

Unbounce · Instapage · Leadpages · Swipe Pages · Carrd · ClickFunnels

---

## Related

- [[MOC_Marketing]]
- [[MOC_CRM]] — form submissions → contacts
