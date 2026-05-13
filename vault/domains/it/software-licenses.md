---
type: module
domain: IT & Security
panel: it
module-key: it.licenses
status: planned
color: "#4ADE80"
---

# Software Licenses

> Inventory every software licence and SaaS subscription, track seat allocations, monitor renewal dates, and identify unused licences to reduce waste.

**Panel:** `it`
**Module key:** `it.licenses`

## What It Does

Software Licenses gives IT and finance a single view of every software licence and SaaS subscription the company pays for. Each licence record tracks the vendor, product, cost, seat count purchased, seats allocated to users, and renewal date. Alerts fire before renewals to allow negotiation time. Utilisation analysis identifies licences where allocated seats are unused — presenting immediate cost-saving opportunities. Integration with [[asset-management]] links perpetual licence keys to the hardware assets they are installed on.

## Features

### Core
- Licence record: vendor, product name, licence type (perpetual, subscription, concurrent), seats purchased, cost, currency, billing cycle, renewal date, contract owner
- Seat allocation: assign licence seats to specific employees; track allocated vs available vs total seats
- Renewal alerts: configurable notification schedule before renewal date (90, 60, 30, 14 days)
- Cost tracking: annual and monthly cost per licence; total software spend dashboard
- Licence categories: productivity, security, development, communication, design, infrastructure
- Document storage: licence agreement, invoice, and contract attached to each licence record

### Advanced
- Utilisation tracking: for SaaS tools with API access, pull last-login data per seat to identify inactive users
- Under-utilisation alerts: flag licences where >20% of seats have not logged in within 90 days
- Renewal negotiation workflow: assign a renewal owner and track negotiation notes; record final negotiated cost and terms
- Cost per seat trend: track how cost per seat changes at each renewal cycle
- Shadow IT detection: flag SaaS subscriptions discovered via expense claims or credit card statement imports that are not yet in the licence register
- Licence compliance: for on-premise software, compare installed copies (from asset data) to licence entitlements; flag over-use

### AI-Powered
- Consolidation suggestion: identify overlapping tools serving the same function and estimate consolidation savings
- Renewal risk: predict whether a tool is likely to see a price increase at renewal based on vendor pricing history patterns

## Data Model

```erDiagram
    it_software_licences {
        ulid id PK
        ulid company_id FK
        string vendor
        string product_name
        string licence_type
        integer seats_purchased
        integer seats_allocated
        decimal annual_cost
        string currency
        string billing_cycle
        date renewal_date
        ulid contract_owner_id FK
        string status
        timestamps timestamps
    }

    it_licence_seats {
        ulid id PK
        ulid licence_id FK
        ulid employee_id FK
        date allocated_on
        date deallocated_on
        timestamp last_login_at
        boolean is_active
        timestamps timestamps
    }

    it_software_licences ||--o{ it_licence_seats : "allocates"
```

| Table | Purpose |
|---|---|
| `it_software_licences` | Licence master records with cost and renewal |
| `it_licence_seats` | Per-seat allocations with utilisation tracking |

## Permissions

```
it.licenses.view-any
it.licenses.create
it.licenses.update
it.licenses.allocate-seats
it.licenses.manage-renewals
```

## Filament

**Resource class:** `SoftwareLicenceResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `UtilisationReviewPage` (seats with no recent activity flagged for deallocation)
**Widgets:** `RenewalCalendarWidget` (upcoming renewals in 90 days), `TotalSoftwareSpendWidget`
**Nav group:** Assets

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Snow Software | Software asset management and licence optimisation |
| Flexera | Licence compliance and SaaS management |
| Torii | SaaS spend and utilisation management |
| Productiv | SaaS operations and utilisation analytics |

## Related

- [[asset-management]] — perpetual licences linked to hardware assets
- [[access-management]] — licence seat allocation linked to access records
- [[audit-compliance]] — licence compliance evidence for software audits
- [[../finance/INDEX]] — software costs reconciled against finance expenditure
