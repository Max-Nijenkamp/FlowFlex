---
type: design
section: design-system
last_updated: 2026-05-13
status: in-progress
right_brain_log: "[[builder-log-ui-theme-overhaul]]"
---

# Brand Foundation

The single source of truth for all FlowFlex branding. Every other design-system file references back here. If there is a conflict between this file and any other file, this file wins.

---

## 1. Brand Identity

**Name**: FlowFlex

**Taglines**:
- Primary: "Everything flows."
- Long form: "One platform. Every tool. Always flexible."

**What FlowFlex is**

FlowFlex is a single SaaS platform that gives businesses every operational tool in one place — HR, Projects, Finance, CRM, and 28 more domains — each as a module that can be turned on when the business needs it and turned off when it doesn't. No app-switching, no re-entering data, no disconnected silos.

FlowFlex is built for SMEs and mid-market companies (50–500 employees) who are outgrowing their patchwork of separate tools and need one coherent system that scales with them.

**The two-word brand promise**

**Flow** — The platform feels effortless. Navigation is fast, UI is clean, workflows have no friction. Tasks that took three tools now take one. Users don't have to think about the software — it gets out of the way. Every interaction is designed so the next step is obvious, the feedback is immediate, and nothing blocks the person from doing their actual job.

**Flex** — The platform adapts to the business, not the other way round. Modules are activated one by one as a company grows. A 20-person startup uses 3 modules. A 200-person company uses 15. Price and complexity scale with actual need — there is no "enterprise bloat" baked in from day one, and no forced upgrades to unlock features that should have been standard.

---

## 2. Brand Values

### 1. Flexible by design

Modular architecture means companies pay for what they use and activate what they need.

In practice: every feature is a module, every module is optional, the workspace panel only shows what's active. A company can start with HR and Payroll, add Finance six months later, and turn on CRM when they hire their first sales team — without migrating, without retraining, without switching vendors.

### 2. Flow-first UX

Every screen is designed for speed and clarity.

In practice: one-click navigation between domains, zero loading spinners for common actions (optimistic UI), keyboard shortcuts throughout, consistent interaction patterns so users never have to learn a new paradigm inside the same product. The measure of success for any screen is how few seconds it takes a new user to complete the primary action.

### 3. Unified truth

One database, one login, one data model.

In practice: an employee hired in HR automatically appears in Payroll, IT provisioning, and LMS — no re-entry, no CSV exports, no API integrations to maintain. A deal closed in CRM creates the invoice stub in Finance. A purchase order approved in Operations creates the bill in Finance. The data flows because the modules share the same source of truth.

### 4. Transparent and fair

Clear pricing, no hidden modules, data portability on request, GDPR-compliant from day one.

In practice: companies can export their full dataset at any time, cancel any time, and always own their data. Pricing is shown as a module-by-module breakdown — there is no opaque "bundle" that hides the cost of things companies don't need. FlowFlex earns trust by not requiring it.

### 5. Customer-first always

Built for the people using it — department managers, employees — not the IT department.

In practice: onboarding wizard on first login, contextual help inline rather than in a separate knowledge base, no jargon in the UI (not "entity" — "company"; not "record" — "employee"), support from real humans not a chatbot labyrinth. Complexity is hidden behind the interface, not loaded onto the user.

---

## 3. Brand Personality

| Attribute | FlowFlex IS | FlowFlex is NOT |
|---|---|---|
| Tone | Friendly, direct, confident | Corporate-speak, jargon-heavy, cold |
| Visual | Clean, spacious, modern | Busy, cluttered, dated |
| Complexity | Simple on the surface, powerful underneath | Complex, intimidating, enterprise-heavy |
| Speed | Fast, responsive, immediate feedback | Slow, loading-spinner-first, lagging |
| Support | Human, helpful, proactive | Robotic, FAQ-only, defensive |
| Pricing | Transparent, modular, fair | Opaque, lock-in-heavy, punishing |
| Ambition | Replacing ten tools with one | Being another tool to add to the stack |

---

## 4. Logo

**Primary mark**: "FlowFlex" wordmark + icon

