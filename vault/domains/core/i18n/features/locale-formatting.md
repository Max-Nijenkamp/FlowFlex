---
domain: core
module: i18n
feature: locale-formatting
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Locale Formatting

Parent: [[../_module]] · See [[../architecture]]

Per-company date, number, and money formatting.

- `LocaleFormatter::date()` / `::number()` / `::money()` — one formatting API for Blade, Filament, and DTOs *(assumed)*.
- Dates render per the company date-format setting; timezone stored UTC, displayed in company TZ, round-trips correctly.
- Numbers/money respect decimal + thousands separators and currency symbol position.
- `SetLocale` middleware switches translation strings per request; a missing key falls back to `en` (that middleware/translation surface is detailed in [[locale-middleware]]).

## UI

- **Kind**: background — `LocaleFormatter` is a support class (`app/Support/Services/LocaleFormatter.php`) called from Blade/Filament/DTOs; it has no page of its own. The user-facing configuration surface is a tab in [[../../company-settings/_module]] (owned by core.settings), not by this module.
- **Page**: background (no page) — a formatting API (`LocaleFormatter::date()` / `::number()` / `::money()`). The setting values it reads are edited on the Company Settings locale tab (`/app`, owned by core.settings).
- **Layout**: no layout of its own — it produces formatted strings consumed by other modules' views/DTOs.
- **Key interactions**: any Blade/Filament/DTO output calls `LocaleFormatter::date($value)` / `::number($value)` / `::money($value)` → the helper reads the company's `CompanyLocaleSettings` (date format, separators, timezone, currency, symbol position) and returns a formatted string. Dates: DB stores UTC → helper converts to the company timezone and round-trips back.
- **States**: empty (n/a) · loading (n/a — synchronous formatting) · error (missing/invalid setting → falls back to `en`/defaults) · selected (n/a).
- **Gating**: no permission of its own — formatting is available wherever code runs; the *settings that drive it* are gated by `core.settings.update` on the Company Settings tab.

## Data

- Owns / writes: **no tables of its own.** It reads settings and writes nothing.
- Reads: `CompanyLocaleSettings` (via `spatie/laravel-settings`) — **owned by [[../../company-settings/_module]]** (core.settings) — read-only. Uses `brick/money` and `NumberFormatter`/`Carbon` for the actual formatting.
- Cross-domain writes: none — effects other domains only via events (there are none) ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (no domain events) — reads locale settings synchronously from core.settings storage.
- Feeds: none — it is a synchronous formatting helper, not an event producer. Every other module's Blade/Filament/DTO output *calls into* it read-only.
- Shared entity: `CompanyLocaleSettings` — reference/config data **owned by [[../../company-settings/_module]]** (core.settings); i18n reads it, never writes it.

> [!warning] UNVERIFIED — the `LocaleFormatter::date()/::number()/::money()` API surface is marked *(assumed)* in [[../_module]] / [[../architecture]]; the exact method set is inferred from the flat spec, not confirmed against code.
