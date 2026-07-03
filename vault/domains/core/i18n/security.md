---
domain: core
module: i18n
type: security
color: "#4ADE80"
updated: 2026-07-03
---

# I18n — Security

## Permissions

| Verb | Grants | Notes |
|---|---|---|
| `core.i18n.manage` *(assumed)* | change company locale / language settings | Owner/admin-tier; the underlying `CompanyLocaleSettings` storage is owned by core.settings |

No state machine, no command actions beyond the settings save — no further verbs required. No comms / money / file / external-API paths, so no named rate limiter beyond the panel default.

## Tenancy

Locale settings are scoped by `company_id` via `spatie/laravel-settings`; user-level language preference lives on the user record. No cross-tenant read paths.

## Encrypted fields

None — locale/language codes are not sensitive.

## Related

- [[_module|Hub]] · [[architecture]] · [[../company-settings/_module|core.settings]]
