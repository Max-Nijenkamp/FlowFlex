# Handoff: FlowFlex Public Site + Auth + Panel Skin ("Switchboard+")

## Overview
Complete redesign of the FlowFlex public-facing surface: all marketing pages, auth screens, and a brand skin for the Filament app panels. The design system is called **Switchboard+** — the per-user-per-module business model made visible: modules are literal switches, invoices are receipts, stats live in blueprint cells, and cross-domain data flow is shown as dark "Flow" bands with animated pulse lines.

## About the Design Files
The files in this bundle are **design references created in HTML/JSX** — pixel-accurate prototypes showing intended look and behavior, **not production code to copy directly**. The task is to **recreate these designs in the existing FlowFlex codebase**:

- **Marketing + auth pages** → Vue 3.5 + TypeScript + Inertia.js v2 + Tailwind CSS v4 (`resources/js/Pages/Marketing/*`, `resources/js/Pages/Auth/*`, layouts in `resources/js/Components/Layout/`)
- **Panel screens** → Filament panel theme CSS (`resources/css/filament/*`) using Filament's native components; the two panel mockups define the *visual target*, not a rebuild of Filament

Follow the codebase's established conventions (see `vault/frontend/_index.md`): Tailwind utilities only, `<Link>` for navigation, `useForm` for forms, layouts via `defineOptions({ layout: ... })`, components in `Components/UI` + `Components/Marketing`.

## Fidelity
**High-fidelity.** Colors, type, spacing, copy and states are final. Recreate pixel-perfectly. The only intentionally-approximate parts: the design mocks render interactive elements (switches, sliders, accordions) as static states — implement them as real interactive components with the states shown.

## Viewing the designs
Open `FlowFlex Site Design.html` in a browser. It is a pan/zoom canvas with 4 sections: Public site desktop (7 pages), mobile (2 pages), Auth (4 screens), Filament panels (2 screens). In the browser console, `window.__renderPage('home')` renders a single page full-width (ids: home, pricing, product, domain, about, contact, legal, home-m, pricing-m, login, admin, invite, forgot, dash, crud).

---

## Design Tokens

Add to `resources/css/app.css` `@theme` (extends the existing token block; ink/paper/line/accent values are unchanged from the current site):

```css
@theme {
    --font-display: 'Archivo', ui-sans-serif, system-ui, sans-serif;       /* headings + wordmark contexts */
    --font-sans: 'Instrument Sans', ui-sans-serif, system-ui, sans-serif;  /* body (unchanged) */
    --font-mono: 'JetBrains Mono', ui-monospace, monospace;                /* data, labels, prices */

    --color-paper: #FBFAF8;        /* page background (warm, never pure white) */
    --color-paper-deep: #F4F2EC;   /* recessed surfaces */
    --color-card: #FFFFFF;         /* cards, boards, receipts */
    --color-ink: #111827;          /* headings, footer/nav-dark bg */
    --color-ink-soft: #4B5563;     /* body text */
    --color-ink-faint: #98A0AB;    /* meta text, placeholders */
    --color-line: #E7E4DD;         /* hairlines (warm) */
    --color-line-strong: #D8D4CA;  /* card borders */
    --color-accent: #4F46E5;       /* THE accent — indigo, used sparingly */
    --color-accent-soft: #EEF2FF;
    --color-flow: #38BDF8;         /* sky — secondary highlight inside dark Flow bands only */
    --color-flow-bg: #0E1320;      /* dark Flow band background (NOT the same as ink) */
}
```

Load fonts: Archivo (400/500/600/700/800), Instrument Sans (400/500/600/700), JetBrains Mono (400/500/700) — Google Fonts or self-hosted.

**Domain colors** (functional, from `vault/product/ux-principles.md` panel table): hr `#8B5CF6`, finance `#10B981`, crm `#F43F5E`, projects `#6366F1`, comms `#3B82F6`, support `#F97316`, dms `#64748B`, marketing `#EC4899`, operations `#FB923C`, analytics `#38BDF8`, it `#06B6D4`, legal `#F59E0B`, ecommerce `#14B8A6`, lms `#22C55E`, ai `#818CF8`, workplace `#84CC16`, events `#FB7185`. Used as 10–11px squares with `border-radius: 3px` (never circles on light surfaces).

