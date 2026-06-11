---
type: module
domain: CRM & Sales
domain-key: crm
panel: crm
module-key: crm.contracts
status: planned
priority: v1
depends-on: [crm.deals, core.billing, core.rbac, core.files, core.notifications]
soft-depends: [crm.quotes, legal.contracts]
fires-events: []
consumes-events: []
patterns: [states, money, pdf]
tables: [crm_contracts]
permission-prefix: crm.contracts
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Contracts

Customer contracts with renewal tracking, value, and signature status. Sales-focused contract lifecycle (distinct from Legal's full legal contract management).

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/crm/deals\|crm.deals]] | contracts generated from won deals |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, signed PDF storage, renewal alerts |
| Soft | [[domains/crm/quotes\|crm.quotes]] | accepted quote pre-fills contract |
| Soft | [[domains/legal/legal-contracts\|legal.legal-contracts]] | deep legal lifecycle (P3) |

---

## Core Features

- Contract record: customer, deal, title, value, start/end dates, renewal terms, status
- Status machine: `draft → sent → signed → active → expired | terminated`
- Generated from a won deal or accepted quote
- E-signature: **v1 = manual signed-PDF upload + signed flag; DocuSign/native e-sign = later ADR** *(assumed)*
- Renewal tracking: renewal date, auto-renew flag, notice period
- Renewal alerts before expiry (90/30 days *(assumed)*)
- Contract value → recurring revenue tracking
- Document storage (signed PDF via Media Library)
- Linked to CRM account

---

## Data Model

### crm_contracts

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id, company_id (indexed) | ulid | | |
| account_id | ulid | not null FK | |
| deal_id | ulid | nullable FK | |
| title | string | not null | |
| value_cents | bigint | ≥ 0 | |
| currency | string(3) | | |
| billing_interval | string | one-off / monthly / yearly *(assumed)* | recurring revenue calc |
| start_date / end_date | date | end after start | |
| renewal_date | date | nullable | |
| auto_renew | boolean | default false | |
| notice_period_days | int | default 30 | |
| status | string | default `draft` | state machine |
| signed_at | timestamp | nullable | |
| alerted_levels | jsonb | default `[]` | 90/30-day once-guards |
| deleted_at | timestamp | nullable | |

**Indexes:** `(company_id, status, renewal_date)`

---

## State Machine

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `draft` | `sent` | `crm.contracts.send` | |
| `sent` | `signed` | signed-PDF upload + `crm.contracts.sign-off` | `signed_at` |
| `signed` | `active` | start_date reached (scheduled) or manual | |
| `active` | `expired` | end_date passed, no renewal (scheduled) | |
| `active` | `terminated` | `crm.contracts.terminate` | reason required |
| `active` | `active` (renewed) | auto_renew or manual renew | new end/renewal dates, audited |

---

## DTOs

### CreateContractData — account_id (required), deal_id?, title, value_cents, billing_interval, start_date/end_date (end after start), auto_renew, notice_period_days
### TerminateContractData — contract_id, reason (required)

## Services & Actions

- `ContractService::createFromDeal(string $dealId): ContractData` — prefills from deal/quote
- `ContractService::markSigned(string $contractId, UploadedFile $signedPdf): ContractData`
- `ContractService::renew(string $contractId, CarbonImmutable $newEnd): ContractData`
- `ContractService::terminate(TerminateContractData $data): ContractData`
- `ContractService::recurringRevenue(): Money` — sum of active recurring contracts normalised monthly

---

## Filament

**Nav group:** Pipeline

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `ContractResource` | #1 CRUD resource | create-from-deal action, signed-PDF upload, renew/terminate actions |
| `ContractRenewalWidget` | #6 widget | renewals next 90 days |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('crm.contracts.view-any') && BillingService::hasModule('crm.contracts')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

**Security notes** (per [[build/security-audit-2026-06-11]]):

- **Upload contract** (medium): In the contract record / markSigned section note: accept application/pdf only, max size cap, stored under companies/{company_id}/contracts/ via Media Library.

---

## Permissions

`crm.contracts.view-any` · `crm.contracts.view` · `crm.contracts.create` · `crm.contracts.send` · `crm.contracts.sign-off` · `crm.contracts.terminate`

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ContractLifecycleCommand` | default | daily 05:30 | signed→active at start; active→expired past end; auto-renew handled; `alerted_levels` once-guards for 90/30-day alerts |

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Create-from-deal prefills account/value
- [ ] Sign requires PDF upload; sets signed_at
- [ ] Lifecycle command: activation, expiry, auto-renew, alert once per level
- [ ] Terminate requires reason; audited
- [ ] Recurring revenue normalises yearly→monthly correctly (brick/money)

---

## Build Manifest

```
database/migrations/xxxx_create_crm_contracts_table.php
app/Models/CRM/Contract.php
app/States/CRM/Contract/{ContractState,Draft,Sent,Signed,Active,Expired,Terminated}.php
app/Data/CRM/{CreateContractData,TerminateContractData,ContractData}.php
app/Services/CRM/ContractService.php
app/Console/Commands/CRM/ContractLifecycleCommand.php
app/Filament/CRM/Resources/ContractResource.php
app/Filament/CRM/Widgets/ContractRenewalWidget.php
database/factories/CRM/ContractFactory.php
tests/Feature/CRM/{ContractLifecycleTest,ContractRenewalTest}.php
```

---

## Related

- [[domains/crm/deals]]
- [[domains/crm/quotes]]
- [[domains/legal/legal-contracts]]
