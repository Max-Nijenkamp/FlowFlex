---
type: module
domain: Real Estate & Property
panel: realestate
module-key: realestate.tenants
status: planned
color: "#4ADE80"
---

# Tenant & Occupancy Management

> Tenant contact management, occupancy period tracking, and communications log.

**Panel:** `realestate`
**Module key:** `realestate.tenants`

---

## What It Does

Tenant & Occupancy Management maintains the relationship record for every tenant in the portfolio. It stores the company and individual contact details of each tenant, tracks their occupancy period (which properties and units they currently or historically occupy), and maintains a chronological communications log of all interactions — emails sent, calls noted, service requests raised. This gives property managers a complete picture of each tenant relationship at a glance.

---

## Features

### Core
- Tenant company record: company name, registration number, sector, credit rating
- Tenant contacts: individual contacts with name, role, email, and phone
- Occupancy periods: link tenant to property/unit with occupancy start and end dates
- Communications log: chronological record of emails, calls, and meetings with date and summary
- Current leases view: all active leases associated with the tenant
- Tenant portal link: optional invitation for the tenant to view their lease summary and raise requests

### Advanced
- Tenant financial health: link to Companies House or credit data for financial health monitoring
- Rent payment history: summary of on-time vs late payment history across all leases
- Tenant satisfaction survey: send periodic satisfaction surveys and record responses
- Multiple properties: one tenant may occupy units across multiple buildings; all shown on one tenant record
- Tenant category: classify tenants (anchor, major, minor) for portfolio analysis

### AI-Powered
- Retention risk scoring: flag tenants approaching break or expiry dates who show signs of dissatisfaction
- Communication sentiment: analyse logged communications for negative sentiment trends
- Tenant health monitoring: alert when a tenant's credit score changes significantly

---

## Data Model

```erDiagram
    tenants {
        ulid id PK
        ulid company_id FK
        string tenant_name
        string registration_number
        string sector
        string credit_rating
        timestamps created_at_updated_at
    }

    tenant_contacts {
        ulid id PK
        ulid tenant_id FK
        string name
        string role
        string email
        string phone
        boolean is_primary
    }

    tenant_communications {
        ulid id PK
        ulid tenant_id FK
        ulid logged_by FK
        string type
        text summary
        timestamp occurred_at
    }

    tenants ||--o{ tenant_contacts : "has"
    tenants ||--o{ tenant_communications : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `tenants` | Tenant company records | `id`, `company_id`, `tenant_name`, `registration_number`, `credit_rating` |
| `tenant_contacts` | Contact persons | `id`, `tenant_id`, `name`, `role`, `email`, `is_primary` |
| `tenant_communications` | Communications log | `id`, `tenant_id`, `type`, `summary`, `occurred_at` |

---

## Permissions

```
realestate.tenants.view
realestate.tenants.create
realestate.tenants.update
realestate.tenants.delete
realestate.tenants.view-financial
```

---

## Filament

- **Resource:** `App\Filament\Realestate\Resources\TenantResource`
- **Pages:** `ListTenants`, `CreateTenant`, `EditTenant`, `ViewTenant`
- **Custom pages:** `TenantCommunicationsPage`, `TenantPortalPage`
- **Widgets:** `ActiveTenantsWidget`, `UpcomingBreaksWidget`
- **Nav group:** Tenants

---

## Displaces

| Feature | FlowFlex | Yardi | Re-Leased | MRI |
|---|---|---|---|---|
| Tenant CRM records | Yes | Yes | Yes | Yes |
| Communications log | Yes | Yes | Partial | Yes |
| Retention risk scoring | Yes | No | No | No |
| Satisfaction surveys | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[lease-management]] — tenants are linked to leases
- [[rental-billing-arrears]] — payment history tracked per tenant
- [[property-maintenance]] — tenants can raise maintenance requests
