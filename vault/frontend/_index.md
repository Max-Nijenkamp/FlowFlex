---
type: frontend
category: index
color: "#FBBF24"
---

# Public Frontend — Vue 3 + Inertia

> **Design system**: [[design-system|Switchboard+ reference]] — tokens, component library, panel skin, motion, copy rules. Read it before touching any UI.

> **Update (2026-06-12, pass 2)**: graph-paper grid replaced everywhere by the **bloom** treatment — `.bg-bloom` (indigo radial + paper-deep fade) on light heroes/sections + auth form side, `.bg-bloom-accent` (white + sky glows) on indigo CTA bands; no grid textures remain. Public login forgot-link moved below the password input (tab order). Panel side same session: shared Filament skin rebuilt against verified Filament 5 selectors, Spotlight ⌘K/Ctrl+K palette, UX-state defaults — see [[../architecture/filament-patterns]] items 6, 13–16 and [[../architecture/patterns/ux-states]].
>
> **Build status (2026-06-12 — Switchboard+ redesign)**: Full visual redesign implemented from the `design_handoff_flowflex_site/` bundle (high-fidelity spec, copy final). Design system "Switchboard+": modules as literal switches, receipts as invoices, blueprint stat cells, dark Flow bands with animated pulse lines. Type: Archivo display / Instrument Sans body / JetBrains Mono data (Google Fonts in `app.blade.php`). New tokens in `app.css` (`--color-card`, `--color-line-strong`, `--color-flow-bg`, marquee + pulse-dash keyframes, `.bg-graph`, `.receipt-edge`). Shared components in `Components/Marketing/` (Kicker, SectionTag, Switchboard, BlueprintCell, ModuleTile, Receipt, FlowBand, ReplacesStrip, CtaBand, DomainPill, LegalPage) + `Components/UI/Switch.vue`; static content data in `resources/js/data/marketing.ts`. All marketing pages rebuilt to the per-screen specs; Pricing kept its server props + reactive calculator; Contact kept useForm + honeypot. Auth: split shell (dark `#0E1320` panel, radial indigo glow, 3 animated SVG flow pulses) for Login/InviteRegister, centered variant for Forgot/Reset (`AuthLayout` `split` prop). Filament: shared `resources/css/filament/flowflex-skin.css` imported by all 5 panel themes — ink sidebar in both modes, domain-color active nav item (2px left border + 16% tint), mono table headers + zebra rows, paper canvas, indigo customer login button / ink staff button; providers switched to light logo + Instrument Sans.
>
> **Previous status (2026-06-11, superseded)**: v1 SHIPPED + full design revamp. Brand-true design system per [[../product/brand]]: ink #111827 / warm paper #FBFAF8 / single indigo accent #4F46E5, editorial numbered sections, mono accents for figures, animated flow-line motif, scroll-reveal (ease-out, reduced-motion safe). Logo SVGs created at the brand.md paths (icon = flow-loop F; favicon = SVG, PNG deferred — no image tooling in env). Pages: home (problem stats, interactive flex demo, dark Flow section w/ real event chains, coverage grid), pricing ("build your invoice" live calculator + fair-print FAQ), product (per-domain stories + what-flows lists), about (values + is/isn't), contact (split layout), legal. Auth = split-screen AuthLayout (dark brand panel). Copy follows brand voice: sentence case, no exclamation marks, "you/your". Blog + portals = Phase 2. **Pass 2 (same day)**: shared form component library (`Components/Form/` + BaseButton/Accordion), @tailwindcss/forms class strategy, pricing domain accordions, nav product dropdown, handcrafted AppMock hero visual, Filament panel brand skin + logo/favicon on all panels.

The public-facing side of FlowFlex. Separate from Filament panels — custom design, SEO, external users.

**Tech**: Vue 3.5 + TypeScript 5 + Inertia.js v2 + Tailwind CSS v4 + Vite 6

---

## What Goes Here vs Filament

| Page type | Tech |
|---|---|
| Marketing site (homepage, pricing, about) | Vue + Inertia |
| Invite-accept registration (`/register/invite/{token}`) | Vue + Inertia |
| Client portal (external clients viewing CRM data) | Vue + Inertia |
| Learner portal (LMS — external learners) | Vue + Inertia |
| Login / password reset | Vue + Inertia |
| Checkout / billing flows | Vue + Inertia |
| Setup wizard (first-login company setup) | Custom Filament Page in `/app` — NOT here |
| Custom domain views (Kanban, Gantt, Calendar) | Custom Filament Page — NOT here |
| All business domain CRUD screens | Filament — NOT here |

---

## Page Inventory

### Marketing Pages

