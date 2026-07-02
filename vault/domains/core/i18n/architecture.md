---
domain: core
module: i18n
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Internationalisation — Architecture

Parent: [[_module]]

Two components over storage owned by core.settings (`CompanyLocaleSettings` via `spatie/laravel-settings`). No own tables, DTOs, jobs, or state.

## SetLocale middleware

- Registered on all panels (built in foundation.panels, reads from this module).
- Reads locale/timezone/format from Company Settings and calls `app()->setLocale()` per request — Laravel translation keys resolve automatically, falling back to `en` on a missing key.

## LocaleFormatter support class

`app/Support/Services/LocaleFormatter.php` — single formatting API for Blade, Filament, and DTOs, wrapping:

| Helper | Wraps | Respects |
|---|---|---|
| `LocaleFormatter::date()` | `Carbon` | company date format + timezone (UTC → company TZ) |
| `LocaleFormatter::number()` | `NumberFormatter` | decimal + thousands separators |
| `LocaleFormatter::money()` | `brick/money` | currency, symbol position (prefix/suffix), decimal places |

The `LocaleFormatter::*` surface is *(assumed)* per the flat spec. Timezone convention: the database always stores UTC; display converts to the company timezone and round-trips back.

## Security

No permissions of its own — access rides on `core.settings.update` (the settings form is a tab in [[../company-settings/_module]]). Locale settings are company-scoped through core.settings' storage, so one company's locale cannot affect another's.
