---
type: module
domain: Core Platform
panel: app
module-key: core.sandbox
status: planned
color: "#4ADE80"
---

# Sandbox Environments

> Isolated test environments per company — provision a sandbox, experiment safely with data and configuration, and reset it without touching production.

**Panel:** `app`
**Module key:** `core.sandbox`

## What It Does

Sandbox Environments lets a company provision a separate isolated workspace populated with realistic dummy data. Users can test new configurations, import experimental data, or train new employees without any risk to production records. A sandbox is a second company record in the same FlowFlex database, owned by the same company, but completely isolated by the multi-tenancy layer. The Filament sandbox page controls provisioning, resetting, and deprovisioning. Sandbox access is separate from production access — a user is explicitly granted sandbox access by the company owner.

## Features

### Core
- Provision sandbox: creates a sibling company record with `is_sandbox = true` and `parent_company_id` pointing to the production company
- Sandbox seeded with realistic dummy data matching the production company's active modules (using the `LocalDemoDataSeeder` pattern per domain)
- Sandbox panel accessible at `/app` with a sandbox mode banner clearly distinguishing it from production
- Reset sandbox: wipes all sandbox data and re-seeds from the demo data seeder — idempotent operation
- Deprovision sandbox: soft-deletes the sandbox company record

### Advanced
- Sandbox access list: owner grants specific users access to the sandbox — different from their production role if needed
- Configuration sync: optionally copy production settings (module configuration, workflow templates, custom fields) into the sandbox without copying real data
- Time-limited sandboxes: sandbox companies auto-expire after 90 days unless renewed — owner notified 14 days before expiry
- Sandbox data isolation: `CompanyScope` enforces that sandbox data is invisible from production and vice versa — they are separate company records

### AI-Powered
- AI-generated demo data: sandbox is seeded with AI-generated realistic data matching the company's industry, headcount, and active modules rather than generic fixture data
- Change impact preview: before applying a configuration change in production, preview its effect in the sandbox automatically

## Data Model

```erDiagram
    companies {
        ulid id PK
        ulid parent_company_id FK
        boolean is_sandbox
        timestamp sandbox_expires_at
        string status
        timestamps created_at/updated_at
    }

    sandbox_access {
        ulid id PK
        ulid sandbox_company_id FK
        ulid user_id FK
        string role
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `is_sandbox` | Distinguishes sandbox from production company records |
| `parent_company_id` | Links sandbox back to its production company |
| `sandbox_expires_at` | Auto-expiry date; null for non-sandbox companies |

## Permissions

- `core.sandbox.provision`
- `core.sandbox.reset`
- `core.sandbox.deprovision`
- `core.sandbox.manage-access`
- `core.sandbox.access`

## Filament

- **Resource:** None
- **Pages:** `SandboxPage` — provision/reset/deprovision controls, access list management, expiry countdown
- **Custom pages:** `SandboxPage`
- **Widgets:** `SandboxBannerWidget` — persistent banner shown in all sandbox panel views
- **Nav group:** Settings (app panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Salesforce Sandboxes | CRM test environment management |
| Workday Sandbox | HR system isolated test environment |
| Xero Demo Company | Accounting demo environment |
| HubSpot Demo Account | CRM sandbox and training environment |

## Implementation Notes

**Multi-tenancy architecture:** The sandbox is a separate `companies` row with `is_sandbox = true` and `parent_company_id` pointing to the production company. The `CompanyScope` global scope naturally isolates sandbox data (it filters by `company_id` which differs between sandbox and production). The `SetCompanyContext` middleware must correctly resolve the sandbox company — the user logs in to the production FlowFlex app and switches to sandbox mode via a company-switcher dropdown. `sandbox_access.role` determines which permissions the user has in the sandbox context (may differ from production).

**Sandbox switching UI:** A company-switcher dropdown in the Filament panel header allows users with `core.sandbox.access` permission to switch between production and sandbox. On switch, the session's `company_id` is updated (via `CompanyContextService::switchTo($sandboxCompanyId)`). A persistent `SandboxBannerWidget` appears at the top of every panel page in sandbox mode.

**Provisioning job:** `ProvisionSandboxJob` is dispatched when the sandbox is created. It:
1. Creates a new `companies` record with `is_sandbox = true`, `parent_company_id`, `sandbox_expires_at = now()->addDays(90)`.
2. Copies the production company's active module subscriptions (creates `company_module_subscriptions` rows for the sandbox company).
3. Copies company settings and workflow configurations (optional, per `core.sandbox.manage-access` configuration).
4. Seeds the sandbox with demo data by running `LocalDemoDataSeeder` scoped to the sandbox `company_id` — the seeder must already handle the module-scoped seeding pattern.

**Reset job:** `ResetSandboxJob` hard-deletes all non-companies records for the sandbox `company_id` (bypassing soft-delete — this is an intentional hard delete for a reset operation), then re-runs the demo seeder. This is an exception to the "no hard deletes" convention — document it explicitly in the job class docblock.

**Auto-expiry:** A daily scheduled job `ExpireSandboxEnvironmentsJob` queries `companies` where `is_sandbox = true` and `sandbox_expires_at < now()` and `status = active`. For each: update `status = expired`, send `SandboxExpiredNotification` to the sandbox access list users.

**AI-generated demo data:** The spec says "AI-generated realistic data matching the company's industry." For MVP, use the existing `LocalDemoDataSeeder` (which already generates realistic data per domain). The "AI-generated" aspect can be a Phase 2 enhancement where `GenerateDemoDataService` calls OpenAI GPT-4o to generate company-specific fake data (employee names, deal names, product names matching the industry).

**Missing from data model:** `companies` table needs `is_sandbox boolean default false`, `parent_company_id ulid nullable FK`, and `sandbox_expires_at timestamp nullable` columns. These are foundational columns that must be in the initial `companies` migration even if the sandbox feature is built in a later phase — retrofitting them later risks data integrity issues.

## Related

- [[multi-tenancy-layer]]
- [[company-settings]]
- [[data-import]]
