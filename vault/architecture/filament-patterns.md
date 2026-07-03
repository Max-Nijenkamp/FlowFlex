---
type: architecture
category: filament
pattern-key: filament
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Filament 5 Patterns

Critical non-obvious patterns for Filament 5 in FlowFlex. Read this before writing any resource, page, widget, or panel provider. Each item reflects a real pitfall causing silent failures or hard-to-diagnose bugs.

---

## 1. canAccess() on Every Resource and Page

Every resource and every custom page must implement `canAccess()`. This is the only mechanism preventing unauthorised users from seeing navigation links and accessing URLs.

```php
// On resources
public static function canAccess(): bool
{
    return Auth::check()
        && Auth::user()->can('hr.employees.view-any')
        && BillingService::hasModule('hr.employees');
}

// On custom pages
public static function canAccess(): bool
{
    return Auth::check()
        && Auth::user()->can('hr.payroll.view-any')
        && BillingService::hasModule('hr.payroll');
}
```

If `canAccess()` is omitted, the resource is visible to every authenticated user regardless of role or module subscription. This is a security defect and UX defect simultaneously.

**Spec-level requirement** (per [[build/decisions/decision-2026-06-11-security-contract-hardening]]): every module spec's `## Filament` section must declare this access contract for each artifact — `permission + BillingService::hasModule(module-key)`. Custom pages (Kanban, dashboards, builders, kiosks, wizards) are the highest risk: Filament auto-registers their routes but does **not** auto-gate them, so a missing `canAccess()` exposes the URL to any authenticated user. The 2026-06-11 audit ([[_archive/build-history/security-audit-2026-06-11]]) found this missing on ~100+ artifacts across all 31 domains — it is the #1 systemic gap. Verify at `/flowflex:start` and before `/flowflex:done`.

---

## 2. $view Property — Non-Static on Custom Pages

In Filament 5, `$view` on custom page classes must be an **instance property**, not static:

```php
// Correct — Filament 5
class KanbanBoardPage extends Page
{
    protected string $view = 'filament.projects.pages.kanban-board';
}

// Wrong — Filament 4 pattern, silently ignored in Filament 5
class KanbanBoardPage extends Page
{
    protected static string $view = 'filament.projects.pages.kanban-board';
}
```

The static declaration compiles without error but Filament 5 does not read it. Page renders blank or throws a missing view exception.

---

## 3. getSlug() Signature on Custom Pages

```php
// Correct — Filament 5 signature
public static function getSlug(?Panel $panel = null): string
{
    return 'kanban-board';
}

// Wrong — missing parameter causes interface mismatch
public static function getSlug(): string
{
    return 'kanban-board';
}
```

---

## 4. Navigation Property Types

```php
// Correct — Filament 5 types
protected static string|UnitEnum|null $navigationGroup = 'Employees';
protected static BackedEnum|string|null $navigationIcon = 'heroicon-o-users';
protected static BackedEnum|string|null $activeNavigationIcon = 'heroicon-s-users';

// Wrong — Filament 4 types
protected static ?string $navigationGroup = 'Employees';
protected static ?string $navigationIcon = 'heroicon-o-users';
```

Heroicons strings still work — the type is union, not an enum requirement. But the property declaration must use the new union type syntax.

---

## 5. Panel Provider Pattern

```php
class HrPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('hr')
            ->path('hr')
            ->colors(['primary' => Color::hex('#7C3AED')])
            ->brandName('FlowFlex — HR & People')
            ->brandLogo(asset('images/logo/flowflex-logo-light.svg')) // light — sidebar is ink in both modes
            ->font('Instrument Sans') // Switchboard+ body face (brand.md)
            ->darkMode(Feature::Enabled)
            ->sidebarCollapsibleOnDesktop()
            ->authGuard('web')
            ->authModel(User::class)
            ->discoverResources(
                in: app_path('Filament/HR/Resources'),
                for: 'App\\Filament\\HR\\Resources',
            )
            ->discoverPages(
                in: app_path('Filament/HR/Pages'),
                for: 'App\\Filament\\HR\\Pages',
            )
            ->discoverWidgets(
                in: app_path('Filament/HR/Widgets'),
                for: 'App\\Filament\\HR\\Widgets',
            )
            ->middleware(['web', SetLocale::class])
            ->authMiddleware([
                Authenticate::class,
                SetCompanyContext::class,
            ])
            ->viteTheme('resources/css/filament/hr/theme.css');
    }
}
```

Registered in `bootstrap/providers.php`.

---

## 6. Theme CSS and Vite Registration

Each panel needs its own Tailwind CSS theme file at `resources/css/filament/{panel}/theme.css` — since 2026-06-12 these are **thin**: Google-fonts import (must stay the first statement), the vendor theme import, the shared **Switchboard+ skin**, the panel's `@source` globs, and a mono sidebar label:

```css
@import url('https://fonts.googleapis.com/css2?family=Archivo:...&family=Instrument+Sans:...&family=JetBrains+Mono:...&display=swap');
@import '../../../../vendor/filament/filament/resources/css/theme.css';
@import '../flowflex-skin.css';

@source '../../../../app/Filament/HR/**/*';
@source '../../../../resources/views/filament/hr/**/*';

.fi-sidebar-header {
    --ff-panel-label: 'HR & PEOPLE · /HR';
}
```

All visual rules live ONCE in `resources/css/filament/flowflex-skin.css` (ink sidebar, domain-color active item, mono table headers, paper canvas, tabs/pagination/badges, empty states, wizard steps, spotlight, login). The panel accent rides on Filament's `--primary-*` variables — never hardcode a domain color in the skin.

Register in `vite.config.js`:

