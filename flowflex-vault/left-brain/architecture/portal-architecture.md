---
type: architecture-note
section: architecture
status: decision-required
last_updated: 2026-05-09
---

# Portal Architecture — Unified Framework Decision

FlowFlex has three Vue+Inertia portals targeting different user types. This note defines the unified architecture so they share infra without leaking data across auth boundaries.

---

## The Three Portals

| Portal | Route Prefix | Auth Guard | User Type | Domain |
|---|---|---|---|---|
| Employee Self-Service (ESS) | `/my/` | `auth:employee` | Employees | HR |
| Client Portal | `/portal/` | `auth:portal` | Customers / Clients | CRM |
| B2B Commerce Portal | `/b2b/` | `auth:b2b` | Trade buyers / Resellers | Ecommerce |
| Partner Portal | `/partner/` | `auth:partner` | Channel partners / Agencies | CRM (PRM) |
| Learner Portal | `/learn/` | `auth:learner` | External learners | LMS |
| Community | `/community/` | `auth:member` | Community members | Community |

All share: Vue 3 + Inertia.js, company branding, mobile-first responsive, PWA capability.

---

## Problem: Six Separate Implementations

Without a unified framework, each portal re-implements:
- Session management + CSRF
- Company branding injection (logo, colours, fonts)
- Inertia shared data (auth user, flash messages, navigation)
- Company subdomain resolution (`company.flowflex.com/portal`)
- Rate limiting, CORS, CSP headers

This is ~6x the maintenance surface.

---

## Decision: Unified Portal Framework

Single `PortalKernel` handles all portals. Guard determines which data is visible.

### Auth Guards (separate, non-overlapping)

Each portal has its own guard config in `config/auth.php`:

```php
'guards' => [
    'employee'  => ['driver' => 'session', 'provider' => 'portal_users'],
    'portal'    => ['driver' => 'session', 'provider' => 'portal_users'],
    'b2b'       => ['driver' => 'session', 'provider' => 'portal_users'],
    'partner'   => ['driver' => 'session', 'provider' => 'portal_users'],
    'learner'   => ['driver' => 'session', 'provider' => 'portal_users'],
    'member'    => ['driver' => 'session', 'provider' => 'portal_users'],
],
'providers' => [
    'portal_users' => ['driver' => 'eloquent', 'model' => PortalUser::class],
],
```

`PortalUser` has a `portal_type` enum column: `employee | client | b2b | partner | learner | member`. Login page checks `portal_type` matches guard.

**Critical isolation rule**: A user authenticated on `auth:employee` guard cannot access `auth:portal` routes, even with the same email address. Each guard has its own session cookie (`employee_session`, `portal_session`, etc.).

### Company Resolution

All portals resolve the active company via subdomain or path:
- **Subdomain**: `mycompany.flowflex.com/my/dashboard` — resolves `mycompany`
- **Custom domain**: `portal.acme.com` — resolved via `company_domains` lookup table
- **Path**: `flowflex.com/portal/acme/...` — fallback for non-domain customers

```php
// Middleware: ResolvePortalCompany
class ResolvePortalCompany
{
    public function handle(Request $request, Closure $next): Response
    {
        $company = $this->resolver->fromRequest($request);

        if (!$company || !$company->isActive()) {
            abort(404);
        }

        app()->instance(Company::class, $company);
        app(CompanyContext::class)->set($company);

        return $next($request);
    }
}
```

### Shared Inertia Kernel

All portals share one `HandlePortalInertiaRequests` middleware:

```php
public function share(Request $request): array
{
    return [
        'company' => fn() => [
            'name'         => $this->company->name,
            'logo_url'     => $this->company->branding['logo_url'] ?? null,
            'primary_colour' => $this->company->branding['primary_colour'] ?? '#3B82F6',
            'custom_domain' => $this->company->portal_domain,
        ],
        'auth' => fn() => [
            'user'        => $this->portalUser($request),
            'portal_type' => $this->guardType($request),
            'permissions' => $this->portalPermissions($request),
        ],
        'flash' => fn() => [
            'success' => $request->session()->get('success'),
            'error'   => $request->session()->get('error'),
        ],
    ];
}
```

