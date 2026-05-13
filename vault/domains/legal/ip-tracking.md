---
type: module
domain: Legal & Compliance
panel: legal
module-key: legal.ip
status: planned
color: "#4ADE80"
---

# IP Tracking

> Intellectual property register covering patents, trademarks, copyrights, and domain names with renewal date tracking and ownership documentation.

**Panel:** `legal`
**Module key:** `legal.ip`

## What It Does

IP Tracking maintains the company's intellectual property portfolio in a structured register. Each IP asset — patent, trademark, copyright, domain name, trade secret classification — is recorded with its registration number, jurisdiction, registration date, expiry date, and the responsible attorney or agent. Renewal deadlines are tracked and alerted before they are missed, since missed renewals can result in permanent loss of IP rights. The register provides a clear picture of what the company owns and where it is protected.

## Features

### Core
- IP types: patent (granted, pending, provisional), trademark (registered, pending, common law), copyright (registered, unregistered), domain name, trade secret, design right
- IP record: title, type, registration number, jurisdiction(s), filing date, registration date, expiry date, registered owner, responsible attorney/agent
- Status: pending, active, lapsed, abandoned, licensed out, licensed in
- Renewal tracking: configurable alerts before expiry (12, 6, 3, 1 month before)
- Document storage: certificates, filing confirmations, and correspondence attached to each IP record
- Jurisdiction management: track the same IP registered across multiple jurisdictions (e.g., EU trademark + US trademark) as related records

### Advanced
- Maintenance fee tracking: for patents, track annuity payment due dates per jurisdiction with alert before deadline
- Licensing records: record licence agreements granted or received on each IP asset; link to contract in [[contracts]]
- IP portfolio value: assign an estimated value to each IP asset for balance sheet reporting
- Portfolio view: filter IP register by type, jurisdiction, status, or responsible party
- Freedom-to-operate links: note related third-party IP that may constrain use; flag for legal review
- Trademark watch alerts: record trademark classes monitored for conflicting registrations (manual entry of watch service findings)

### AI-Powered
- Renewal risk scoring: flag IP assets where renewal is approaching and no renewal instruction has been recorded
- Class coverage check: for trademarks, prompt if key business activities may not be covered by registered classes

## Data Model

```erDiagram
    legal_ip_assets {
        ulid id PK
        ulid company_id FK
        string title
        string ip_type
        string registration_number
        string status
        json jurisdictions
        date filing_date
        date registration_date
        date expiry_date
        ulid owner_id FK
        string responsible_attorney
        decimal estimated_value
        timestamps timestamps
        softDeletes deleted_at
    }

    legal_ip_renewals {
        ulid id PK
        ulid ip_asset_id FK
        date due_date
        string jurisdiction
        decimal fee_amount
        string currency
        string status
        timestamp paid_at
    }

    legal_ip_licences {
        ulid id PK
        ulid ip_asset_id FK
        ulid contract_id FK
        string licence_type
        string licensee
        date licence_start
        date licence_end
        decimal royalty_rate
    }

    legal_ip_assets ||--o{ legal_ip_renewals : "requires"
    legal_ip_assets ||--o{ legal_ip_licences : "licensed via"
```

| Table | Purpose |
|---|---|
| `legal_ip_assets` | IP portfolio register |
| `legal_ip_renewals` | Renewal and maintenance fee schedule |
| `legal_ip_licences` | Licensing arrangements per IP asset |

## Permissions

```
legal.ip.view-any
legal.ip.create
legal.ip.update
legal.ip.manage-renewals
legal.ip.delete
```

## Filament

**Resource class:** `IpAssetResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `IpRenewalCalendarPage` (upcoming renewal deadlines across all IP assets)
**Widgets:** `IpRenewalAlertWidget` (renewals due in 90 days)
**Nav group:** Compliance

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Dennemeyer IP Management | Patent and trademark portfolio management |
| CPA Global (Clarivate) | IP renewal management |
| Anaqua | IP portfolio and docketing |
| Pattsy Wave | Trademark and patent docketing |

## Related

- [[contracts]] — IP licence agreements linked to contract records
- [[matter-management]] — IP disputes tracked as legal matters
- [[compliance-calendar]] — IP renewal deadlines captured as compliance obligations
