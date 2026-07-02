---
domain: marketing
module: forms
feature: embed-hosted
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Embed & Hosted Page

Render the form off-platform (embed snippet / iframe) and on a hosted FlowFlex URL.

## Behaviour

- Embed: JS snippet or iframe pulls a cached form-definition JSON and renders via a lightweight renderer.
- Hosted: standalone page at `/f/{slug}` (Vue + Inertia), branded, mobile-responsive.
- Views increment `mkt_forms.view_count` (conversion base). Inactive form → 404.

## UI

- **Kind**: public-vue
- **Page**: hosted form `/f/{slug}` (Vue + Inertia, ui-strategy row #16); embed = JS renderer injected into the customer's own site.
- **Layout**: single-column form matching field definition; thank-you / redirect on success.
- **Key interactions**: fill → submit → POST `/f/{slug}` → thank-you or redirect; client-side validation mirrors server rules.
- **States**: empty (n/a — always shows fields) · loading (definition fetch for embed) · error (validation messages inline; 404 if inactive) · submitted (thank-you panel).
- **Gating**: public, unauthenticated; resolves company by slug (no session). Throttled + honeypot ([[../security]]).

## Data

- Owns / writes: `mkt_forms.view_count` (own module).
- Reads: cached form definition (own).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Feeds: submissions flow into [[public-submit]].
- Consumed by: [[../../landing-pages/_module|Landing Pages]] form block embeds this.
- Shared entity: none.

## Unknowns

- Origin allow-list per-form vs company-wide. See [[../unknowns]].

## Related

- [[../_module|Forms]] · [[public-submit]] · [[../architecture]]
