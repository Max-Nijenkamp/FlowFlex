---
domain: marketing
module: utm-tracking
feature: utm-builder
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: UTM Builder

Generate correctly encoded tagged URLs for campaigns.

## Behaviour

- Enter base URL + UTM params (source, medium, campaign, term, content).
- `BuildUtmUrlAction` returns a correctly URL-encoded tagged link with a copy button.

## UI

- **Kind**: custom-page
- **Page**: `UtmBuilderPage` (`/marketing/utm/builder`) — Analytics nav group (ui-strategy row #7, form-style custom page).
- **Layout**: form (base URL + 5 UTM fields) → generated-URL field + copy button; optional saved-presets list.
- **Key interactions**: fill fields → live-generated URL → copy; clear/reset.
- **States**: empty (fields blank → placeholder URL) · loading (n/a — client-side) · error (invalid base URL) · selected (generated URL focused for copy).
- **Gating**: `marketing.utm.build`.

## Data

- Owns / writes: none (stateless tool) — presets, if added, would live in own config.
- Reads: none cross-domain.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Feeds: generated URLs used in [[../../campaigns/_module|Campaigns]] + external channels; their clicks later become [[touch-capture]] touches.
- Shared entity: none.

## Unknowns

- Saved presets / campaign-name dictionary — nice-to-have, unspecced.

## Related

- [[../_module|UTM Tracking]] · [[touch-capture]] · [[attribution]]
