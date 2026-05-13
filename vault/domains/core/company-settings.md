---
type: module
domain: Core Platform
panel: app
module-key: core.settings
status: planned
color: "#4ADE80"
---

# Company Settings

> Company profile, branding, slug, timezone, locale, currency, and feature flags — the canonical workspace configuration that every other module inherits.

**Panel:** `app`
**Module key:** `core.settings`

## What It Does

Company Settings is the single authoritative source for workspace-level configuration in FlowFlex. The company owner manages their company's display name, branding assets, timezone, locale, and currency. All domain modules inherit locale and currency from this source — no module maintains its own copy of these preferences. Changes to the primary colour trigger a queued Vite/Tailwind recompile job so the new colour takes effect in the compiled app panel theme. The slug is immutable after creation and acts as the company's unique subdomain identifier. All changes are audit-logged.

## Features

### Core
- Identity fields: name, slug (read-only after creation), email, timezone (IANA list), locale (`en`, `nl`, `de`, `fr`, `es`), currency (`EUR`, `USD`, `GBP`, `CHF`, `SEK`, `NOK`, `DKK`, `PLN`)
- Branding: logo upload (PNG/SVG max 5 MB via spatie/laravel-media-library), favicon upload (ICO/PNG max 1 MB), primary colour picker (hex)
- Slug rules: auto-generated from name on company creation (lowercase, alphanumeric + hyphens, max 63 chars), globally unique, immutable after first save
- Access gate: `canAccess()` checks `core.settings.manage` permission — only the `owner` role holds this by default

### Advanced
- Primary colour change triggers queued `RecompileTenantThemeJob` — new colour applied to Filament app panel theme without page reload (uses compiled CSS custom properties)
- Logo and favicon stored via `spatie/laravel-media-library` with S3/R2 backend; retrieved via signed URL
- All field changes passed through `AuditLogger::log()` with event type `company.settings.updated` — before/after diff captured per field
- Danger zone: company deletion request (owner must type company name to confirm) — soft-deletes company and all users, triggers cancellation flow in Billing Engine

### AI-Powered
- Timezone auto-detection: suggest timezone based on company email domain's geographic origin on first setup
- Brand colour suggestions: based on logo upload, AI suggests a complementary primary colour for the panel theme

## Data Model

```erDiagram
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
        string status
        timestamp onboarded_at
        timestamp deleted_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `slug` | Immutable after creation; globally unique |
| `primary_color` | Hex value; drives Filament app panel theme |
| `logo_path` | Media library path; served via signed URL |
| `status` | trial / active / suspended / cancelled |

## Permissions

- `core.settings.view`
- `core.settings.manage`
- `core.settings.manage-branding`
- `core.settings.change-locale`
- `core.settings.delete-company`

## Filament

- **Resource:** None
- **Pages:** `CompanySettingsPage` — sections: Identity, Branding, Localization, Danger Zone
- **Custom pages:** `CompanySettingsPage`
- **Widgets:** None
- **Nav group:** Settings (app panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Notion Workspace Settings | Workspace name, logo, and branding |
| HubSpot Account Settings | CRM company profile and defaults |
| Slack Workspace Settings | Workspace configuration and branding |
| Monday.com Admin | Workspace settings and localization |

## Related

- [[i18n]]
- [[file-storage]]
- [[setup-wizard]]
- [[billing-engine]]
- [[audit-log]]
