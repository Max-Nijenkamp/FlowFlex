---
domain: core
module: i18n
feature: locale-middleware
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Locale Selection & SetLocale Middleware

Parent: [[../_module]] · See [[../architecture]]

Per-request locale selection: the `SetLocale` middleware reads the company's chosen locale/timezone/format from Company Settings and calls `app()->setLocale()`, so Laravel translation keys (`lang/{locale}/`) resolve automatically, falling back to `en` on a missing key. Supported locales: en, nl, de, fr, es (extensible via new `lang/{locale}/` files). This is the translation half of i18n; the formatting half is [[locale-formatting]].

## UI

- **Kind**: background — `app/Http/Middleware/SetLocale.php` runs per request on all panels; no page of its own. The locale-selection *control* is a field on the Company Settings locale tab (owned by core.settings).
- **Page**: background (no page) — HTTP middleware. Trigger: every authenticated panel request passes through `SetLocale` before the response renders. The user picks the locale on the Company Settings locale tab (`/app`, owned by core.settings).
- **Layout**: no layout of its own — the effect is that all rendered translation strings switch language for the request.
- **Key interactions**: request arrives → `SetLocale` reads `CompanyLocaleSettings` → `app()->setLocale($locale)` → all `__()` / `@lang` keys resolve in that locale (missing key → `en` fallback). Changing the locale on the settings tab takes effect on the next request.
- **States**: empty (n/a) · loading (n/a — synchronous middleware) · error (missing translation key → falls back to `en`) · selected (the active company locale).
- **Gating**: no permission of its own — the middleware runs for every request. The locale-selection field is gated by `core.settings.update` on the Company Settings tab.

## Data

- Owns / writes: **no tables of its own.** The middleware writes nothing — it only sets the request-scoped app locale.
- Reads: `CompanyLocaleSettings` (via `spatie/laravel-settings`) — **owned by [[../../company-settings/_module]]** (core.settings) — read-only. Reads `lang/{locale}/` translation files.
- Cross-domain writes: none — effects other domains only via events (there are none) ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (no domain events) — reads locale settings synchronously per request.
- Feeds: none — sets request-scoped locale; every panel/page downstream benefits, but via `app()->setLocale()`, not an event.
- Shared entity: `CompanyLocaleSettings` — config data **owned by [[../../company-settings/_module]]** (core.settings); i18n reads it read-only.

> [!warning] UNVERIFIED — [[../_module]] notes the `SetLocale` middleware may already be provided by foundation.panels and merely extended here; the exact ownership split (foundation vs. i18n) is not confirmed against code.

## Related

- [[../_module]] · [[../architecture]] · [[locale-formatting]]
- [[../../company-settings/_module]] · [[../../../../security/data-ownership]]