**Icon concept**: An infinity-loop / flow symbol that subtly forms an "F" — representing continuous flow and the flexibility to adapt. The loop has no sharp corners: everything curves, everything connects. It is not a generic icon — it is identifiably FlowFlex.

**Logo versions**:
- Primary (dark): wordmark in `#111827` (Gray-900) + icon in `#4F46E5` (Indigo-600) — for white and light backgrounds
- Reversed (light): wordmark in white + icon in white — for brand-primary (`#4F46E5`) or dark backgrounds
- Icon-only: used for favicon, mobile app icon, loading states, and avatar placeholders

**Logo usage rules**:
- Minimum size: 120px wide for the full wordmark; 24px for icon-only
- Clear space: minimum 1× icon height on all four sides — no other elements inside this zone
- Never stretch, rotate, skew, or recolour the wordmark
- Never place the logo on a busy photographic background without a backing shape (solid colour block or frosted overlay)
- Never use a domain colour as the logo background — the logo always sits on brand-primary, white, or a dark neutral
- Never recreate the logo in CSS or with text characters — always use the SVG asset

**File locations** (once assets exist):
- `public/images/logo/flowflex-logo-dark.svg` — full wordmark for light backgrounds
- `public/images/logo/flowflex-logo-light.svg` — full wordmark for dark backgrounds
- `public/images/logo/flowflex-icon.svg` — icon-only mark
- `public/images/logo/flowflex-favicon.png` — 32×32 and 64×64 rasterised versions

---

## 5. Platform Colour Palette

The FlowFlex colour system has two distinct layers:

1. **Platform colours** — used for the FlowFlex brand itself: marketing site, admin shell, primary CTAs, focus rings, and system-level feedback states. These do not belong to any domain.
2. **Domain colours** — each of the 31 business domains has a fixed unique colour for instant recognition in the workspace navigation panel, domain badges, and dashboard widgets.

### Platform semantic colours

Implemented as Tailwind v4 CSS custom properties (declared in `resources/css/app.css`).

| Token | Hex value | Tailwind reference | Usage |
|---|---|---|---|
| `--color-brand-primary` | `#4F46E5` | Indigo-600 | Primary CTA buttons, active nav indicator, links, focus rings |
| `--color-brand-primary-hover` | `#4338CA` | Indigo-700 | Hover state on primary elements |
| `--color-brand-primary-light` | `#EEF2FF` | Indigo-50 | Backgrounds behind primary-coloured elements, badges |
| `--color-brand-accent` | `#7C3AED` | Violet-600 | AI features, premium tier highlights, marketing accent colour |
| `--color-brand-accent-light` | `#F5F3FF` | Violet-50 | Accent backgrounds |
| `--color-neutral-900` | `#111827` | Gray-900 | Primary text, headings, icon fill on light backgrounds |
| `--color-neutral-700` | `#374151` | Gray-700 | Secondary text, form labels, muted headings |
| `--color-neutral-400` | `#9CA3AF` | Gray-400 | Placeholder text, disabled states, subtle borders |
| `--color-neutral-200` | `#E5E7EB` | Gray-200 | Dividers, input borders (default state) |
| `--color-neutral-100` | `#F3F4F6` | Gray-100 | Subtle section backgrounds, table zebra rows |
| `--color-neutral-50` | `#F9FAFB` | Gray-50 | Page background |
| `--color-success` | `#059669` | Emerald-600 | Success states, approved indicators, positive trends |
| `--color-success-light` | `#D1FAE5` | Emerald-100 | Success message backgrounds, approved row highlights |
| `--color-warning` | `#D97706` | Amber-600 | Warnings, pending states, items needing attention |
| `--color-warning-light` | `#FEF3C7` | Amber-100 | Warning message backgrounds, pending row highlights |
| `--color-danger` | `#DC2626` | Red-600 | Errors, destructive actions, overdue indicators |
| `--color-danger-light` | `#FEE2E2` | Red-100 | Error message backgrounds, destructive confirmation highlights |
| `--color-info` | `#0284C7` | Sky-600 | Informational states, neutral system notifications |
| `--color-info-light` | `#E0F2FE` | Sky-100 | Info message backgrounds |