**Other token values**
- Radii: buttons 10px (12px lg, 8–9px sm) · cards/boards 14–16px · tiles 12px · inputs 10px · kickers 7px · receipt 4px top only
- Display tracking: `-0.025em` to `-0.03em` on Archivo headings
- Type scale (desktop): h1 hero 62px/1.02 · section h2 42px/1.06 · card h3 16px · body 15–16px · lede 16.5–18px/1.65 · mono meta 11–12px
- Shadows: cards `0 1px 2px rgba(17,24,39,0.04)`; elevated boards add `0 28px 56px -28px rgba(17,24,39,0.22)`; receipt `0 20px 40px -20px rgba(17,24,39,0.25)`
- Graph-paper texture (heroes + accent sections): two `linear-gradient` lines `rgba(17,24,39,0.04)`, `background-size: 32px 32px`
- Section padding: 104px desktop / 68px mobile; sections separated by 1px `--color-line` borders

---

## Core Components (build once in `Components/UI` / `Components/Marketing`)

1. **Switch** (`ff-sw`): 38×22px pill (sm: 32×19), off `#E3E0D8`, on `--color-accent`, white 18px knob with `0 1px 3px rgba(17,24,39,0.3)` shadow. This is the system's signature control — used in boards, tiles, pricing rows, even About-page bullets.
2. **Kicker** (`ff-kicker`): mono 11.5px uppercase tracking 0.18em indigo, 8px indigo square, white card bg, 1px line border, 7px radius. Replaces the old `.section-index`.
3. **Section tag** (`ff-tag`): mono `01 / FLEX` — number indigo bold, label faint, tracking 0.2em.
4. **Switchboard** (`ff-board`): card with header row, zebra rows (odd `#FAF9F5`), each row = domain square + name + mono price + Switch; footer strip = ink bg, mono formula left, big mono total right. OFF rows at 45% opacity.
5. **Blueprint stat cell** (`ff-cell`): white card in a 1px-gap grid over `--color-line-strong`; 14px indigo corner tick (top-left, 2px borders); mono 44px number, 16px heading, 14px body.
6. **Module tile** (`ff-tile`): 12px radius card; header = 22px domain-color chip (6px radius, white 8px square inside) + ON/OFF state pill (mono 9.5px; ON = accent-soft/indigo, OFF = `#EFEDE7`/faint); name 14px semibold; mono price. OFF tiles: dashed border, transparent bg. Ghost tile: dashed, centered faint label.
7. **Receipt** (`ff-receipt`): white card, 4px top radius, zigzag bottom edge (CSS gradient sawtooth), all-mono 13px; dashed separators; `FLOWFLEX · MONTHLY` letterspaced title; total row 16px bold.
8. **Dark Flow band** (`ff-flow`): bg `--color-flow-bg`, radial indigo glow top-center, vertical gradient line (transparent → indigo → sky → transparent) at the node column; rows = mono sky route label (right-aligned, 200px col) + glowing 11px node dot (alternating indigo/sky border + glow) + Archivo 17px event + 14.5px effect at 62% white.
9. **Replaces strip** (`ff-strip`): white band, sticky mono `REPLACES` label with fade, marquee (34s linear infinite, translateX -50%) of competitor names with indigo strikethrough.
10. **CTA band** (`ff-band`): accent bg + white 6%-opacity graph grid overlay, centered 50px h2, white button.
11. **Buttons**: primary = accent bg + glow shadow; default = ink bg; outline = white + `--color-line-strong` border. Hover: existing site convention (ink→accent transition, `active:scale-[0.98]`).
12. **Form field**: 13.5px semibold label (+ faint "optional" right-aligned), input 10px radius white card border `--color-line-strong`, focus = accent border + `0 0 0 3px rgba(79,70,229,0.15)` ring.

---

## Screens

### 1. Home (`Pages/Marketing/Home.vue`) — artboard `home`, mobile `home-m`
- **Hero** (graph-paper bg): 2-col grid `1.05fr 1fr` gap 64. Left: kicker "PER USER · PER MODULE", h1 62px "Run everything. / Pay for what's *switched on*." (underline = `inset 0 -0.16em 0 #C7D2FE` box-shadow), lede, primary + outline CTAs, mono meta line. Right: **Switchboard** with 7 modules (profiles/leave/invoicing/pipeline ON, payroll/expenses/projects OFF), total `€5,00/user × 80 users = €400/month`.
- **Replaces strip**.
- **01 / The patchwork tax** (white bg): h2 "Twelve tools, one company, and nothing talks to anything." + 3 blueprint cells: `5–15` separate tools / `×5` forms per hire / `0` integrations.
- **02 / Flex** (graph bg): "Modules are switches, not sales calls." + 8 module tiles (4 ON, 3 OFF, 1 ghost "+ 65 more modules").
- **03 / Flow** — dark Flow band, 6 chains (deal won→invoice, invoice paid→LTV, offer→payroll, leave→scheduling, tickets→health score, course→profile).
- **04 / Coverage** (white): 12-row domain table (zebra, domain square, name, mono count, `explore →`), footnote "+ 4 more departments".
- **05 / Pricing teaser** (graph bg): copy left + receipt right (rotate 0.6deg).
- **CTA band** "Switch on what you need. Nothing else." + ink footer (4-col, mono headers).
- **Mobile (390px)**: same order, hero stacked (board below CTAs, 5 rows), tiles 2-col, chain rows lose route labels (line moves to 8px left), cells stack.

