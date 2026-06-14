---
type: product
category: brand
color: "#38BDF8"
---

# Brand Foundation

Single source of truth for FlowFlex brand identity. This file wins on conflicts.

---

## Identity

**Name**: FlowFlex

**Primary tagline**: "Everything flows."

**Long-form tagline**: "One platform. Every tool. Always flexible."

**What FlowFlex is**: A single SaaS platform giving businesses every operational tool in one place — HR, Projects, Finance, CRM, and more — each as a module activated when needed, deactivated when not. No app-switching, no re-entering data, no disconnected silos.

Built for SMEs and mid-market companies with 50–500 employees who have outgrown a patchwork of separate tools.

---

## The Two-Word Brand Promise

**Flow** — Effortless. Fast, clean UI, no friction. Tasks that took three tools now take one. The next step is always obvious, feedback is immediate, nothing blocks the actual job.

**Flex** — Adapts to the business. Modules activated one by one as the company grows. A 20-person startup uses 3 modules. A 200-person company uses 15. No enterprise bloat, no forced upgrades.

---

## Brand Values

| Value | What it means |
|---|---|
| Flexible by design | Activate modules one at a time; pay only for what is active |
| Flow-first UX | Speed and clarity on every screen; keyboard shortcuts; consistent patterns |
| Unified truth | One database, one login; data flows automatically between domains |
| Transparent and fair | Clear pricing, no hidden modules, GDPR-compliant, data portability always |
| Customer-first | Built for department managers and employees, not the IT department |

---

## Brand Personality

| Attribute | FlowFlex IS | FlowFlex is NOT |
|---|---|---|
| Tone | Friendly, direct, confident | Corporate-speak, jargon-heavy, cold |
| Visual | Clean, spacious, modern | Busy, cluttered, dated |
| Complexity | Simple surface, powerful underneath | Complex, intimidating, enterprise-heavy |
| Speed | Fast, responsive, immediate | Slow, spinner-first, lagging |
| Pricing | Transparent, modular, fair | Opaque, lock-in-heavy |

**Not an ERP** — FlowFlex does not impose a rigid process model. Every domain is independent.  
**Not enterprise-only** — serves a 50-person company as well as a 500-person company.  
**Not a feature factory** — every addition must serve Flow or Flex.

---

## Voice and Tone

FlowFlex speaks like a capable colleague who respects the user's time.

| Situation | Good | Avoid |
|---|---|---|
| Success | "Invoice sent." | "Your invoice has been successfully sent!" |
| Error | "Couldn't save. Check your connection and try again." | "An unexpected error has occurred." |
| Empty state | "No employees yet. Add your first team member." | "No records found." |
| Destructive | "This will permanently delete 3 records. You cannot undo this." | "Are you sure you want to proceed?" |
| Validation | "Email address is missing." | "This field is required." |

**Writing rules**:
- Use "you" / "your" — never "the user" or "the account"
- Active voice: "Add an employee" not "An employee can be added"
- No exclamation marks inside the app
- Sentence case for all UI labels, headings, button text
- Avoid: "Please", "Sorry", "Unfortunately", "Invalid", "Failed" without context
- Numbers and dates: always use company locale setting

---

## Logo

**Primary mark**: "FlowFlex" wordmark + icon. Icon concept: infinity-loop / flow symbol that subtly forms an "F".

**Colours**:
- Dark version: wordmark in `#111827` + icon in `#4F46E5` — for light backgrounds
- Light version: wordmark in white + icon in white — for dark/brand-primary backgrounds

**File locations**:
- `public/images/logo/flowflex-logo-dark.svg`
- `public/images/logo/flowflex-logo-light.svg`
- `public/images/logo/flowflex-icon.svg`
- `public/images/logo/flowflex-favicon.png` *(deviation 2026-06-11: SVG favicon shipped instead — flowflex-icon.svg serves as favicon on site + panels; PNG pending image tooling)*

**Rules**: Never stretch, rotate, recolour. Never recreate in CSS. Minimum 120px wide for wordmark, 24px for icon-only.

**Status (2026-06-11)**: all SVGs created and live — public site header/footer (dark/light), auth pages (icon mark), all 5 Filament panels via native brandLogo APIs, favicon on site + panels.

**Panel rule (2026-06-12)**: panel sidebars are ink `#111827` in both light and dark mode — panels always use the **light** wordmark (`->brandLogo(flowflex-logo-light.svg)`).

---

## Visual Identity — "Switchboard+" (2026-06-12)

The design system across public site, auth and panel skins. Source of truth for implementation detail: `design_handoff_flowflex_site/README.md` + ADR [[../build/decisions/decision-2026-06-12-switchboard-plus-design-system|Switchboard+]]. The idea: the per-user-per-module business model made visible — **modules are literal switches, invoices are receipts, stats live in blueprint cells, cross-domain flow is a dark band with pulse lines.**

### Typography

| Role | Face | Usage |
|---|---|---|
| Display | **Archivo** (500–800) | Headings, wordmark contexts; tracking −0.025 to −0.03em |
| Body | **Instrument Sans** (400–700) | All body text, also the Filament panel font |
| Data | **JetBrains Mono** (400–700) | Prices, labels, table headers, kickers, meta lines |

### Palette (CSS tokens in `resources/css/app.css`)

| Token | Hex | Role |
|---|---|---|
| `paper` | `#FBFAF8` | Page background — warm, never pure white |
| `paper-deep` | `#F4F2EC` | Recessed surfaces |
| `card` | `#FFFFFF` | Cards, boards, receipts |
| `ink` | `#111827` | Headings, footer/nav-dark/sidebar bg |
| `ink-soft` / `ink-faint` | `#4B5563` / `#98A0AB` | Body / meta text |
| `line` / `line-strong` | `#E7E4DD` / `#D8D4CA` | Hairlines / card borders |
| `accent` | `#4F46E5` | THE accent — indigo, used sparingly |
| `accent-soft` | `#EEF2FF` | Tints, ON-state chips |
| `flow` | `#38BDF8` | Sky — secondary highlight inside dark Flow bands only |
| `flow-bg` | `#0E1320` | Dark Flow band background (NOT the same as ink) |

Domain colors (17, functional): see panel table in [[ux-principles]] — rendered as 10–11px squares with 3px radius, never circles on light surfaces.

### Backgrounds — bloom, not grids (2026-06-12)

No graph-paper/grid textures anywhere. Light heroes/sections use the **bloom**: soft indigo radial at top + paper-deep fade (`.bg-bloom`). Indigo CTA bands use two glows — white top-left, sky bottom-right (`.bg-bloom-accent`).

### Signature components

Switch (38×22 pill, the system's signature control), Kicker (mono uppercase chip + 8px indigo square), Switchboard (zebra rows + ink total strip), Blueprint stat cell (indigo corner tick + mono number), Module tile (ON/OFF state pill; OFF = dashed), Receipt (mono, sawtooth bottom edge), dark Flow band (glowing nodes + route labels), Replaces strip (strikethrough marquee). Vue implementations in `resources/js/Components/Marketing/`.

### Buttons

Primary = indigo + glow shadow · dark = ink (staff surfaces use ink, customer surfaces indigo) · outline = white + line-strong border. Radii 10px (12 lg / 8 sm) — no pills. Press feedback `active:scale-[0.98]` always.
