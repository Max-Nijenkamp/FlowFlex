---
type: module
domain: Core Platform
panel: app
module-key: core.marketplace
status: planned
color: "#4ADE80"
---

# Module Marketplace

> Module catalog browser — explore all available FlowFlex domain modules, read descriptions and pricing, and activate or deactivate modules per company.

**Panel:** `app`
**Module key:** `core.marketplace`

## What It Does

The Module Marketplace is the in-app catalog where a company owner browses all available FlowFlex domain modules, reads what each one does, sees the per-seat pricing, and activates or deactivates modules for their company. Activation calls `BillingService` to update the `company_module_subscriptions` table and create the corresponding Stripe subscription item. Once activated, the module's Filament navigation group appears immediately. Deactivation hides the navigation and gates access without deleting any data — reactivation restores everything exactly as it was.

## Features

### Core
- Grid view of all available domain modules with name, short description, category, and price per seat per month
- Filter by category (HR, Finance, CRM, Projects, etc.) and search by name
- Module detail drawer: full description, feature list, screenshots, competitor displacement table, and pricing breakdown
- Activate button: triggers `BillingService::activateModule()` → updates `company_module_subscriptions` → Stripe subscription item created
- Deactivate button: sets `is_active = false` on subscription row → navigation hidden → `EnforceModuleAccess` blocks access → Stripe subscription item removed

### Advanced
- Bundles: pre-configured module groupings (e.g. "HR Complete" = employee profiles + leave + payroll + analytics) at a discounted bundle price
- Free trial per module: 14-day free trial on first activation of any paid module
- Active module dashboard: separate tab showing currently active modules, seat counts, and monthly cost breakdown
- Admin override: FlowFlex staff can force-activate or deactivate any module from the admin panel regardless of billing status
- Data retention on deactivation: all data created by the module is retained for 90 days after deactivation; permanent deletion requires explicit owner action

### AI-Powered
- Recommended modules: based on company industry, size, and current usage patterns, the marketplace highlights the three most likely useful next modules
- ROI estimates: AI-generated ROI summary for each module based on company size (e.g. "For a 50-person company, Leave Management typically saves 4 hours of admin per week")

## Data Model

```erDiagram
    module_catalog {
        ulid id PK
        string module_key "unique"
        string name
        string domain
        string category
        text description
        decimal price_per_seat
        boolean is_available
        json feature_list
        timestamps created_at/updated_at
    }

    company_module_subscriptions {
        ulid id PK
        ulid company_id FK
        string module_key
        boolean is_active
        decimal price_per_seat
        integer seat_count
        timestamp activated_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `module_catalog.module_key` | Matches the module-key in each module spec (e.g. `hr.leave`) |
| `is_available` | Admin-controlled — can mark a module as coming soon |
| `company_module_subscriptions.is_active` | Toggled by activate/deactivate actions |

## Permissions

- `core.marketplace.view`
- `core.marketplace.activate-module`
- `core.marketplace.deactivate-module`
- `core.marketplace.manage-seats`
- `core.marketplace.admin-override`

## Filament

- **Resource:** None
- **Pages:** `ModuleMarketplacePage` — catalog grid with filter, search, and detail drawer
- **Custom pages:** `ModuleMarketplacePage`
- **Widgets:** `ActiveModulesWidget` — compact list of active modules on dashboard
- **Nav group:** Modules (app panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Salesforce AppExchange | In-app module/app marketplace |
| Microsoft AppSource | SaaS add-on catalog |
| HubSpot App Marketplace | CRM extension catalog |
| Shopify App Store | E-commerce module marketplace |

## Related

- [[billing-engine]]
- [[setup-wizard]]
- [[company-settings]]
