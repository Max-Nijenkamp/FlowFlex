---
type: module
domain: Real Estate & Property
panel: realestate
module-key: realestate.leases
status: planned
color: "#4ADE80"
---

# Lease Management

> Lease records — tenant, term, rent review schedule, break options, deposit, renewals, and critical date tracking.

**Panel:** `realestate`
**Module key:** `realestate.leases`

---

## What It Does

Lease Management maintains the full lifecycle record of every lease in the portfolio. From initial agreement through rent reviews, break options, and renewal negotiations to expiry or surrender, every critical date and financial term is captured. Automated alerts fire well in advance of break clauses, rent review dates, and lease expiries so asset managers never miss a deadline. The lease record is the source of truth for rental billing, IFRS 16 accounting, and tenant management.

---

## Features

### Core
- Lease creation: property/unit, tenant, start date, end date, lease type (FRI, IRI, licence)
- Rent terms: headline rent, rent-free period, stepped rent schedule
- Rent review: review dates, review type (open market, RPI-linked, fixed uplift), review outcome recording
- Break options: break dates, break conditions (rolling/fixed), notice period requirements
- Deposit: deposit amount, form (cash, bank guarantee), held-by, return conditions
- Critical date calendar: all key dates across the portfolio in a single calendar view

### Advanced
- Lease document storage: attach the executed lease, side letters, licences to alter
- Dilapidations schedule: record dilapidations obligations and end-of-lease settlement
- Renewal workflow: track lease renewal negotiations with stage, proposed terms, and decision
- Surrender: record early surrender with compensation terms and effective date
- Lease abstraction: paste lease clauses and key term summary for quick reference

### AI-Powered
- Critical date alert: proactive alerts 12/6/3 months before break, review, or expiry dates
- Rent review outcome estimation: compare proposed review rent against comparable market data
- Lease risk scoring: flag leases with approaching breaks, no-break options, and low market demand as portfolio risks

---

## Data Model

```erDiagram
    leases {
        ulid id PK
        ulid property_id FK
        ulid unit_id FK
        ulid tenant_id FK
        ulid company_id FK
        string lease_type
        date start_date
        date end_date
        decimal headline_rent
        decimal rent_free_months
        json rent_steps
        json break_options
        decimal deposit_amount
        string deposit_form
        string status
        timestamps created_at_updated_at
    }

    lease_rent_reviews {
        ulid id PK
        ulid lease_id FK
        date review_date
        string review_type
        decimal agreed_rent
        date agreed_at
        text notes
    }

    leases ||--o{ lease_rent_reviews : "has"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `leases` | Lease records | `id`, `property_id`, `unit_id`, `tenant_id`, `lease_type`, `start_date`, `end_date`, `headline_rent`, `status` |
| `lease_rent_reviews` | Rent review outcomes | `id`, `lease_id`, `review_date`, `review_type`, `agreed_rent` |

---

## Permissions

```
realestate.leases.view
realestate.leases.create
realestate.leases.update
realestate.leases.delete
realestate.leases.view-financial
```

---

## Filament

- **Resource:** `App\Filament\Realestate\Resources\LeaseResource`
- **Pages:** `ListLeases`, `CreateLease`, `EditLease`, `ViewLease`
- **Custom pages:** `CriticalDateCalendarPage`, `LeaseExpiryPage`
- **Widgets:** `LeasesExpiringSoonWidget`, `ReviewsDueWidget`
- **Nav group:** Leases

---

## Displaces

| Feature | FlowFlex | Yardi | MRI | Re-Leased |
|---|---|---|---|---|
| Full lease terms | Yes | Yes | Yes | Yes |
| Break and review tracking | Yes | Yes | Yes | Yes |
| Critical date alerts | Yes | Yes | Yes | Yes |
| AI rent review estimation | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[property-register]] — leases reference property and unit records
- [[tenant-occupancy-management]] — tenant linked from lease
- [[rental-billing-arrears]] — rent billing derived from lease terms
- [[ifrs-16-lease-accounting]] — lease data feeds IFRS 16 calculations