### 2. Pricing (`Pages/Marketing/Pricing.vue`) — `pricing`, mobile `pricing-m`
- Hero: kicker PRICING, h1 "No tiers. No bundles. / One formula." (second line indigo), formula chip (ink bg, mono 15px: `invoice = Σ(module price) × active users` — Σ-part `#A5A3FF`, users sky).
- **Calculator** (white section, grid `1fr 380px` gap 48): left = 4 domain group cards (Core platform "always on, always free" with locked rows; HR & people expanded showing 6 module toggle-rows in 2-col grid — selected rows accent-soft bg + accent border; Finance; CRM collapsed with "N on" pill + mono subtotal). Right = **sticky receipt**: team-size slider (80 people, range 10–500), module lines, total `€5,00 × 80 = €400`, full-width "Talk to us" button, footnote. Wire to the existing props (`modules`, `base_price_cents`) and reactive logic already in the current Pricing.vue.
- **02 / Fair print**: h2 "The fine print, minus the fine." + 6 two-column FAQ rows.
- CTA band "Your number is one minute away."

### 3. Product (`Pages/Marketing/Features.vue`) — `product`
- Hero: "Four departments today. / The rest is *already wired*."
- 4 domain sections (alternating white/paper): left = `0N / DOMAIN` tag, h2 with 14px domain square, description, `Explore →` link, "FLOWS AUTOMATICALLY" mono label + bullet rows (7px domain squares); right = 2-col module mini-cards (name + sm Switch + mono price).
- **05 / Next in line** (graph bg): "Waiting on the switchboard." + dashed domain pills with mono "soon".
- CTA band "Only pay for the rows you need."

### 4. Domain detail (`Pages/Marketing/Domain.vue`) — `domain` (HR example)
- Breadcrumb `Product / HR & people`; h1 with 18px violet square; lede; primary CTA "Price these modules" + arrow link.
- **01 / Modules** (white): 3-col detailed tiles (chip, ON/OFF, name 15.5px, 13.5px description, mono price).
- **02 / Flow band** (HR-specific chains: offer→payroll, leave→scheduling, onboarding→IT, course→profile).
- **03 / Plays well with**: domain pills (finance/projects/lms/it).
- CTA band "Start with HR. Grow from there."

### 5. About (`Pages/Marketing/About.vue`) — `about`
- Hero h1 56px "Growing companies shouldn't need *fifteen tools* to run one business." + two story paragraphs (17.5px/1.7).
- **01 / Values**: 3×2 blueprint cells (5 values + logo cell).
- **02 / Is / isn't**: rows with ON-switch + statement left, strikethrough anti-statement right.
- Trust strip (mono, centered, dot-separated) + CTA band "Talk to the team."

### 6. Contact (`Pages/Marketing/Contact.vue`) — `contact`
- Single section, grid `1fr 1.15fr` gap 72 on graph bg. Left: kicker, h1 "Talk to us.", lede, 2 info cards (first has indigo corner tick). Right: form card (20px radius, 40px padding, elevated): Name + Work email 2-col, Company size select, message textarea with hint, full-width primary button, footnote "No newsletter, no drip campaign — just a reply." Keep the existing `useForm` + honeypot logic.

### 7. Terms (`Pages/Marketing/Terms.vue`, mirror for Privacy) — `legal`
- White, grid `260px 1fr` gap 72: sticky left TOC (kicker LEGAL + items with 2px left borders, active indigo); right: h1 44px, mono updated-line, **"The short version"** summary box (accent-soft bg, accent 25% border), then h3+paragraph sections (15px/1.75).

