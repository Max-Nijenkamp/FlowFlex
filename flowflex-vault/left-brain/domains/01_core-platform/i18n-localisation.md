---
type: module
domain: Core Platform
panel: app + admin
cssclasses: domain-admin
phase: 1
status: complete
migration_range: 010001–019999
last_updated: 2026-05-12
right_brain_log: "[[builder-log-core-platform-phase1]]"
---

# i18n & Localisation

Multi-language UI support for all Filament panels and the public Vue+Inertia frontend. Locale resolution is layered: user preference → company default → `Accept-Language` header → application fallback (`en`). All five supported locales ship with baseline translation files.

**Panel:** `app` + `admin` (SetLocale middleware applied to both panel middleware stacks)  
**Phase:** 1 — must be in place before any UI copy is written for Phase 2 domains

---

## Features

### Supported Locales

| Code | Language |
|------|----------|
| `en` | English |
| `nl` | Dutch |
| `de` | German |
| `fr` | French |
| `es` | Spanish |

Unsupported locale codes (including region variants like `fr-FR`) are normalised to the language-only code (`fr`). If the resulting code is still unsupported, the application falls back to `en`.

### SetLocale Middleware

`app/Http/Middleware/SetLocale.php`

Resolution order:
1. `auth()->user()->locale` — user's explicitly set locale preference (nullable; skipped if null)
2. `Accept-Language` HTTP header — first matching supported locale; region codes normalised
3. `config('app.locale')` — application default (currently `en`)

Applied to both Filament panel middleware stacks and the web middleware group. Must run after authentication middleware so the user is available.

### Lang Files

Structure: `lang/{locale}/ui.php`

Files created for all 5 supported locales at Phase 1:
- `lang/en/ui.php`
- `lang/nl/ui.php`
- `lang/de/ui.php`
- `lang/fr/ui.php`
- `lang/es/ui.php`

Each file returns a flat associative array of UI string keys. Domain-specific strings are added to these files as domains are built.

### User Locale Column

- `User` model gains a `locale` column: `string`, nullable, default `null`
- `null` means "inherit from company locale"
- Users set their locale preference in their profile settings page (Phase 2+)

### Company Locale Column

- `Company` model gains a `locale` column: `string`, default `'en'`
- Serves as the workspace default for all users without a personal locale set
- Set via [[company-workspace-settings]]

### Future: Number, Date, Currency Formatting

Phase 1 delivers locale switching for UI strings only. The following are planned for a future phase:

- **Number formatting** — `NumberFormatter` (PHP `intl` extension) per user locale
- **Date formatting** — Carbon locale applied to all `->format()` and `->diffForHumans()` calls
- **Currency display** — symbol and decimal separator per locale (e.g. `€1.234,56` in `nl`)

---

## Data Model

No new tables. Locale columns added to existing tables:

```
users {
    string locale "nullable — null = inherit company locale"
    ...
}

companies {
    string locale "default: en"
    ...
}
```

---

## Permissions

None — every authenticated user can always set their own locale preference. No permission gate required.

---

## Related

- [[MOC_CorePlatform]]
- [[company-workspace-settings]] — company default locale set here
- [[entity-user]]
- [[entity-company]]
