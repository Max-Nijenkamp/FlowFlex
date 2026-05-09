---
type: module
domain: Foundation
panel: admin
phase: 0
status: planned
migration_range: 000000–009999
last_updated: 2026-05-09
---

# Admin Panel — FlowFlex Internal

The `/admin` Filament panel for FlowFlex staff (Max and team). Not visible to tenants. Used to manage all tenant companies, monitor system health, handle support, and configure billing.

- **URL**: `/admin`
- **Guard**: `admin`
- **Model**: `Admin` (separate table — never mixed with tenant `users`)

---

## What This Panel Does

FlowFlex staff log in here to manage every aspect of the platform. From here you can view all companies, impersonate users for support, manage billing, monitor queues and health, and send platform-wide announcements. It is the operational nerve centre for running FlowFlex as a business. Tenant users have no knowledge this panel exists and cannot access it under any circumstance — the `admin` guard is completely separate from the `web` guard.

---

## Feature Groups

| Feature Group | Key Features | Notes |
|---|---|---|
| Company Management | List all companies, create/edit/suspend/cancel, view module status — create company + set owner + send invite email | Companies = tenants |
| Module Management | View/override active modules per company, force-activate/deactivate | Bypasses normal billing gate |
| User Impersonation | Log in as any tenant user for support, full audit trail recorded | Spatie ActivityLog entry on every impersonation start/end |
| Billing & Subscriptions | View Stripe subscription per company, MRR, trial status, payment history | Read Stripe data; module activation changes trigger Stripe API |
| System Health | Horizon (queues), Pulse (metrics), Telescope (dev), Reverb (WebSocket connections) | All embedded into admin dashboard |
| Announcements | Create platform-wide notifications to all companies, or specific companies | Stored in `platform_announcements`, delivered via notification system |
| Support Logs | View activity logs for any company without impersonating | Read-only — does not create a session as the user |
| Admin User Management | Create/manage FlowFlex staff accounts (other admins) | Role: super_admin, support, billing, developer |
| Feature Flags | Toggle beta features per company or globally | Foundation for PLG; stored in `company_feature_flags` |

---

## Admin Roles (FlowFlex Staff)

These roles control what FlowFlex staff members can do in the admin panel. They are separate from tenant user roles.

| Role | Access Level |
|---|---|
| `super_admin` | Full access to everything, including creating other admins. Reserved for Max. |
| `support` | Read all company data, impersonate users for debugging. Cannot change billing or create admins. |
| `billing` | Manage Stripe subscriptions, view MRR and payment history. Cannot impersonate or access support logs. |
| `developer` | System health, queue monitoring (Horizon), request inspection (Telescope), Pulse metrics. Cannot access billing or impersonate. |

---

## Key Filament Resources

| Resource | Panel Section | Description |
|---|---|---|
| `CompanyResource` | Companies | Full CRUD for all tenant companies — create, edit, suspend, cancel, view module subscriptions |
| `AdminUserResource` | Team | FlowFlex staff management — create accounts, assign roles, deactivate |
| `BillingResource` | Billing | Per-company Stripe subscription view, manual billing adjustments, module price management |
| `AnnouncementResource` | Announcements | Create/send platform-wide notifications, targeting all companies or a specific company |
| `ImpersonationResource` | Support | Initiate / end user impersonation sessions with forced audit logging |
| `ActivityLogResource` | Support | Browse spatie/laravel-activitylog entries — filterable by company, user, date |
| `FeatureFlagResource` | Settings | Enable/disable beta features globally or per company |

### Dashboard Widgets

| Widget | Data |
|---|---|
| MRR Widget | Total monthly recurring revenue from Stripe |
| Active Companies Widget | Count of companies by status (trial, active, suspended, cancelled) |
| Trials Widget | Companies in trial period, days remaining |
| Failed Jobs Widget | Count and detail of failed Horizon queue jobs |
| Queue Depth Widget | Jobs pending per queue from Horizon |
| WebSocket Connections Widget | Active Reverb connections count |

---

## Company Creation & Owner Invite Flow

When a new company is created in the admin panel, the flow is:

1. Admin fills the `CompanyResource` create form: company name, slug, primary email, country, timezone, currency.
2. On save:
   - `Company` record created (`status = trial`)
   - Admin specifies owner: first name, last name, and email address
   - `User` record created (`company_id = company.id`, `status = invited`)
   - Owner role created scoped to company: `Role::create(['name' => 'owner', 'team_id' => $company->id])` with all permissions (`*.*.*`)
   - Role assigned to owner User
   - Starter modules activated as selected by admin during company creation (inserts into `company_module_subscriptions`)
   - Stripe customer created (name, email, metadata: `company_id`)
   - Invite email sent to owner with a one-time token link (valid 7 days)
3. Admin sees the company in the list with status `trial · owner invited`

**Owner invite email** contains a link to `/app/login?invite_token={token}` — the Filament workspace panel login page with the owner's email pre-filled and a password-set prompt. On first login: owner sets password, `email_verified_at` is set, `status` changes to `active`, and the panel session is created with the owner role pre-assigned.

---

## Database Tables

### `admins`

| Column | Type | Notes |
|---|---|---|
| `id` | ULID | Primary key |
| `name` | string | Full name |
| `email` | string | Unique, login credential |
| `password` | string | Bcrypt hashed |
| `role` | enum | super_admin, support, billing, developer |
| `last_login_at` | timestamp | Nullable |
| `deleted_at` | timestamp | Soft delete |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### `platform_announcements`

| Column | Type | Notes |
|---|---|---|
| `id` | ULID | Primary key |
| `title` | string | Short display title |
| `body` | text | Full announcement content (Markdown) |
| `target` | enum | `all`, `company` |
| `target_value` | string | Nullable — `company_id` if target is `company`, null if `all` |
| `sent_at` | timestamp | Nullable — null means draft, set when dispatched |
| `created_by` | ULID | FK to admins.id |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

### `company_feature_flags`

| Column | Type | Notes |
|---|---|---|
| `id` | ULID | Primary key |
| `company_id` | ULID | FK to companies.id — null means global flag |
| `flag` | string | Unique flag key e.g. `beta.ai-copilot` |
| `enabled` | boolean | |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

---

## Security Notes

- The `admin` guard uses a separate session store (prefix `admin_session`) so an admin session and a user session can coexist in the same browser without collision.
- Impersonation is initiated via a signed URL with a 5-minute TTL. The impersonation session is tagged in the activity log with the admin's ID.
- All destructive actions (suspend company, cancel subscription, delete admin) require a confirmation modal in the Filament resource and are logged to spatie/laravel-activitylog.

---

## Features

- Company list with plan, status, and module subscription overview
- Company create, edit, suspend, and cancel workflows
- Module override per company (force-activate or deactivate any module regardless of billing)
- User impersonation with mandatory audit log entry (start and end)
- Stripe billing view per company: active modules, MRR contribution, trial days, payment history
- Horizon queue monitoring dashboard embedded in admin panel
- Pulse application health dashboard embedded in admin panel
- Telescope request inspection (development environment only)
- Platform announcements to all companies or specific companies
- Admin role management for FlowFlex staff (super_admin, support, billing, developer)
- Feature flag management per company or globally

---

## Related

- [[MOC_Foundation]]
- [[workspace-panel]]
- [[entity-company]]
- [[auth-rbac]]
- [[multi-tenancy]]