| Route | Component | Notes |
|---|---|---|
| `/` | `Pages/Marketing/Home.vue` | Homepage — hero, features, pricing, testimonials |
| `/pricing` | `Pages/Marketing/Pricing.vue` | Module-by-module pricing calculator |
| `/about` | `Pages/Marketing/About.vue` | Company story |
| `/features` | `Pages/Marketing/Features.vue` | Domain feature overview |
| `/blog` | `Pages/Marketing/Blog/Index.vue` | Blog post list |
| `/blog/{slug}` | `Pages/Marketing/Blog/Show.vue` | Blog post detail |
| `/contact` | `Pages/Marketing/Contact.vue` | Contact form |
| `/terms` | `Pages/Marketing/Terms.vue` | Terms of service |
| `/privacy` | `Pages/Marketing/Privacy.vue` | Privacy policy |
| `/modules` | `Pages/Marketing/Catalogue.vue` | Full module catalogue per domain + upcoming pills (§14) |
| `/switch-over` | `Pages/Marketing/SwitchOver.vue` | Migration pitch: 3-step plan, tool→module mapping (§15) |
| `/trust` | `Pages/Marketing/Trust.vue` | Security/GDPR page: blueprint cells + ops notes (§16) |
| `/changelog` | `Pages/Marketing/Changelog.vue` | Shipped log; entries from `Support/Marketing/MarketingContent` (§17) |
| `/patchwork` | `Pages/Marketing/Patchwork.vue` | Interactive patchwork-vs-FlowFlex cost calculator (§21) |
| `/customers/{slug}` | `Pages/Marketing/CaseStudy.vue` | Case study template; content in MarketingContent (Veldkamp sample) (§22) |
| `/status` | `Pages/Marketing/Status.vue` | Public status from spatie/laravel-health latest results, 60s cache (§23) |
| `/help`, `/help/{slug}` | `Pages/Marketing/Help/*` | Help center: client-side search index + article pages (§24) |
| any 404 (public GET) | `Pages/Marketing/NotFound.vue` | "This page is switched off" — wired in `bootstrap/app.php` (§18) |

### Auth Pages

| Route | Component | Notes |
|---|---|---|
| `/login` | `Pages/Auth/Login.vue` | Login form |
| `/register/invite/{token}` | `Pages/Auth/InviteRegister.vue` | Invite-accept registration — name + password only, email pre-filled. No open self-registration; companies are created by FlowFlex staff in `/admin` (see [[domains/core/invitation-system]]) |
| `/forgot-password` | `Pages/Auth/ForgotPassword.vue` | Password reset request |
| `/reset-password/{token}` | `Pages/Auth/ResetPassword.vue` | Password reset form |
| `/verify-email` | `Pages/Auth/VerifyEmail.vue` | Email verification |

### Onboarding

No public onboarding pages. New company setup runs through the first-login Setup Wizard — a custom Filament page in `/app` (see [[domains/core/setup-wizard]]). New users join a workspace only via invitation (see [[domains/core/invitation-system]]).

### Portals (Phase 2+)

| Route | Component | Notes |
|---|---|---|
| `/portal` | `Pages/Portal/Dashboard.vue` | Client portal — external-facing CRM view |
| `/learn` | `Pages/Learn/Dashboard.vue` | Learner portal — LMS for external learners |

---

## Directory Structure

```
resources/
├── js/
│   ├── Pages/
│   │   ├── Marketing/
│   │   ├── Auth/
│   │   └── Portal/
│   ├── Components/
│   │   ├── Marketing/      # Homepage sections, pricing table, feature cards
│   │   ├── UI/             # Button, Input, Modal, Card — shared design system
│   │   └── Layout/         # Header, Footer, Nav
│   ├── Composables/        # useForm, useAuth, useLocale
│   ├── types/
│   │   └── generated.d.ts  # Auto-generated from PHP DTOs (never edit manually)
│   └── app.ts              # Inertia bootstrap
├── css/
│   ├── app.css             # Base Tailwind for Vue pages
│   └── filament/           # Per-panel Filament themes (not this section's concern)
└── views/
    └── app.blade.php       # Inertia root template
```

---

## Inertia Shared Data (HandleInertiaRequests)

`app/Http/Middleware/HandleInertiaRequests.php` is the server-side bridge between Laravel and Vue. It shares global data on every page load:

```php
class HandleInertiaRequests extends Middleware
{
    public function share(Request $request): array
    {
        return array_merge(parent::share($request), [
            'auth' => [
                'user' => $request->user() ? [
                    'id' => $request->user()->id,
                    'name' => $request->user()->full_name,
                    'email' => $request->user()->email,
                    'avatar_url' => $request->user()->avatar_url,
                ] : null,
                'company' => $request->user() ? [
                    'id' => $request->user()->company_id,
                    'name' => $request->user()->company->name,
                    'locale' => $request->user()->company->locale,
                    'currency' => $request->user()->company->currency,
                ] : null,
                'permissions' => $request->user()
                    ? $request->user()->getAllPermissions()->pluck('name')
                    : [],
            ],
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'ziggy' => fn () => [
                ...Ziggy::toArray(),
                'location' => $request->url(),
            ],
        ]);
    }
}
```

