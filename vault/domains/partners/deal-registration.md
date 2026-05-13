---
type: module
domain: Partner & Channel
panel: partners
module-key: partners.deal-registration
status: planned
color: "#4ADE80"
---

# Deal Registration

> Partners submit deals they are working; company reviews and approves or rejects with reason; approved deals receive 90-day protection; approved deals sync to CRM as opportunities.

**Panel:** `/partners`
**Module key:** `partners.deal-registration`

## What It Does

Deal Registration is the formal process by which partner organisations claim exclusive rights to pursue a specific prospect. A partner fills in a deal registration form (prospect company name, domain, estimated deal value, close date, notes) via the partner portal. The company reviews each submission in Filament and approves or rejects it, providing a reason in both cases. Approved deals are protected — no other partner or internal direct sales rep can claim the same prospect domain for the duration of the protection period (default 90 days, configurable per partner tier). When approved, a corresponding opportunity is created in the CRM domain so the internal sales team can support the partner pursuit. Deal status is tracked through sales stages aligned with the CRM pipeline, visible in both the partner portal and Filament admin.

## Features

### Core
- Partner-submitted deal registration form in portal: prospect company name, prospect domain (for de-duplication), estimated deal value and currency, target close date, product/service of interest, notes and background
- Company review in Filament: approve (with protection period confirmation) or reject (with mandatory reason text). Email notification sent to partner on status change.
- Protection enforcement: on approval, `protection_expires_at` is set to `approved_at + protection_days`. On any new deal registration submission for the same domain while protection is active, the system blocks the submission and notifies the submitting partner that the prospect is protected.
- Deal stage tracking: approved deals mirror CRM deal stages (Discovery, Proposal, Negotiation, Closed Won, Closed Lost). Stage updated by the company sales team in Filament or automatically when the linked CRM opportunity changes stage.
- Expiry: deals not progressed within the protection period automatically expire. Partner is notified 7 days before expiry with an option to request a 30-day extension.

### Advanced
- Duplicate check on submission: before allowing submission, portal checks `partner_deal_registrations` for any active record (status = approved, protection not expired) with the same normalised `prospect_domain`. Shows conflict warning to the partner.
- Extension requests: partners can request a 30-day extension from the portal before expiry. Company admin approves or rejects the extension request.
- Deal registration limit per partner: configurable maximum number of active registered deals per partner (configurable per tier). Prevents deal hoarding.
- Internal conflict check: on approval, system checks CRM contacts for an existing deal with the same prospect domain to alert the company's sales ops that a partner is now pursuing a prospect the internal team may already be working.
- Bulk review: Filament list view supports bulk approve and bulk reject with a shared reason template.
- Conversion tracking: when an approved deal closes (Closed Won in CRM), the linked `partner_deal_registrations` record is flagged as converted, and the commission calculation is triggered automatically.

### AI-Powered
- Prospect enrichment: on submission, AI enriches the prospect company name with company size, industry, and website via public data sources — filling fields to help the company reviewer assess deal quality
- Rejection reason suggestions: when a reviewer is about to reject a deal, Claude suggests a professionally worded rejection reason based on similar past rejections

## Data Model

```erDiagram
    partner_deal_registrations {
        ulid id PK
        ulid company_id FK
        ulid partner_id FK
        string prospect_name
        string prospect_domain
        decimal estimated_value
        string currency
        date close_date
        text notes
        string product_interest
        string status
        ulid reviewed_by FK
        timestamp reviewed_at
        string rejection_reason
        ulid crm_deal_id FK
        timestamp protection_expires_at
        integer protection_days
        boolean is_converted
        timestamps created_at/updated_at
    }

    partner_deal_extension_requests {
        ulid id PK
        ulid deal_registration_id FK
        integer days_requested
        string reason
        string status
        ulid reviewed_by FK
        timestamp reviewed_at
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `status` | pending / approved / rejected / expired / converted |
| `prospect_domain` | Normalised (lowercase, no `www.`, no trailing slash) — used for de-duplication checks |
| `crm_deal_id` | FK to `crm_deals` — set when the approved deal is synced to the CRM as an opportunity |
| `protection_expires_at` | Set on approval. Scheduled `ExpireProtectedDeals` command checks this daily. |
| `protection_days` | Snapshot of the tier-based protection period at time of approval (in case tier changes later) |
| `is_converted` | Set true when the linked CRM deal reaches "Closed Won" — triggers commission calculation |

## Permissions

```
partners.deal-registration.view
partners.deal-registration.create
partners.deal-registration.review
partners.deal-registration.delete
partners.deal-registration.export
```

## Filament

- **Resource:** `DealRegistrationResource` — list view with status filter (pending / approved / rejected / expired), partner filter, and date range filter. Pending registrations highlighted with amber badge. Bulk approve and bulk reject actions. Individual record: view all submitted details, link to partner, link to CRM deal (if synced).
- **Pages:** `ListDealRegistrations`, `ViewDealRegistration` (read-only detail view with approve/reject actions as Filament Actions), `EditDealRegistration` (limited — company can update CRM deal link and stage)
- **Custom pages:** None
- **Widgets:** `PendingDealRegistrationsWidget` on the Partners panel dashboard — count of deals awaiting review, avg days waiting. `DealConversionRateWidget` — approved deals vs converted deals ratio.
- **Nav group:** Deals (partners panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Salesforce PRM | Deal registration and protection |
| PartnerStack | Deal registration |
| Channelscaler | Partner deal management |
| Kiflo | Deal registration, approval workflow |
| Impartner | Deal registration and conflict management |

## Related

- [[partner-portal]]
- [[partner-commissions]]
- [[partner-onboarding]]
- [[domains/crm/deals]]
- [[domains/crm/pipeline]]

## Implementation Notes

- **CRM sync:** On deal registration approval, a `SyncDealToCrm` queued job creates a `crm_deal` record: name = "Partner Deal — {prospect_name}", value = estimated_value, stage = Discovery, owner = company's partner sales manager (configurable default), and sets a custom field `partner_deal_registration_id`. Updates `partner_deal_registrations.crm_deal_id` with the new deal ULID. CRM deal stage changes are synced back via `CrmDealObserver::updated()` which updates the registration's status to `converted` on "Closed Won".
- **Domain normalisation:** `ProspectDomainNormaliser` service strips `www.`, lowercases, and removes trailing slashes and paths. Input `https://www.Acme.com/about` → stored as `acme.com`. Used both on submission (normalise before store) and on de-duplication check (normalise input before query).
- **Protection expiry job:** `ExpireProtectedDeals` scheduled daily command (02:00 UTC) queries registrations where `status = approved AND protection_expires_at <= now()`. Updates status to `expired`. Dispatches `PartnerDealExpired` event which sends email to partner via queued `PartnerDealExpiredNotification`.
- **Extension request workflow:** Partner submits extension request via portal (creates `partner_deal_extension_requests`). Company admin reviews in Filament (approve → adds 30 days to `protection_expires_at`, reject → sends reason to partner). Filament notification badge on `DealRegistrationResource` shows pending extension requests.
- **Conflict with internal sales:** `InternalSalesConflictChecker` service is called on approval. Queries `crm_deals` for active deals where the linked `crm_company.domain` matches `prospect_domain`. If found, creates an internal Filament notification for the company's sales ops user: "Partner [name] has registered prospect [domain] which matches an existing CRM deal [deal name]."