### Domain colour palette

These are the authoritative values. Use them exactly as listed. They are not approximations — they are the precise hex codes registered to each domain throughout the entire codebase.

| # | Domain | Primary | Light bg | Tailwind name |
|---|---|---|---|---|
| 00 | Foundation / Admin | `#111827` | `#F9FAFB` | Gray-900 |
| 01 | Core Platform | `#111827` | `#F9FAFB` | Gray-900 |
| 02 | HR & People | `#7C3AED` | `#EDE9FE` | Violet-600 |
| 03 | Projects & Work | `#4F46E5` | `#EEF2FF` | Indigo-600 |
| 04 | Finance & Accounting | `#059669` | `#D1FAE5` | Emerald-600 |
| 05 | CRM & Sales | `#DC2626` | `#FEE2E2` | Red-600 |
| 06 | Marketing & Content | `#DB2777` | `#FCE7F3` | Pink-600 |
| 07 | Operations | `#D97706` | `#FEF3C7` | Amber-600 |
| 08 | Analytics & BI | `#0284C7` | `#E0F2FE` | Sky-600 |
| 09 | IT & Security | `#6B7280` | `#F3F4F6` | Gray-500 |
| 10 | Legal & Compliance | `#92400E` | `#FFFBEB` | Amber-800 |
| 11 | E-commerce | `#0891B2` | `#CFFAFE` | Cyan-600 |
| 12 | Communications | `#7C3AED` | `#EDE9FE` | Violet-600 |
| 13 | Learning & Development | `#16A34A` | `#DCFCE7` | Green-600 |
| 14 | AI & Automation | `#6366F1` | `#EEF2FF` | Indigo-500 |
| 15 | Community & Social | `#F59E0B` | `#FEF3C7` | Amber-400 |
| 16 | Workplace & Facility | `#0F766E` | `#CCFBF1` | Teal-700 |
| 17 | Professional Services (PSA) | `#7E22CE` | `#F5F3FF` | Purple-700 |
| 18 | Product-Led Growth | `#0369A1` | `#E0F2FE` | Sky-700 |
| 19 | Business Travel | `#1D4ED8` | `#DBEAFE` | Blue-700 |
| 20 | ESG & Sustainability | `#15803D` | `#DCFCE7` | Green-700 |
| 21 | Real Estate & Property | `#57534E` | `#F5F5F4` | Stone-600 |
| 22 | Customer Success | `#0EA5E9` | `#E0F2FE` | Sky-500 |
| 23 | Subscription Billing | `#10B981` | `#D1FAE5` | Emerald-500 |
| 24 | Procurement | `#F97316` | `#FFEDD5` | Orange-500 |
| 25 | FP&A | `#6366F1` | `#EEF2FF` | Indigo-500 |
| 26 | Events Management | `#EC4899` | `#FCE7F3` | Pink-500 |
| 27 | Document Management | `#8B5CF6` | `#EDE9FE` | Violet-500 |
| 28 | Whistleblowing & Ethics | `#6D28D9` | `#EDE9FE` | Violet-700 |
| 29 | Field Service Management | `#EA580C` | `#FFEDD5` | Orange-600 |
| 30 | Pricing Management | `#0D9488` | `#CCFBF1` | Teal-600 |
| 31 | Enterprise Risk Management | `#B91C1C` | `#FEE2E2` | Red-700 |

**Domain colour rules**:
- Primary colour: used for domain panel navigation accent, sidebar active-state indicator, domain badges, and chart series colour when a domain's data is displayed
- Light bg: used for domain-specific card backgrounds, highlighted table rows, and empty-state backgrounds within a domain panel
- Never use a domain colour for the FlowFlex platform brand — logo, primary CTA, and marketing materials always use the brand palette above
- Domain colours are structural, not cosmetic — they are not tenant-overridable at the domain level (see White-Label section for what tenants can override)

---

## 6. Typography

**Primary typeface**: Inter (via Bunny Fonts — GDPR-friendly, EU-hosted)
**Monospace**: JetBrains Mono (code blocks, terminal output, data tables requiring fixed-width alignment)
**Fallback stack**: `-apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, sans-serif`