Vue side — read shared data via `usePage()`:

```typescript
// composables/useAuth.ts
export function useAuth() {
    const page = usePage<{ auth: { user: UserData; company: CompanyData; permissions: string[] } }>()
    return {
        user: computed(() => page.props.auth.user),
        company: computed(() => page.props.auth.company),
        can: (permission: string) => page.props.auth.permissions.includes(permission),
    }
}
```

---

## Flash Messages

Server → Vue via Inertia shared data. The `flash` prop carries one-time messages from `session()->flash()`.

Server (Laravel controller or action):

```php
return redirect()->route('hr.employees.index')
    ->with('success', 'Employee created successfully.');
```

Vue — `useFlash` composable reads the flash prop and shows a toast:

```typescript
// composables/useFlash.ts
export function useFlash() {
    const page = usePage<{ flash: { success?: string; error?: string } }>()

    watch(() => page.props.flash, (flash) => {
        if (flash.success) toast.success(flash.success)
        if (flash.error) toast.error(flash.error)
    }, { immediate: true })
}
```

Call `useFlash()` in the root app layout component — fires on every navigation.

---

## Conventions

**TypeScript types**: generated from PHP DTOs via `php artisan typescript:transform`. Never hand-write types for server data.

**Forms**: use `useForm` from `@inertiajs/vue3` for all forms that POST to Laravel. Validation errors come back from the server.

**Navigation**: use `<Link>` from `@inertiajs/vue3` for all internal links — prevents full page reload.

**Layouts**: define in `defineOptions({ layout: MarketingLayout })` in each page component.

**CSS**: Tailwind utility classes only. No custom CSS unless required by a third-party component. No `!important`.

**No Vue Router**: Inertia handles routing server-side. No `vue-router` dependency.

---

## State Management

**Decision: No Pinia for most features.** Inertia's server-driven page props cover 95% of state needs. Server is the source of truth — no client-side store needed for data that comes from the server.

**Use Pinia only when:**
- State must persist across Inertia page navigations (e.g. a multi-step wizard with unsaved progress)
- State is purely client-side and unrelated to server data (e.g. UI state like sidebar open/closed, theme preference)
- Shared state between sibling components that don't have a common parent

```typescript
// stores/wizard.ts — example of valid Pinia use
import { defineStore } from 'pinia'

export const useWizardStore = defineStore('wizard', {
    state: () => ({ step: 1, data: {} as Record<string, unknown> }),
    actions: {
        nextStep() { this.step++ },
        setData(key: string, value: unknown) { this.data[key] = value },
    },
})
```

Packages: `pinia` + `@pinia/nuxt` not needed — just `pinia` and `@vueuse/core`.

---

## Shared Composables

```
resources/js/composables/
├── useAuth.ts          ← current user, company, permissions from Inertia shared data
├── useLocale.ts        ← company locale, date formatting, currency formatting
├── useFlash.ts         ← flash message handling (success/error from server)
├── usePagination.ts    ← paginated list helpers (page, perPage, total)
└── useConfirm.ts       ← confirm dialog before destructive actions
```

`useAuth.ts` reads from Inertia's shared data (set in `HandleInertiaRequests` middleware):

```typescript
export function useAuth() {
    const page = usePage()
    return {
        user: computed(() => page.props.auth.user),
        company: computed(() => page.props.auth.company),
        can: (permission: string) => page.props.auth.permissions.includes(permission),
    }
}
```

---

## Frontend Testing

| Tool | Purpose | When |
|---|---|---|
| **Vitest** | Unit tests for composables, utilities, formatters | Always |
| **Vue Test Utils** | Component unit tests (form validation, conditional rendering) | For complex components |
| **Playwright** | End-to-end tests for critical user flows | Auth flows, invite acceptance, checkout |

**What to test:**
- Composables: pure logic, easy to unit test
- Form validation: Vue Test Utils on form components
- E2E (Playwright): login flow, invite acceptance → first login, checkout flow
- Do NOT test Filament panels with Playwright — use Pest + `pest-plugin-livewire` instead

```bash
# package.json scripts
"test": "vitest",
"test:e2e": "playwright test",
"test:coverage": "vitest run --coverage"
```

---

## ESLint + Prettier

```json
// .eslintrc
{
  "extends": ["plugin:vue/vue3-recommended", "@vue/typescript/recommended"],
  "rules": {
    "vue/multi-word-component-names": "off",
    "@typescript-eslint/no-explicit-any": "error"
  }
}
```

Run in CI: `eslint resources/js --ext .ts,.vue`. Prettier for formatting (do not use both `eslint --fix` and `prettier` on the same file — pick one; use Prettier for format, ESLint for lint).
