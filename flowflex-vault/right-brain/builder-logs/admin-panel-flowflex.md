---
type: builder-log
module: admin-panel-flowflex
domain: Foundation
color: "#F97316"
status: complete
built_date: 2026-05-09
last_updated: 2026-05-09
---

# Builder Log — Admin Panel (FlowFlex Internal)

The `/admin` Filament panel is operational. FlowFlex staff can log in at `/admin/login` using the `admin` guard.

---

## Files Created

### Panel Provider
- `app/Providers/Filament/AdminPanelProvider.php`
  - Path: `/admin`
  - Guard: `admin`
  - Login + password reset enabled
  - No registration page
  - Brand: "FlowFlex Admin", primary color: Orange

### Resources
- `app/Filament/Admin/Resources/CompanyResource.php`
  - List with status badge, user count, module count
  - Create form: company details + owner details section (visible on create only)
  - Edit form: company details only
  - Table actions: Activate, Suspend, Cancel (with confirmation)
  - Calls `CompanyCreationService` on create, `CompanyService` on status changes
- `app/Filament/Admin/Resources/AdminUserResource.php`
  - Create/Edit: name, email, password, role (super_admin|support|billing|developer)
  - Password hashed via `bcrypt()`
- `app/Filament/Admin/Resources/ModuleCatalogResource.php`
  - Full CRUD for module pricing
  - module_key, domain, name, per_user_monthly_price, is_active
- `app/Filament/Admin/Resources/PlatformAnnouncementResource.php`
  - Target: all or specific company
  - Send action marks `sent_at`
  - Only draft announcements can be edited
- `app/Filament/Admin/Resources/CompanyFeatureFlagResource.php`
  - company_id nullable (null = global flag)
  - Uses `withoutGlobalScopes()` in table query

### Resource Pages (15 files)
All standard List/Create/Edit pages for each resource above.

---

## Admin Routes Verified

```
GET /admin/login
GET /admin/companies
GET /admin/companies/create
GET /admin/companies/{record}/edit
GET /admin/admin-users
GET /admin/admin-users/create
GET /admin/admin-users/{record}/edit
GET /admin/module-catalogs
GET /admin/company-feature-flags
GET /admin/platform-announcements
```

---

## Auth Model

`App\Models\Admin` uses `admin` guard, separate `admins` table, `HasUlids`, `SoftDeletes`.
Auth config in `config/auth.php` has `admins` provider pointing to `Admin::class`.

---

## Related
- [[admin-panel-flowflex]]
- [[project-scaffolding]]
- [[entity-admin]]