Inter is loaded at weights 400, 500, 600, 700, and 800. Do not load weights that are not in this list — it adds unnecessary payload.

**Type scale**

| Role | Size | Weight | Line height | Tailwind classes |
|---|---|---|---|---|
| Display | 48–72px | 800 | 1.1 | `text-6xl font-extrabold` |
| H1 | 36px | 700 | 1.2 | `text-4xl font-bold` |
| H2 | 30px | 600 | 1.3 | `text-3xl font-semibold` |
| H3 | 24px | 600 | 1.4 | `text-2xl font-semibold` |
| H4 | 20px | 600 | 1.4 | `text-xl font-semibold` |
| Body | 16px | 400 | 1.6 | `text-base` |
| Body small | 14px | 400 | 1.5 | `text-sm` |
| Label / UI | 12px | 500 | 1.4 | `text-xs font-medium` |
| Caption | 11px | 400 | 1.4 | `text-[11px]` |
| Code | 14px | 400 | 1.6 | `font-mono text-sm` |

Display size is for marketing/hero only. Inside the application, H1 is the largest heading in use.

---

## 7. Spacing and Layout

**Base unit**: 4px (Tailwind default — `1` = `4px`, `2` = `8px`, `4` = `16px`, etc.)

**Key layout dimensions**:
| Element | Value | Tailwind |
|---|---|---|
| Content max-width | 1280px | `max-w-7xl` |
| Panel sidebar (expanded) | 256px | `w-64` |
| Panel sidebar (icon-only) | 64px | `w-16` |
| Card border radius | 8px | `rounded-lg` |
| Modal border radius | 12px | `rounded-xl` |
| Button border radius | 6px | `rounded-md` |
| Input border radius | 6px | `rounded-md` |
| Dropdown border radius | 8px | `rounded-lg` |
| Page padding (horizontal) | 24px / 32px | `px-6` / `px-8` |
| Section gap | 32px | `gap-8` |
| Card padding | 24px | `p-6` |
| Form field gap | 20px | `gap-5` |

**Grid**: 12-column grid at full width; 4-column on mobile. Use `grid grid-cols-12 gap-6` as the base layout container inside panels.

---

## 8. Iconography

**Library**: Heroicons v2 (MIT licence) — the default icon library for all FlowFlex interfaces.

- **Outline variant**: default for navigation items, empty states, and inline labels
- **Solid variant**: active or selected states, filled status indicators, buttons where a filled icon improves scannability
- **Mini (16px)**: inline with text labels, table action cells
- **Standard (20px)**: buttons, form labels, sidebar navigation
- **Large (24px)**: empty states, feature highlights, dashboard header icons

**Usage in Filament**:
```php
protected static string $navigationIcon = 'heroicon-o-users';
protected static string $activeNavigationIcon = 'heroicon-s-users';
```

**Usage in Vue**:
```html
<!-- via @heroicons/vue package -->
<UsersIcon class="w-5 h-5 text-gray-500" />
<SolidUsersIcon class="w-5 h-5 text-indigo-600" />
```

**Custom icons**: FlowFlex-specific icons not available in Heroicons live in `resources/js/icons/` as individual SVG Vue components, named `FlowIcon{Name}.vue` (e.g., `FlowIconPayroll.vue`). They must match Heroicons' 24px viewBox and stroke conventions for consistency.

**Do not** use emoji as icons, Font Awesome, or any other icon library — consistency matters across 31 domains.

---

## 9. Motion and Animation

All transitions must feel snappy and purposeful. FlowFlex does not use decorative animations that delay interaction.

| Type | Duration | Easing | Tailwind | Usage |
|---|---|---|---|---|
| Micro-interaction | 75ms | ease-in-out | `duration-75` | Button press, checkbox toggle, hover colour |
| Standard | 150ms | ease-in-out | `duration-150` | Dropdown open, tooltip appear, input focus ring |
| Slow | 300ms | ease-in-out | `duration-300` | Modal enter/exit, drawer slide, page transition |

