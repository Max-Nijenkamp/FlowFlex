---
domain: core
module: i18n
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Internationalisation

Locale, timezone, and date/number/currency format management per company. All format settings are inherited from Company Settings — no module maintains its own locale. This is a thin module: a `SetLocale` middleware plus a `LocaleFormatter` support class over storage owned by core.settings.

## Module-key

`core.i18n`

**Priority:** v1  
**Panel:** app (no standalone surface — locale controls are a Company Settings tab)  
**Permission prefix:** none (rides on `core.settings.update`)  
**Tables:** none (storage is `CompanyLocaleSettings`, owned by core.settings)  
**Events:** fires none · consumes none

## Sibling notes

- [[architecture]] — `SetLocale` middleware + `LocaleFormatter` support class
- Features: [[features/locale-formatting]] · [[features/locale-middleware]]

No `data-model.md` — no own tables; storage is `CompanyLocaleSettings` via `spatie/laravel-settings`, owned by core.settings. No `api.md` — no events or DTOs. No standalone Filament resource — the settings form is a tab in [[../company-settings/_module]].

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../company-settings/_module]] (core.settings) | `CompanyLocaleSettings` is the storage |

## Core Features

- Locale selection: en, nl, de, fr, es (extensible — `lang/{locale}/` files)
- Timezone: IANA timezone string (default UTC; DB stores UTC always, display converts)
- Date format: DD/MM/YYYY, MM/DD/YYYY, YYYY-MM-DD
- Number format: decimal separator (`.` or `,`), thousands separator
- Currency display: symbol position (prefix/suffix), decimal places (0, 2)
- All Filament panels read locale from Company Settings via `SetLocale` middleware
- `app()->setLocale()` called in middleware — Laravel translation keys work automatically
- Format helpers: `LocaleFormatter::date()`, `::number()`, `::money()` — single formatting API for Blade/Filament/DTOs *(assumed)*

## Permissions

Covered by `core.settings.update` — no separate keys. The settings form is gated by core.settings' `canAccess`.

## Test Checklist

- [ ] Tenant isolation: locale/format settings are company-scoped (via core.settings); one company's locale cannot affect another's
- [ ] Module gating: n/a (platform module — no standalone gated surface; middleware/formatter always active)
- [ ] Locale change switches translation strings on next request
- [ ] Date rendered per company date-format setting
- [ ] Number/money formatting respects separators + symbol position
- [ ] Timezone: stored UTC, displayed in company TZ, round-trips correctly
- [ ] Missing translation key falls back to `en`

## Build Manifest (corrected to flat paths)

```
lang/{en,nl}/ (initial translation files)
app/Support/Services/LocaleFormatter.php
app/Http/Middleware/SetLocale.php (if not already from foundation.panels — extend)
tests/Feature/Core/LocaleFormattingTest.php
```

Spec paths were already flat (no `Core/` subdir) — no correction needed.

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| fires | none | — | i18n fires no domain events |
| consumes | none | — | Consumes no domain events |
| reads | `CompanyLocaleSettings` (spatie/laravel-settings) | [[../company-settings/_module]] | `SetLocale` middleware + `LocaleFormatter` read locale/timezone/format read-only |

Data ownership: i18n owns **no tables of its own** — locale/format storage is `CompanyLocaleSettings`, owned by [[../company-settings/_module]] (core.settings). It reads that config read-only (via `SetLocale` middleware and `LocaleFormatter`), writes nothing, and effects other domains only via events (there are none) ([[../../../security/data-ownership]]).

## Related

- [[../company-settings/_module]]
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[../../../architecture/patterns/dto-pattern]] — formatted output fields
- [[../../../security/data-ownership]] · [[../../../glossary]]