### 8–11. Auth (`Pages/Auth/*`, layout `Components/Layout/AuthLayout.vue`) — `login`, `admin`, `invite`, `forgot`
- **Split shell** (login, invite, admin): left 620px dark panel `#0E1320` with logo (white wordmark variant), radial indigo glow, 3 animated bezier "flow pulse" lines (1.5px; rails `rgba(255,255,255,0.07)`; pulses alternate indigo/sky, `stroke-dasharray: 26 200`, dashoffset animation ~4.5s linear infinite, staggered delays), bottom-left display text + mono trust line. Right: graph-paper paper bg, centered 420px form card.
- **Customer login**: "Sign in to FlowFlex / Welcome back.", email, password (+"Forgot it?" indigo right-aligned in label row), checked "Keep me signed in", primary button, invite-only footnote.
- **Admin login**: left panel shows mono `FLOWFLEX STAFF · /ADMIN` + "Platform operations."; card has `/ADMIN` ink badge, "FlowFlex employees only. All sessions are audited.", email, password, **6-box 2FA input** (48×52px mono boxes, active box accent border + focus ring), ink (not indigo) button, security footnote. Visual rule: staff surfaces use ink buttons, customer surfaces indigo.
- **Invite register** (`/register/invite/{token}`): kicker "YOU'RE INVITED", "Join Veldkamp Logistics" (workspace name from props), inviter sentence, **locked email field** (paper-deep bg + lock icon), name, password + 4-segment strength meter (green "strong"), "Create account & join".
- **Forgot password**: no split — centered logo, 440px card, email, primary button, "Back to sign in" link, mono trust footnote.

### 12–13. Filament panels (`resources/css/filament/*` themes) — `dash`, `crud`
Implement as Filament theme CSS + native components. Visual targets:
- **Sidebar**: bg `#111827` (all panels, per ux-principles.md), 248px; logo top; mono panel label `HR & PEOPLE · /HR`; nav groups (10.5px uppercase faint); items 13.5px at 62% white; **active item = 2px domain-color left border + domain-color 16% bg + white text** (HR violet `#8B5CF6`); bottom: panel-switcher chips (26px mono squares, active outlined violet) + user card.
- **Topbar** (60px, paper): breadcrumb, 320px search with `⌘K` kbd hint, bell with violet ping dot, avatar.
- **Content bg `--color-paper`** (warm — override Filament gray), cards white with `--color-line-strong` borders, 12–14px radius.
- **Dashboard widgets**: KPI cells with violet corner ticks + mono 27px values; "Leave requests · awaiting approval" list (avatar, name/role, mono date range, green Approve `#10B981` + ghost Deny) with sky footnote strip "Approvals flow to scheduling automatically"; "Out this week" 5-day mini calendar (tinted name chips); "Onboarding in progress" rows with violet progress bars; "Recent activity" audit feed (mono timestamps, domain-color squares).
- **CRUD index (Employees)**: title + meta, Export ghost + violet "+ New employee"; status tabs with mono count chips (active = 2px violet underline); violet-tinted bulk bar ("1 selected · Assign to department · Export · Deactivate"); table — mono 10px uppercase letterspaced headers, zebra rows, avatar+name+email, role, department badge (white, domain square), status pills (Active green / On leave amber / Onboarding violet / Offboarding gray — 999px, dot + label), mono start dates, kebab; footer pagination (30px squares, active violet).

## Interactions & Behavior
- Scroll-reveal: keep the existing `Reveal.vue` pattern (`cubic-bezier(0,0,0.2,1)`, 0.5s, translateY 14px, reduced-motion safe) on section content.
- Marquee: 34s linear infinite, duplicate content, `translateX(-50%)`; pause not required.
- Flow-band pulses: SVG `stroke-dashoffset` keyframes, 4.5s linear infinite, staggered 1.1–1.4s delays. Gate decorative animation on `prefers-reduced-motion: no-preference`.
- Switches/tiles/pricing rows: optimistic local state (existing pattern in Pricing.vue/Home.vue); selecting updates receipt totals reactively.
- Buttons: `transition ease-out duration-150`, `active:scale-[0.98]`.
- Copy rules (vault/product/brand.md): sentence case everywhere, no exclamation marks, "you/your", active voice.

## Assets
- Logos already in repo: `public/images/logo/flowflex-logo-dark.svg`, `flowflex-logo-light.svg`, `flowflex-icon.svg` (copies in `assets/` here). Never recolor/recreate; wordmark min 120px, icon min 24px.
- No photography. All visuals are typographic/structural.

## Files in this bundle
- `FlowFlex Site Design.html` — canvas with all 15 artboards (open in browser)
- `system/ff.css` — the design system CSS (tokens + all marketing components; class-per-component, maps ~1:1 to the component list above)
- `system/shared.jsx` — shared nav/footer/flow-band/switch components
- `pages/home.jsx`, `pages/pricing.jsx`, `pages/product.jsx`, `pages/company.jsx`, `pages/auth.jsx` — per-screen structures + final copy
- `panel/panel.css`, `panel/panel.jsx` — Filament skin target
- `directions/data.js` — content data (modules, prices, domains, flows, competitor names)
- `lib/design-canvas.jsx`, `assets/*` — canvas viewer + logos (reference only)