**Rules**:
- No decorative animations that block or delay interaction — animation must never add perceived latency
- Skeleton screens over spinners: when content takes more than 150ms to load, show a skeleton layout, not a spinner in the centre of the page
- Respect `prefers-reduced-motion`: all CSS transitions and animations must be wrapped with `@media (prefers-reduced-motion: reduce)` that sets `animation: none; transition: none`
- In Vue: use `<Transition>` with named transition classes rather than inline styles. Class names: `fade`, `slide-up`, `slide-right`

---

## 10. Voice and Tone

FlowFlex speaks like a capable colleague who respects the user's time. It does not shout, apologise excessively, or hide behind jargon.

**Situation guide**:

| Situation | Tone | Good example | Avoid |
|---|---|---|---|
| Success | Warm, brief | "Invoice sent." | "Your invoice has been successfully sent!" |
| Error | Calm, actionable | "Couldn't save. Check your connection and try again." | "An unexpected error has occurred." |
| Empty state | Encouraging, clear | "No employees yet. Add your first team member." | "No records found." |
| Onboarding | Friendly guide | "Let's set up your workspace. It takes about 5 minutes." | "Please complete the mandatory setup wizard." |
| Danger / destructive action | Direct, honest | "This will permanently delete 3 records. You cannot undo this." | "Are you sure you want to proceed?" |
| Notification | Precise, scannable | "Leave approved — Max, 3 days from 12 May" | "A leave request has been approved for the user." |
| Form validation | Specific | "Email address is missing." | "This field is required." |

**Writing rules**:
- Use "you" and "your" — never "the user" or "the account"
- Active voice always: "Add an employee" not "An employee can be added"
- No exclamation marks inside the application — they are fine in marketing copy
- Sentence case for all UI labels, headings, and button text — not Title Case
- Avoid: "Please", "Sorry", "Unfortunately", "You must", "Invalid", "Failed" without context
- Numbers and dates: always use the company's locale settings — never hardcode format
- Avoid abbreviations unless they are universally understood (e.g., "HR", "CRM", "VAT" are fine; "emp." for employee is not)

---

## 11. Filament Panel Implementation

Each domain's Filament panel registers its domain colour as the panel primary. This is the only thing that changes per panel — all other brand tokens are inherited from the global stylesheet.

```php
// In each domain's PanelProvider, e.g. HrPanelProvider
->colors([
    'primary' => Color::hex('#7C3AED'), // HR & People — Violet-600
])
->brandName('FlowFlex — HR & People')
->brandLogo(fn () => view('filament.brand.logo'))
->brandLogoHeight('32px')
->font('Inter')
->sidebarCollapsibleOnDesktop()
->darkMode(Feature::Enabled)
```

The domain primary colour controls: sidebar active-state highlight, primary button colour, focus ring colour, and form input accent within that panel.

The workspace panel (the multi-domain shell that houses all active domain sections) uses the FlowFlex platform primary (`#4F46E5`) by default and transitions the sidebar accent colour to match the active domain section when the user navigates between domains.

---

## 12. White-Label Overrides

Tenants on the Enterprise plan can apply limited branding overrides. These are stored in `companies.branding` as a JSON column and applied at runtime via CSS custom property injection into the tenant's session.

| Field | Overrides | Notes |
|---|---|---|
| `logo_url` | Replaces the FlowFlex logo in the tenant's workspace header | Must be an SVG or PNG served from the tenant's approved CDN |
| `primary_colour` | Overrides `--color-brand-primary` in the tenant's session | Validated to meet WCAG AA contrast against white |
| `favicon_url` | Replaces the browser tab icon | 32×32 PNG |
| `hide_powered_by` | Removes "Powered by FlowFlex" footer text | Enterprise-only flag |

**What cannot be overridden**:
- Domain colours — these are structural identifiers used in cross-domain data visualisation, not cosmetic choices
- Typography — Inter is the platform typeface; custom fonts are not supported
- Motion timings — animation standards are consistent platform-wide

---

## Related

[[MOC_DesignSystem]], [[colour-system]], [[typography]], [[tech-stack]], [[workspace-panel]], [[portal-architecture]]
