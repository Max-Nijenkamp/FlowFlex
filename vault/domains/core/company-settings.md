---
type: module
domain: Core Platform
domain-key: core
panel: app
module-key: core.settings
status: complete
priority: v1-core
depends-on: [foundation.panels, foundation.tenancy]
soft-depends: [core.files]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: []
permission-prefix: core.settings
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Company Settings

Source of truth for workspace configuration: timezone, locale, currency, branding, and business identity. All other modules read settings from here — they never maintain their own locale or currency config. Always-free core module, cannot be deactivated.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/foundation/filament-panels\|foundation.panels]] | lives in `/app` |
| Hard | [[domains/foundation/multi-tenancy-layer\|foundation.tenancy]] | settings scoped by company |
| Soft | [[domains/core/file-storage\|core.files]] | logo/favicon upload; without it, identity tab hides upload fields *(assumed)* |

---

## Core Features

- Workspace identity: company name, slug, logo, favicon, primary color
- Locale settings: timezone (default UTC), locale (default en), date format, number format
- Currency: base currency, currency symbol position, decimal places
- Business settings: fiscal year start month, week start day (Mon/Sun), public holiday calendar
- Branding: primary color, custom email footer, custom login logo
- Settings backed by `spatie/laravel-settings` — type-safe, per-company scoped
- GDPR: data retention period, DSAR contact email, data portability export trigger

---

## Data Model

No custom tables — stored via `spatie/laravel-settings` in its `settings` table, scoped by `company_id` (settings cache enabled per [[architecture/caching]]).

Setting groups (classes in `app/Settings/`):

| Class | Fields |
|---|---|
| `CompanyIdentitySettings` | name, slug, logo_path, favicon_path, primary_color |
| `CompanyLocaleSettings` | timezone, locale, date_format, currency, currency_position, decimal_places |
| `CompanyBusinessSettings` | fiscal_year_start_month, week_start, holiday_calendar_country |
| `CompanyPrivacySettings` | data_retention_months, dsar_email, consent_logging_enabled |

---

## DTOs

Settings classes ARE the typed objects — Filament form writes them directly; no separate Data classes *(assumed: spatie/laravel-settings convention)*. Validation on the form components (timezone in IANA list, locale in supported set, color hex, slug unique).

## Services & Actions

None — `app(CompanyLocaleSettings::class)` is the read API for all other modules.

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `CompanySettingsPage` | #7 wizard-style custom page (tabbed form) | tabs: Identity, Locale, Business, Privacy; saves per tab |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('core.settings.view-any') && BillingService::hasModule('core.settings')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`core.settings.view` · `core.settings.update`

Owner + admin roles only *(assumed)*.

---

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| spatie settings cache (`settings.*`) | 10 min (Spatie default) | automatic on save |

---

## Test Checklist

- [ ] Tenant isolation: company A settings change does not affect company B
- [ ] Locale change reflects in `SetLocale` middleware on next request
- [ ] Currency change affects new money formatting, not stored amounts
- [ ] Slug uniqueness enforced across companies
- [ ] Non-admin user cannot access the settings page (`canAccess`)
- [ ] Settings cache busts on save

---

## Build Manifest

```
app/Settings/{CompanyIdentitySettings,CompanyLocaleSettings,CompanyBusinessSettings,CompanyPrivacySettings}.php
database/settings/ (spatie settings migrations)
app/Filament/App/Pages/CompanySettingsPage.php
resources/views/filament/app/pages/company-settings.blade.php
tests/Feature/Core/CompanySettingsTest.php
```

---

## Related

- [[architecture/packages]] (`spatie/laravel-settings`)
- [[domains/core/data-privacy]]
- [[domains/core/i18n]]
