---
type: module
domain: IT & Security
panel: it
module-key: it.access
status: planned
color: "#4ADE80"
---

# Access Management

> Manage user access requests, provisioning approvals, periodic access reviews, and deprovisioning to enforce least-privilege access across the company.

**Panel:** `it`
**Module key:** `it.access`

## What It Does

Access Management governs who has access to what systems across the company. Employees request access via a self-service catalogue. Requests route through a defined approval chain (line manager plus system owner). Approved access is provisioned and recorded. Periodic access reviews prompt system owners to certify that each user's access is still appropriate — flagging unnecessary entitlements for removal. When an employee leaves, offboarding triggers automatic deprovisioning tasks for all their active access records.

## Features

### Core
- Access catalogue: list of all systems/applications with access tiers and request form per system
- Access request: employee selects system and access level, provides business justification
- Approval workflow: configurable approver chain (line manager → system owner → IT admin) per system
- Access record: granted access records per employee per system with start date and business justification
- Provisioning task: approved request creates a task for the IT team to provision the access in the actual system
- Revocation: manually revoke access at any time; create deprovisioning task

### Advanced
- Periodic access review: campaign that prompts system owners to review all access records for their system on a schedule (quarterly, annually)
- Review outcomes: certify (access confirmed), revoke (access to be removed), or transfer (access moved to different access level)
- Automated deprovisioning triggers: employee offboarding in [[../hr/INDEX]] triggers a review of all access records for that employee
- Segregation of duties (SoD): flag when the same employee holds two conflicting access roles (e.g., can both create and approve payments)
- Access analytics: who has access to the most sensitive systems; systems with most access requests per month
- Privileged access tracking: separate record for admin-level and privileged accounts with enhanced review frequency

### AI-Powered
- Access recommendation: suggest the standard access package for a new employee based on their role and department
- Risk flag: highlight access records that have not been reviewed in over 12 months or where the user has not logged in

## Data Model

```erDiagram
    it_access_systems {
        ulid id PK
        ulid company_id FK
        string name
        string category
        ulid system_owner_id FK
        json access_tiers
        boolean requires_mfa
        boolean is_privileged
        timestamps timestamps
    }

    it_access_requests {
        ulid id PK
        ulid company_id FK
        ulid system_id FK
        ulid requester_id FK
        string access_tier
        text business_justification
        string status
        ulid approved_by FK
        timestamp approved_at
        timestamps timestamps
    }

    it_access_records {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        ulid system_id FK
        string access_tier
        date granted_on
        date revoked_on
        string status
        timestamps timestamps
    }

    it_access_review_items {
        ulid id PK
        ulid review_campaign_id FK
        ulid access_record_id FK
        string decision
        ulid decided_by FK
        timestamp decided_at
    }

    it_access_systems ||--o{ it_access_requests : "receives"
    it_access_systems ||--o{ it_access_records : "grants"
    it_access_records ||--o{ it_access_review_items : "reviewed in"
```

| Table | Purpose |
|---|---|
| `it_access_systems` | System catalogue with owner and access tiers |
| `it_access_requests` | Access requests with approval workflow |
| `it_access_records` | Active and historical access grants |
| `it_access_review_items` | Per-record review decisions in a campaign |

## Permissions

```
it.access.view-any
it.access.request
it.access.approve
it.access.provision
it.access.manage-reviews
```

## Filament

**Resource class:** `AccessSystemResource`, `AccessRequestResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `AccessCataloguePage` (employee self-service portal), `ReviewCampaignPage` (system owner review interface)
**Widgets:** `PendingAccessRequestsWidget`, `OverdueReviewItemsWidget`
**Nav group:** Access

## Displaces

| Competitor | Feature Replaced |
|---|---|
| Okta Lifecycle Management | Access provisioning and deprovisioning |
| SailPoint (SMB) | Access reviews and certifications |
| BetterCloud | SaaS access management |
| Freshservice Access Management | Request and provisioning workflow |

## Related

- [[service-desk]] — access requests submitted via service desk portal
- [[audit-compliance]] — access records and review evidence for IT audits
- [[../hr/INDEX]] — employee offboarding triggers deprovisioning
- [[it-analytics]] — access review completion rates tracked as IT KPIs
