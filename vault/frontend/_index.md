---
type: frontend
category: index
color: "#FBBF24"
---

# Public Frontend — Vue 3 + Inertia

The public-facing side of FlowFlex. Separate from Filament panels — custom design, SEO, external users.

**Tech**: Vue 3.5 + TypeScript 5 + Inertia.js v2 + Tailwind CSS v4 + Vite 6

---

## What Goes Here vs Filament

| Page type | Tech |
|---|---|
| Marketing site (homepage, pricing, about) | Vue + Inertia |
| Onboarding wizard (new company signup) | Vue + Inertia |
| Client portal (external clients viewing CRM data) | Vue + Inertia |
| Learner portal (LMS — external learners) | Vue + Inertia |
| Login / registration | Vue + Inertia |
| Checkout / billing flows | Vue + Inertia |
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

### Auth Pages

| Route | Component | Notes |
|---|---|---|
| `/login` | `Pages/Auth/Login.vue` | Login form |
| `/register` | `Pages/Auth/Register.vue` | Company + owner registration |
| `/forgot-password` | `Pages/Auth/ForgotPassword.vue` | Password reset request |
| `/reset-password/{token}` | `Pages/Auth/ResetPassword.vue` | Password reset form |
| `/verify-email` | `Pages/Auth/VerifyEmail.vue` | Email verification |

### Onboarding

| Route | Component | Notes |
|---|---|---|
| `/onboarding` | `Pages/Onboarding/Wizard.vue` | Multi-step company setup (name, timezone, invite team) |
| `/onboarding/modules` | `Pages/Onboarding/Modules.vue` | First module selection |

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
│   │   ├── Onboarding/
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
| **Playwright** | End-to-end tests for critical user flows | Auth flows, onboarding, checkout |

**What to test:**
- Composables: pure logic, easy to unit test
- Form validation: Vue Test Utils on form components
- E2E (Playwright): login → onboarding wizard, invoice creation flow, plan upgrade
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
