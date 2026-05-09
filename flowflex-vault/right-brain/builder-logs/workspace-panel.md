---
type: builder-log
module: workspace-panel
domain: Foundation
color: "#F97316"
status: complete
built_date: 2026-05-09
last_updated: 2026-05-09
---

# Builder Log — Workspace Panel (Tenant App Shell)

The `/app` Filament panel is operational. Tenant users log in at `/app/login` using the `web` guard.

---

## Files Created

### Panel Provider
- `app/Providers/Filament/WorkspacePanelProvider.php`
  - Path: `/app`
  - Guard: `web`
  - Login + password reset enabled
  - No registration page
  - Brand: "FlowFlex", primary color: Blue

### Pages
- `app/Filament/App/Pages/Dashboard.php`
  - Extends `Filament\Pages\Dashboard`
  - Personalized title: "Welcome back, {first_name}"
  - No widgets yet (Phase 1)
- `app/Filament/App/Pages/CompanySettings.php`
  - Form: name, slug, email, timezone, locale, currency
  - Uses `CompanyContext` to load current company
  - Calls `CompanyService::update()` on save
  - Navigation group: Settings, sort: 10
- `app/Filament/App/Pages/ModuleMarketplace.php`
  - Shows all active catalog modules grouped by domain
  - Enable/disable module actions
  - Navigation group: Settings, sort: 20

### Resources
- `app/Filament/App/Resources/UserResource.php`
  - Table: name, email, role badge, status badge, last login
  - Create: invite form (first_name, last_name, email, locale, timezone)
  - Table action: Deactivate (soft-deletes user)
  - Navigation group: Settings, sort: 5
- `app/Filament/App/Resources/RoleResource.php`
  - Form: name + permission checkbox list
  - Table: name, permissions count, users count
  - Navigation group: Settings, sort: 6

### Blade Views
- `resources/views/filament/app/pages/company-settings.blade.php`
- `resources/views/filament/app/pages/module-marketplace.blade.php`

---

## Workspace Routes Verified

```
GET /app                    (dashboard)
GET /app/login
GET /app/company-settings
GET /app/module-marketplace
GET /app/users
GET /app/users/create
GET /app/users/{record}/edit
GET /app/roles
GET /app/roles/create
GET /app/roles/{record}/edit
```

---

## Multi-Tenancy

All workspace resources use `BelongsToCompany` trait on their backing models, which applies `CompanyScope` automatically. The `SetCompanyContext` middleware fires on every web request after auth to set the company context.

---

## Related
- [[workspace-panel]]
- [[project-scaffolding]]
- [[entity-user]]
- [[multi-tenancy]]
