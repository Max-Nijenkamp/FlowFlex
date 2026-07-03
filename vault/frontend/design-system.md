---
type: frontend
category: design-system
status: stable
last-reviewed: 2026-06-12
color: "#FBBF24"
---

# Switchboard+ — Design System Reference

Canonical implementation reference for the FlowFlex design system across **public site, auth screens and Filament panel skins**. Brand-level rules live in [[../product/brand]]; panel chrome rules in [[../product/ux-principles]]; screen-state rules in [[../architecture/patterns/ux-states]]. **Source spec: `vault/product/design_handoff_flowflex_site 2/`** (212-line README, §1–28, incl. `pages/extras-*.jsx` + `panel/panel.jsx`) — the older sibling folder without " 2" is the superseded 13-screen bundle. §14–25 reconciliation status: [[../build/gaps/gap-switchboard-expansion-spec-missing]].

**Panel chrome (§12, implemented)**: sidebar footer = "Your panels" mono switcher chips (26×30px, active outlined in panel color, `SidebarFooter` support class + `SIDEBAR_FOOTER` hook) + user card (avatar initial, name, company); topbar = panel crumb left (`TopbarCrumb` + `TOPBAR_START` hook — page-header breadcrumbs hidden, ONE crumb line) + 320px search trigger with ⌘K kbd (opens Spotlight via `ff-spotlight-open` window event, `GLOBAL_SEARCH_BEFORE` hook) + bell with ping dot + 30px ringed avatar. Panel switching lives ONLY in the sidebar chips — the profile dropdown carries no "Switch to" items (PanelSwitchItems removed 2026-06-12).

**The idea**: the per-user-per-module business model made visible. Modules are literal switches, invoices are receipts, stats live in blueprint cells, cross-domain data flow is a dark band with animated pulse lines.

---

## Tokens (source: `app/resources/css/app.css` `@theme`)

| Token | Value | Notes |
|---|---|---|
| `--font-display` | Archivo | headings; tracking −0.025/−0.03em |
| `--font-sans` | Instrument Sans | body + Filament panels |
| `--font-mono` | JetBrains Mono | prices, labels, table headers, meta |
| `--color-paper` | `#FBFAF8` | page bg, warm, never pure white |
| `--color-paper-deep` | `#F4F2EC` | recessed surfaces |
| `--color-card` | `#FFFFFF` | cards/boards/receipts |
| `--color-ink` | `#111827` | headings, dark surfaces, panel sidebar |
| `--color-ink-soft` | `#4B5563` | body text |
| `--color-ink-faint` | `#98A0AB` | meta, placeholders |
| `--color-line` | `#E7E4DD` | hairlines |
| `--color-line-strong` | `#D8D4CA` | card borders |
| `--color-accent` | `#4F46E5` | THE accent (indigo), sparing |
| `--color-accent-deep` | `#4338CA` | hover |
| `--color-accent-soft` | `#EEF2FF` | tints, ON chips |
| `--color-flow` | `#38BDF8` | sky — only inside dark Flow bands |
| `--color-flow-bg` | `#0E1320` | Flow band / auth panel bg (≠ ink) |

**Radii**: buttons 10px (12 lg / 8 sm) · cards 14–16px · tiles 12px · inputs 10px · kickers 7px · receipt 4px top only. **Type scale**: hero h1 62px/1.02 · section h2 42px/1.06 · lede 16.5–18px/1.65 · mono meta 11–12px. **Shadows**: cards `0 1px 2px rgba(17,24,39,0.04)`; elevated `+0 28px 56px -28px rgba(17,24,39,0.22)`; receipt `0 20px 40px -20px rgba(17,24,39,0.25)`.

**Backgrounds — bloom, never grids**: `.bg-bloom` (indigo radial top + paper-deep fade) on light heroes/sections; `.bg-bloom-accent` (white TL + sky BR glows) on indigo CTA bands. The original graph-paper texture is retired everywhere.

**Domain colors** (17): hr `#8B5CF6` finance `#10B981` crm `#F43F5E` projects `#6366F1` comms `#3B82F6` support `#F97316` dms `#64748B` marketing `#EC4899` operations `#FB923C` analytics `#38BDF8` it `#06B6D4` legal `#F59E0B` ecommerce `#14B8A6` lms `#22C55E` ai `#818CF8` workplace `#84CC16` events `#FB7185`. Always 9–11px squares with 3px radius — never circles on light surfaces. Source of truth in code: `resources/js/data/marketing.ts`.

---

## Component Library (Vue — `resources/js/Components/`)

