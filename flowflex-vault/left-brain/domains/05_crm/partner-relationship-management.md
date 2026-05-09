---
type: module
domain: CRM & Sales
panel: crm
cssclasses: domain-crm
phase: 8
status: planned
migration_range: 250000–299999
last_updated: 2026-05-09
---

# Partner Relationship Management (PRM)

Reseller and channel partner portal — deal registration, MDF (market development funds), co-selling tools, and partner performance tracking. Replaces Impartner and Allbound.

---

## Features

### Partner Portal (public Vue+Inertia)
- Partner login (separate `auth:partner` guard)
- Deal registration form
- Partner-specific price book access
- Co-marketing materials download
- Training & certification progress
- Commission statements
- Support ticket submission

### Partner Types & Tiers
- Partner type builder (Reseller, Referral, Technology, OEM)
- Tier levels (Silver, Gold, Platinum) with criteria and benefits
- Automated tier promotion based on deal volume
- Tier-based discount levels

### Deal Registration & Co-Selling
- Partner submits deal via portal → internal review
- Conflict detection (partner A registered same customer as partner B)
- Internal owner assigned to co-sell support
- Deal stage tracking (partner-visible subset)
- Win/loss attribution per partner

### MDF (Market Development Funds)
- MDF budget per partner per quarter
- Fund request → approval → claim → reimbursement
- Campaign co-branding assets
- MDF utilisation reporting

### Performance & Enablement
- Partner scorecard (deals registered, closed, MDF used)
- Training completion tracking
- Certification requirements per tier
- Partner-specific content library

---

## Data Model

```erDiagram
    partners {
        ulid id PK
        ulid company_id FK
        string name
        string type
        string tier
        string status
        string portal_subdomain
    }

    partner_deals {
        ulid id PK
        ulid partner_id FK
        ulid crm_deal_id FK
        string status
        decimal deal_value
        timestamp registered_at
        timestamp closed_at
    }

    mdf_requests {
        ulid id PK
        ulid partner_id FK
        string campaign_name
        decimal amount_requested
        decimal amount_approved
        string status
        timestamp submitted_at
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `PartnerDealRegistered` | Partner registers deal | CRM (create deal, assign owner), Notifications |
| `PartnerDealClosed` | Deal marked won | Finance (calculate partner commission) |
| `MDFRequestApproved` | MDF approved | Finance (reserve budget), Notifications (partner) |

---

## Permissions

```
crm.partners.view-any
crm.partners.manage
crm.partner-deals.approve
crm.mdf.approve
```

---

## Competitors Displaced

Impartner · Allbound · Channeltivity · Mindmatrix · Salesforce PRM

---

## Related

- [[MOC_CRM]]
- [[entity-contact]]
