---
type: adr
date: 2026-06-12
status: decided
domain: Frontend
color: "#F97316"
---

# Adopt "Switchboard+" design system for public site, auth and panel skins

## Context

A high-fidelity design handoff bundle (`design_handoff_flowflex_site/`) landed with a complete redesign of the public surface: all marketing pages, auth screens and a brand skin for the Filament panels. The system — "Switchboard+" — makes the per-user-per-module business model visible: modules are literal switches, invoices are receipts, stats live in blueprint cells, cross-domain flow is shown as dark bands with animated pulse lines. Colors, type, spacing and copy were declared final.

## Options Considered

1. **Implement the bundle's JSX/CSS directly** — fast, but violates the Vue 3 + Inertia + Tailwind v4 conventions and ships a second styling system.
2. **Recreate in existing conventions (chosen)** — design references are recreated as Vue components with Tailwind utilities; panel mocks are treated as a Filament theme target, restyling native components via per-panel theme CSS.
3. **Partial adoption (marketing only)** — rejected; the handoff explicitly covers auth + panels and the user asked for full adoption.

## Decision

- Tokens extended in `resources/css/app.css` `@theme`: `--font-display` (Archivo), `--color-card`, `--color-ink-faint` (#98A0AB), `--color-line-strong`, `--color-accent-deep`, `--color-flow-bg`, marquee/pulse-dash animations; small custom utilities for the graph-paper texture and receipt sawtooth (cannot be expressed as inline utilities).
- One shared component per design-system primitive in `Components/Marketing/` + `Components/UI/Switch.vue`; static marketing content in `resources/js/data/marketing.ts`.
- Buttons: primary = indigo + glow, dark = ink, outline = white; 10/12/8px radii (no more pills) — `BaseButton` variants updated.
- Auth: `AuthLayout` gains a `split` prop — split dark flow-pulse shell for login/invite, centered card for forgot/reset.
- Filament: one shared `resources/css/filament/flowflex-skin.css` imported by all five panel themes (ink sidebar in both modes, domain-color active item via `--primary-*`, mono table headers, zebra rows, paper canvas, 9px buttons); per-panel theme files reduce to the skin import + a mono `--ff-panel-label`. Staff (`/admin`) login button is ink; customer panels indigo. Panel providers: light wordmark + Instrument Sans.

## Consequences

- The marketing/auth surface is pixel-aligned with the handoff while staying 100% inside Vue + Tailwind conventions; the design bundle remains in the repo as reference.
- Per-panel Filament theme drift is eliminated — future skin changes happen once in `flowflex-skin.css`.
- Sidebar is permanently dark; any new panel must use the light logo variant and set `--ff-panel-label`.
- Google Fonts adds three families (Archivo, Instrument Sans, JetBrains Mono); self-hosting is a later optimisation.

## Amendments

- **2026-06-12 (same day, pass 2)**: graph-paper grid texture dropped from the system — replaced by the **bloom** treatment (`.bg-bloom` indigo radial + paper-deep fade on light surfaces; `.bg-bloom-accent` white+sky glows on CTA bands; Filament login matches). No grid/graph patterns anywhere.
- **2026-06-12**: skin selectors corrected against rendered Filament 5 markup (`fi-sidebar-item-btn`, `nav.fi-topbar`, `fi-pagination-item.fi-active`, `fi-wi-widget`) — the original selector names from older docs matched nothing and failed silently. Rule recorded as filament-patterns item 16.
- **2026-06-12**: Spotlight (⌘K/Ctrl+K) added as a system component; panels drop `globalSearchKeyBindings`.
- **2026-06-12**: UX-state rules codified in [[../../architecture/patterns/ux-states|ux-states]] and wired platform-wide (`Table::configureUsing`, skin empty-state/selected/wizard styling).

- **2026-06-12 (pass 3)**: expansion pages §14–25 (catalogue, switch-over, trust, changelog, patchwork calculator, case study, status, help center, 404, mail theme) built **in-system without the regenerated bundle** — layouts/copy *(assumed)*, tracked in [[../gaps/gap-switchboard-expansion-spec-missing]]. Static marketing content centralised in `app/Support/Marketing/MarketingContent.php`.

## Related

- [[frontend/_index|Frontend index]] · `design_handoff_flowflex_site/README.md` · [[product/brand]] · [[product/ux-principles]]