```js
input: [
    'resources/css/app.css',
    'resources/css/filament/hr/theme.css',
    'resources/css/filament/finance/theme.css',
    // one per domain panel
],
```

And in the panel provider: `->viteTheme('resources/css/filament/hr/theme.css')`.

If the CSS exists but is not in vite.config.js, the build step skips it silently.

---

## 7. Middleware Order

`Authenticate` must run before `SetCompanyContext`:

```php
->authMiddleware([
    Authenticate::class,     // establishes $user first
    SetCompanyContext::class, // reads $user->company_id
])
```

If `SetCompanyContext` runs before `Authenticate`, `auth()->user()` is null and the middleware throws a null dereference.

---

## 8. URL Slug Auto-Generation

Filament generates slugs from class names: `KanbanBoardPage → /kanban-board-page`. Use `getSlug()` (see item 3) when you need a clean slug like `/kanban`.

---

## 9. discoverResources() Path Convention

```php
->discoverResources(
    in: app_path('Filament/HR/Resources'),   // filesystem path
    for: 'App\\Filament\\HR\\Resources',    // namespace prefix
)
```

If the namespace does not match the class declaration, Filament silently skips the class. Verify the class declares `namespace App\Filament\HR\Resources;`.

---

## 10. Multi-Tenant Select Fields

```php
// Correct — scoped to current company automatically
Select::make('manager_id')
    ->relationship('manager', 'full_name')

// Wrong — bypasses company scope, shows all companies' users
Select::make('manager_id')
    ->options(User::withoutGlobalScopes()->pluck('full_name', 'id'))
```

`withoutGlobalScopes()` is only valid in the `/admin` panel for FlowFlex staff.

---

## 11. Filament Assets Must Be Published (Browser-Only Failure)

`php artisan filament:assets` publishes `public/js/filament/*` — Filament's Alpine
components (`filamentFormButton`, dropdowns, modals). When missing, **every browser
interaction silently breaks while the entire Pest suite stays green** (Livewire feature
tests never execute browser JS). Discovered 2026-06-11 ([[../build/gaps/gap-filament-assets-unpublished]]).

Composer hooks now run it on `post-install-cmd` + `post-update-cmd` — never remove them.

## 12. No Tailwind Classes in PHP-Side Panel HTML

Panel theme builds scan `app/Filament/**` and `resources/views/filament/**` — NOT
`app/Providers/**`. Any HtmlString built in a provider (brand logos, render hooks)
must use plain CSS classes defined in the theme (e.g. `.ff-login-footer`) or native
Filament APIs (`->brandLogo()/->darkModeBrandLogo()/->brandLogoHeight()`), never raw
Tailwind utilities — they won't exist in the compiled theme.

## 13. Auth Page Skin (Login Parity)

Panel auth pages (simple layout) match the public Vue login: bloom background (no
grids), centred 420px card (20px radius, line-strong border), ease-out entrance,
brand mark above the card (`SIMPLE_LAYOUT_START` hook) + mono footer strip
(`SIMPLE_LAYOUT_END` hook, AppServiceProvider). The forgot-password link sits
**below** the password input (`PanelLogin::getPasswordFormComponent` →
`belowContent`) so tabbing goes email → password directly. Buttons: customer
panels **indigo**, staff console **ink** + mono `/ADMIN` badge next to the heading
(admin/theme.css overrides). Shared rules live in `flowflex-skin.css`.

Guests on staff surfaces (`/admin*`, `/horizon*`, `/pulse*`) redirect to
`/admin/login`, not the customer login — `redirectGuestsTo` in `bootstrap/app.php`.

**`url.intended` is guard-scoped (2026-06-12 bug pair).** Laravel stores ONE
intended URL per session: a guest visit to `/admin` followed by a customer
login redirected the customer to `/admin` → bounced to the staff login (and
the mirror image hijacked staff logins to `/app`). Fix:
`App\Http\Responses\GuardScopedLoginResponse` (bound to Filament's
`LoginResponse` contract — honors intended only when its path matches the
panel's guard) + the same prefix filter in `PublicAuthController::login`.
Regression tests in `PanelAuthTest`. Symptom signature: "login on X redirects
me to Y's login" — check the intended URL before suspecting guards.

## 14. Spotlight (⌘K / Ctrl+K)

`App\Livewire\Spotlight` + `resources/views/livewire/spotlight.blade.php`, injected
on every authenticated panel page via the `BODY_END` render hook. Panel-scoped:
navigation (resources + pages, `canAccess`-filtered), quick-create actions, and
record results via `$panel->getGlobalSearchProvider()`. The component restores
panel context with `Filament::setCurrentPanel()` because Livewire update requests
don't run panel routing. Styling = `.ff-spotlight-*` classes in the skin (plain CSS —
livewire views aren't scanned by panel themes, see item 12). Panels no longer set
`globalSearchKeyBindings` — Spotlight owns mod+k.

## 15. UX-State Defaults

`Table::configureUsing()` in AppServiceProvider sets human empty-state copy
platform-wide; skin styles empty states (panel-tint icon + corner tick), selected
rows (10% tint + 2px edge ≠ hover 5% wash) and wizard steps. Full rules:
[[architecture/patterns/ux-states]]. Forms >8 fields use a `Wizard` — steps
validate on Next, never all-at-the-end.

## 16. Skin Selectors Are Verified Against Rendered Markup

Filament 5 class names differ from v3 docs (`fi-sidebar-item-btn` not
`…-item-button`; topbar is `nav.fi-topbar` inside `.fi-topbar-ctn`; active
pagination = `.fi-pagination-item.fi-active`). When extending the skin, verify
against rendered HTML or `vendor/filament/*/resources/views` — a selector that
matches nothing fails silently.

