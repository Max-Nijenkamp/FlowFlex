---
domain: core
module: company-settings
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Company Settings

Source of truth for workspace configuration: timezone, locale, currency, branding, and business identity. All other modules read settings from here — they never maintain their own locale or currency config. Always-free core module, cannot be deactivated.

- **module-key:** `core.settings` · **panel:** app · **priority:** v1-core
- **fires-events:** none · **consumes-events:** none

## Sibling notes

- [[architecture]] — `spatie/laravel-settings` classes, tabbed settings page, read API
- [[security]] — owner-only access, permissions, tenancy
- [[decisions]] — owner-only settings modules
- [[unknowns]] — UNVERIFIED / `*(assumed)*` items
- Features: [[features/settings-tabs]]

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../foundation/filament-panels/_module]] | lives in `/app` |
| Hard | [[../../foundation/multi-tenancy-layer/_module]] | settings scoped by company |
| Soft | [[../file-storage/_module]] | logo/favicon upload; without it, identity tab hides upload fields *(assumed)* |

> [!warning] UNVERIFIED — needs confirmation: exact folder slugs for the foundation panel/tenancy modules (linked by convention).

## Core Features

- Workspace identity: company name, slug, logo, favicon, primary color
- Locale settings: timezone (default UTC), locale (default en), date format, number format
- Currency: base currency, currency symbol position, decimal places
- Business settings: fiscal year start month, week start day (Mon/Sun), public holiday calendar
- Branding: primary color, custom email footer, custom login logo
- Settings backed by `spatie/laravel-settings` — type-safe, per-company scoped
- GDPR: data retention period, DSAR contact email, data portability export trigger

## Data Model

No custom tables — stored via `spatie/laravel-settings` in its `settings` table, scoped by `company_id` (settings cache enabled per [[../../../architecture/caching]]). The setting classes themselves are documented in [[architecture]]; there is no ERD.

## Test Checklist

- [ ] Tenant isolation: company A settings change does not affect company B
- [ ] Locale change reflects in `SetLocale` middleware on next request
- [ ] Currency change affects new money formatting, not stored amounts
- [ ] Slug uniqueness enforced across companies
- [ ] Non-admin user cannot access the settings page (`canAccess`)
- [ ] Settings cache busts on save

## Build Manifest (corrected to flat paths)

```
app/Settings/{CompanyIdentitySettings,CompanyLocaleSettings,CompanyBusinessSettings,CompanyPrivacySettings}.php
database/settings/ (spatie settings migrations)
app/Filament/App/Pages/CompanySettingsPage.php
resources/views/filament/app/pages/company-settings.blade.php
tests/Feature/Core/CompanySettingsTest.php
```

`app/Settings/` has no `Core/` subdir in the real layout — paths above are already flat.

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| fires | none | — | company-settings fires no events; other modules read its config synchronously |
| consumes | none | — | it maintains no local copies of other domains' data |

Data ownership: company-settings owns and writes only the `spatie/laravel-settings` `settings` rows (Identity/Locale/Business/Privacy classes, scoped by `company_id`); it reads nothing cross-domain and is itself the shared read-only source of workspace locale/currency/business/branding config for every other module ([[../../../security/data-ownership]]).

## Related

- [[../../../architecture/packages]] (`spatie/laravel-settings`)
- [[../../../architecture/caching]] — settings cache
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[../../../glossary]]
