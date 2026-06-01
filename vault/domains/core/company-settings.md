---
type: module
domain: Core Platform
panel: app
module-key: core.settings
status: planned
color: "#4ADE80"
---

# Company Settings

Source of truth for workspace configuration: timezone, locale, currency, branding, and business identity. All other modules read settings from here — they never maintain their own locale or currency config.

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

No custom tables — stored via `spatie/laravel-settings` in its `settings` table, scoped by `company_id`.

Key setting groups:
- `CompanyIdentitySettings` — name, slug, logo path, favicon path, primary color
- `CompanyLocaleSettings` — timezone, locale, date format, currency, currency position
- `CompanyBusinessSettings` — fiscal year start, week start, holiday calendar country
- `CompanyPrivacySettings` — data retention months, DSAR email, consent logging enabled

---

## Filament

**`/app` panel:**
- `CompanySettingsPage` (custom page) — tabbed settings form: Identity, Locale, Business, Privacy
- Changes saved immediately; locale/currency changes take effect on next page load

---

## Related

- [[architecture/packages]] (`spatie/laravel-settings`)
- [[domains/core/data-privacy]]