### Vue Portal Layout System

Single `PortalLayout.vue` with `portal_type` prop switches nav:

```
resources/js/
├── layouts/
│   ├── PortalLayout.vue          # shared: branding, nav shell, auth check
│   └── portals/
│       ├── ESSNav.vue            # /my/ navigation items
│       ├── ClientPortalNav.vue   # /portal/ navigation items
│       ├── B2BPortalNav.vue      # /b2b/ navigation items
│       └── PartnerPortalNav.vue  # /partner/ navigation items
├── pages/
│   ├── ESS/                      # employee portal pages
│   ├── Portal/                   # client portal pages
│   ├── B2B/                      # b2b portal pages
│   └── Partner/                  # partner portal pages
```

---

## White-Label / Custom Branding

Each portal is white-labelled per company:

```
companies.branding JSON:
{
    "logo_url": "https://cdn.flowflex.com/t/{company_id}/logo.png",
    "favicon_url": "...",
    "primary_colour": "#E63E22",
    "secondary_colour": "#1A1A2E",
    "portal_domain": "portal.acme.com",    // custom domain override
    "font_family": "Inter",                // or customer's own font
    "hide_powered_by": true                // white-label tier
}
```

CSS variables injected per request — no build-time customisation needed.

---

## Data Isolation Matrix

| Data Type | ESS (`auth:employee`) | Client Portal (`auth:portal`) | B2B (`auth:b2b`) |
|---|---|---|---|
| HR records | Own records only | Never | Never |
| Invoices | Own payslips only | Own invoices only | Own orders only |
| Projects | Assigned tasks (read) | Shared projects only | Never |
| CRM contacts | Never | Own contact record | Own company record |
| Orders | Never | Never | Own orders |
| Support tickets | Via HR (absence) | Own tickets | Own tickets |
| Documents | HR-shared docs | Contract/shared docs | Order docs |

Controller-level enforcement — never rely on the view layer to hide data.

---

## Session Security

- Separate session cookie per guard (no cross-portal session reuse)
- HttpOnly + Secure + SameSite=Lax cookies
- Session expiry: 8h default, configurable per company policy
- Concurrent session limit: configurable (default 5 active sessions)
- "Sign out all devices" option in each portal's settings
- IP change → re-authentication prompt (configurable)

---

## Route Structure

```php
// routes/portals/employee.php
Route::middleware(['resolve.portal.company', 'auth:employee', 'portal.active'])
    ->prefix('my')
    ->name('ess.')
    ->group(fn() => require base_path('routes/portals/employee-routes.php'));

// routes/portals/client.php
Route::middleware(['resolve.portal.company', 'auth:portal', 'portal.active'])
    ->prefix('portal')
    ->name('client.')
    ->group(fn() => require base_path('routes/portals/client-routes.php'));
```

---

## Portal User Registration / Invite Flow

Portals do not allow self-registration (except Community). Users are invited:

1. Admin creates portal user (or system auto-creates on CRM contact, new hire, new order)
2. System sends invite email with magic link (24h expiry)
3. User clicks → sets password → session created
4. Company branding shown throughout

Community portal: public self-registration allowed (email verification required).

---

## Related

- [[left-brain/domains/02_hr/employee-self-service-portal.md]] — ESS implementation
- [[left-brain/frontend/client-portal.md]] — Client Portal pages
- [[left-brain/domains/05_crm/partner-relationship-management.md]] — Partner Portal
- [[left-brain/frontend/public-pages.md]] — Community and Learner portals
- [[left-brain/architecture/auth-rbac.md]] — portal guards vs Filament admin guards
- [[concept-multi-tenancy]] — company resolution on every portal request
