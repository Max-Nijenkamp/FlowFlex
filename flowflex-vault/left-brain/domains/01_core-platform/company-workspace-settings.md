---
type: module
domain: Core Platform
panel: app
cssclasses: domain-admin
phase: 1
status: in-progress
migration_range: 010001–019999
last_updated: 2026-05-10
right_brain_log: "[[builder-log-core-platform-phase1]]"
---

# Company & Workspace Settings

Filament settings page where the company owner configures the core identity of their workspace: name, locale, timezone, currency, and branding (logo, favicon, primary colour). All changes are audit-logged. This is the canonical place to set workspace-level defaults that all users and domain modules inherit.

**Panel:** `app`  
**Phase:** 1 — required before domain modules can inherit company locale/currency/branding

---

## Features

### Identity Fields

| Field | Type | Notes |
|-------|------|-------|
| Name | Text | Company display name |
| Slug | Text | Subdomain identifier; auto-generated from name on creation; immutable after first save |
| Email | Email | Primary contact / company email domain |
| Timezone | Select | IANA timezone list (e.g. `Europe/Amsterdam`) |
| Locale | Select | Supported: `en`, `nl`, `de`, `fr`, `es` |
| Currency | Select | Supported: `EUR`, `USD`, `GBP`, `CHF`, `SEK`, `NOK`, `DKK`, `PLN` |

### Slug Rules

- Generated from `name` on company creation: lowercase, alphanumeric + hyphens, max 63 chars
- Must be globally unique (used as subdomain identifier)
- Immutable after creation — cannot be changed via the UI (requires a support request)
- Slug field rendered as read-only text after first save

### Branding Section

| Field | Filament Component | Notes |
|-------|-------------------|-------|
| Logo | `FileUpload` | PNG/SVG, max 5 MB; stored via medialibrary, path in `logo_path` |
| Favicon | `FileUpload` | ICO/PNG, max 1 MB; stored in `favicon_path` |
| Primary colour | `ColorPicker` | Hex value; applied to Filament app panel theme |

Branding changes trigger a Vite/Tailwind CSS rebuild via a queued job so the new primary colour takes effect in the compiled theme.

### Access Control

- `canAccess()` checks `core.company.settings.manage` permission
- Only the `owner` role holds this permission by default

### Audit Logging

All field changes are passed through `AuditLogger::log()` with a `company.settings.updated` event type. Before/after diff captured for every changed field.

---

## Data Model

All fields live on the `companies` table (managed by Phase 0 Foundation migration + this module's additions):

```
companies {
    ulid id PK
    string name
    string slug "unique"
    string email
    string timezone "default: UTC"
    string locale "default: en"
    string currency "default: EUR"
    string logo_path
    string favicon_path
    string primary_color "default: #6366F1"
    string status "default: active"
    timestamps created_at/updated_at
    timestamp deleted_at
}
```

---

## Permissions

```
core.company.settings.manage
```

---

## Related

- [[MOC_CorePlatform]]
- [[file-storage]] — logo and favicon uploads use medialibrary
- [[i18n-localisation]] — company locale is the default for all users without a personal locale set
- [[setup-wizard]] — Steps 2 and 5 link to this page
- [[entity-company]]
- [[audit-log]]
