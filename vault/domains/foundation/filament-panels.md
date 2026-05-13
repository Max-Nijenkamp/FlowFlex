---
type: module
domain: Foundation
panel: (scaffold — no panel)
module-key: foundation.panels
status: planned
color: "#4ADE80"
---

# Filament Panels

> Two Filament 5 panels — `/admin` for FlowFlex staff and `/app` for tenant users — separated by independent guards, models, and middleware stacks.

**Domain:** Foundation
**Module key:** `foundation.panels`

## What It Does

FlowFlex registers two completely independent Filament panels. The `/admin` panel is exclusively for FlowFlex staff (Max and team) — it uses the `admin` guard authenticating against the `admins` table. The `/app` panel is for all tenant users — it uses the `web` guard authenticating against the `users` table. The two guards cannot cross-authenticate: an admin session cannot open a tenant resource, and a tenant session cannot touch the admin panel. Both panels use Filament's built-in login and password reset pages; no external auth package is installed.

## Features

### Core
- `AdminPanelProvider` registered at `/admin` — guard `admin`, model `App\Models\Admin`, session prefix `admin_session`
- `WorkspacePanelProvider` (tenant panel) registered at `/app` — guard `web`, model `App\Models\User`
- Both providers listed in `bootstrap/providers.php`
- Both panels expose only `->login()->passwordReset()` — no registration pages exist anywhere in the project
- Unauthenticated requests to `/app/*` redirect to `/app/login`; unauthenticated requests to `/admin/*` redirect to `/admin/login`

### Advanced
- Admin panel middleware: `auth:admin` only — no company context middleware
- Tenant panel auth middleware: `auth`, `verified`, `SetCompanyContext` — company scope applied before any resource loads
- Admin panel navigation: Companies, Users, Billing, Announcements, Support, System Health, Settings
- Tenant panel navigation: grouped per activated domain module (HR, Finance, CRM, Projects, etc.)
- Filament themes compiled per panel — tenant panel primary colour overridden by company branding setting
- Company impersonation from admin panel: signed URL with 5-minute TTL, mandatory audit log entry on start and end

### AI-Powered
- Admin panel embeds Laravel Horizon (queue depth, failed jobs), Laravel Pulse (health metrics), and Laravel Telescope (dev only) via protected navigation links
- All three tools gated by `AdminAccessGate` — only authenticated `Admin` model users can access them

## Data Model

```erDiagram
    panel_sessions {
        string id PK
        string guard
        json payload
        integer last_activity
    }

    company_feature_flags {
        ulid id PK
        ulid company_id FK
        string flag
        boolean enabled
        timestamps created_at/updated_at
    }

    platform_announcements {
        ulid id PK
        string title
        text body
        string target
        string target_value
        timestamp sent_at
        ulid created_by FK
        timestamps created_at/updated_at
    }
```

| Table | Purpose |
|---|---|
| `panel_sessions` | Separate session store per guard (admin vs web) |
| `company_feature_flags` | Feature flags toggled per company or globally from admin panel |
| `platform_announcements` | Platform-wide or company-specific announcements sent from admin panel |

## Permissions

- `foundation.panels.access-admin`
- `foundation.panels.access-app`
- `foundation.panels.impersonate`
- `foundation.panels.manage-flags`
- `foundation.panels.send-announcements`

## Filament

- **Resource:** `CompanyResource`, `AdminUserResource`, `AnnouncementResource` (admin panel)
- **Pages:** Dashboard with MRR widget, Active Companies widget, Trials widget, Failed Jobs widget
- **Custom pages:** Horizon embed, Pulse embed, Telescope embed (admin panel, dev env only)
- **Widgets:** MRR Widget, Active Companies Widget, Queue Depth Widget, WebSocket Connections Widget
- **Nav group:** System (admin panel) — no nav group visible to tenant users

## Related

- [[laravel-scaffold]]
- [[multi-tenancy-layer]]
- [[test-suite]]