| Component | File | Spec essentials |
|---|---|---|
| Switch | `UI/Switch.vue` | 38×22 (sm 32×19); off `#E3E0D8`, on accent; white knob `0 1px 3px` shadow; `interactive` prop emits `toggle`. THE signature control |
| Kicker | `Marketing/Kicker.vue` | mono 11.5px uppercase 0.18em indigo, 8px square, card bg, 7px radius |
| SectionTag | `Marketing/SectionTag.vue` | mono `01 / LABEL`; `dark` prop for Flow bands |
| Switchboard | `Marketing/Switchboard.vue` | zebra rows (odd `#FAF9F5`), domain square + name + mono price + Switch; ink total strip; OFF rows 45% opacity; emits `toggle` |
| BlueprintCell | `Marketing/BlueprintCell.vue` | white cell in 1px-gap grid over line-strong; 14px accent corner tick; mono 44px number |
| ModuleTile | `Marketing/ModuleTile.vue` | chip 22px + ON/OFF state pill; OFF = dashed border transparent; `ghost` variant |
| Receipt | `Marketing/Receipt.vue` | all-mono 13px, dashed separators, CSS sawtooth bottom (`.receipt-edge`) |
| FlowBand | `Marketing/FlowBand.vue` | bg flow-bg + radial glow + gradient spine; glowing nodes alternate indigo/sky; route column optional (drops when flows lack from/to) |
| ReplacesStrip | `Marketing/ReplacesStrip.vue` | sticky mono REPLACES label + 34s marquee, indigo strikethrough |
| CtaBand | `Marketing/CtaBand.vue` | accent bg + `.bg-bloom-accent` overlay, centered 50px h2, white button |
| DomainPill | `Marketing/DomainPill.vue` | pill + domain square; `dashed` = upcoming |
| LegalPage | `Marketing/LegalPage.vue` | sticky TOC w/ scrollspy + short-version box |
| BaseButton | `UI/BaseButton.vue` | primary=indigo+glow · dark=ink · secondary=outline · ghost; sizes sm/md/lg; `active:scale-[0.98]` |
| Form fields | `Form/*` | 13.5px labels, 10px radius, line-strong border, focus = accent + `0 0 0 3px rgba(79,70,229,0.15)` ring |

Static content data: `resources/js/data/marketing.ts` (domains, colors, flows, replaces, euro helpers) + `app/Support/Marketing/MarketingContent.php` (changelog, help articles, case studies — server-side).

## Page Inventory

See [[_index]] route table. Auth: `AuthLayout.vue` `split` prop — split dark panel (3 SVG flow pulses, `stroke-dasharray 26 200`, 4.5s staggered) for login/invite; centered for forgot/reset. Forgot-link sits BELOW password input (tab order rule).

---

## Filament Panel Skin

One shared file: `resources/css/filament/flowflex-skin.css`, imported by every thin per-panel `theme.css` (Google fonts import first, vendor theme, skin, `@source` globs, `--ff-panel-label`). Accent rides Filament's `--primary-*` vars — never hardcode a domain color in the skin.

Key rules: **scheme-following sidebar** (owner decision 2026-07-03 — light card surface in light mode, ink rail in dark; previously 'ink both modes'), mono panel label, active = 2px primary spine + ~15% tint, warm topbar, paper canvas, mono table headers + zebra rows, underline tabs, 30px pagination squares, blueprint-cell stat widgets, 9px buttons, mono infolist labels, login parity (bloom bg, indigo customer / ink staff buttons, `/ADMIN` badge), Spotlight `.ff-spotlight-*`, empty/selected/wizard states.

⚠️ **Selectors must be verified against rendered Filament 5 markup** — v3-era names fail silently ([[../architecture/filament-patterns]] item 16). Spotlight: [[../architecture/filament-patterns]] item 14.

## Motion

Scroll-reveal `Reveal.vue` (0.5s cubic-bezier(0,0,0.2,1), translateY 14px) · marquee 34s linear · pulse-dash 4.5s linear staggered · entrances ease-out 0.18–0.3s · press `scale(0.97–0.98)` instant. All decorative motion gated behind `prefers-reduced-motion`.

## Copy Rules

Sentence case everywhere · no exclamation marks · "you/your" · active voice · mono for numbers/dates/meta · errors human, never log-file ([[../architecture/patterns/ux-states]]).

## Related

[[../product/brand]] · [[../product/ux-principles]] · [[../architecture/filament-patterns]] · [[../architecture/patterns/ux-states]] · [[../architecture/patterns/perceived-performance]] · ADR [[../build/decisions/decision-2026-06-12-switchboard-plus-design-system]]
