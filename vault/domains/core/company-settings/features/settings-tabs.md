---
domain: core
module: company-settings
feature: settings-tabs
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Settings Tabs

Parent: [[../_module]] · See [[../architecture]] · [[../security]]

`CompanySettingsPage` is a tabbed, wizard-style custom Filament page. Each tab maps to one `spatie/laravel-settings` class and saves independently.

| Tab | Settings class | Fields |
|---|---|---|
| Identity | `CompanyIdentitySettings` | name, slug, logo, favicon, primary color |
| Locale | `CompanyLocaleSettings` | timezone, locale, date/number format, currency, symbol position, decimals |
| Business | `CompanyBusinessSettings` | fiscal year start month, week start, holiday calendar country |
| Privacy | `CompanyPrivacySettings` | data retention months, DSAR email, consent logging |

- Validation is on the form components: timezone in IANA list, locale in supported set, color hex, slug unique per company.
- Saving a tab busts the spatie settings cache automatically.
- Identity's upload fields hide when `core.files` is inactive *(assumed)*.

## UI

- **Kind**: custom-page
- **Page**: `CompanySettingsPage` (`/app`, `app/Filament/App/Pages/CompanySettingsPage.php` + `resources/views/filament/app/pages/company-settings.blade.php`) — ui-strategy row #7, tabbed/wizard-style.
- **Layout**: single custom Filament page with four tabs — **Identity · Locale · Business · Privacy** — each tab a form bound to one `spatie/laravel-settings` class, each saving independently.
- **Key interactions**:
  1. Owner opens the page → each tab hydrates from `app(<SettingsClass>)`.
  2. Edit fields in a tab → **Save** persists only that tab's settings class.
  3. Save busts the spatie settings cache automatically; a locale change is picked up by `SetLocale` middleware on the next request.
  4. Identity's logo/favicon upload fields are hidden when `core.files` is inactive *(assumed)*.
- **States**: empty (defaults — timezone UTC, locale en) · loading (form skeleton on tab hydrate) · error (inline validation: timezone not in IANA list, unsupported locale, bad hex color, slug not unique per company) · selected (active tab highlighted).
- **Gating**: `core.settings.view` / `core.settings.update`; `canAccess()` additionally requires `BillingService::hasModule('core.settings')` **and** `hasRole('owner')` (owner-only, see [[../security]] / [[../decisions]]).

## Data

- Owns / writes: the `settings` rows for `CompanyIdentitySettings`, `CompanyLocaleSettings`, `CompanyBusinessSettings`, `CompanyPrivacySettings`, all scoped by `company_id` via `spatie/laravel-settings`. This module owns no custom tables.
- Reads: none from other domains for this feature (it is the source of truth other modules read from).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: no events. Every other module **reads** this config read-only via `app(CompanyLocaleSettings::class)` etc. (locale, currency, business config); those modules never keep their own copy.
- Shared entity: this module **owns** the workspace locale/currency/business/branding config that the rest of the platform treats as shared read-only reference data.
