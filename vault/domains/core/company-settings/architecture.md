---
domain: core
module: company-settings
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Company Settings — Architecture

Parent: [[_module]] · See also [[security]] · [[features/settings-tabs]]

## Settings classes (`spatie/laravel-settings`)

The setting classes ARE the typed objects — the Filament form writes them directly; there are no separate Data classes *(assumed: spatie/laravel-settings convention)*.

| Class (`app/Settings/`) | Fields |
|---|---|
| `CompanyIdentitySettings` | name, slug, logo_path, favicon_path, primary_color |
| `CompanyLocaleSettings` | timezone, locale, date_format, currency, currency_position, decimal_places |
| `CompanyBusinessSettings` | fiscal_year_start_month, week_start, holiday_calendar_country |
| `CompanyPrivacySettings` | data_retention_months, dsar_email, consent_logging_enabled |

Persisted via spatie's `settings` table, scoped by `company_id`.

## Read API (no service)

There is no service or action layer — `app(CompanyLocaleSettings::class)` (and the other classes) is the read API that every other module uses for locale, currency, and business config. Modules never maintain their own locale/currency config.

## UI — CompanySettingsPage

`CompanySettingsPage` is a custom, wizard-style tabbed Filament page (ui-strategy row #7). Tabs: **Identity, Locale, Business, Privacy**; each tab saves independently. Form-level validation: timezone in the IANA list, locale in the supported set, color hex, slug unique per company. See [[features/settings-tabs]].

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| spatie settings cache (`settings.*`) | 10 min (Spatie default) | automatic on save |

Currency/locale changes affect **new** formatting only, not stored amounts. A locale change is picked up by the `SetLocale` middleware on the next request.
