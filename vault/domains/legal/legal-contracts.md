---
type: module
domain: Legal & Compliance
domain-key: legal
panel: legal
module-key: legal.contracts
status: planned
priority: p3
depends-on: [core.billing, core.rbac, core.files, core.notifications]
soft-depends: [crm.contacts, operations.suppliers, legal.matters]
fires-events: []
consumes-events: []
patterns: [states, money]
tables: [legal_contracts, legal_contract_obligations]
permission-prefix: legal.contracts
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Legal Contracts

Central contract repository with key dates, renewal tracking, obligations, and signature status. Different from [[domains/crm/contracts|crm.contracts]] (sales-focused): full legal lifecycle, all contract types. The Legal anchor — build first in `/legal`.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/file-storage\|core.files]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, signed PDFs, renewal alerts |
| Soft | [[domains/crm/contacts\|crm.contacts]], [[domains/operations/suppliers\|operations.suppliers]] | counterparty links |
| Soft | [[domains/legal/matter-management\|legal.matters]] | contract ↔ matter link |

---

## Core Features

- Contract record: title, counterparty, type, value, start/end dates, renewal terms, status
- Contract types: NDA, MSA, vendor, employment, lease, partnership
- Status machine: `draft → in_review → signed → active → expired | terminated`
- Key date tracking: renewal date, notice period deadline, expiry
- Renewal alerts: notify before notice deadline (90/30d, once each)
- Obligations tracking: deliverables, payment milestones with due dates + overdue alerts
- Document storage (signed PDF via Media Library)
- E-signature: manual signed-PDF upload v1 (same convention as crm.contracts *(assumed)*)
- Linked to CRM account or supplier

---

## Data Model

### legal_contracts

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| title | string | |
| counterparty | string | + crm_account_id / ops_supplier_id nullable links |
| type | string | in set |
| value_cents | bigint nullable | |
| currency | string(3) | |
| start_date / end_date | date | end after start |
| renewal_date | date nullable | |
| notice_period_days | int default 30 | |
| status | string default `draft` | state machine |
| owner_id | ulid FK users | |
| matter_id | ulid nullable | |
| alerted_levels | jsonb default `[]` | 90/30 once-guards |
| signed_at | timestamp nullable | |
| deleted_at | timestamp nullable | |

**Indexes:** `(company_id, status, renewal_date)`, `(company_id, type)`

### legal_contract_obligations — id, contract_id FK, company_id, description, due_date, status (open/done/overdue), responsible_id FK users, alerted boolean

---

## State Machine

| State | Transitions to | Triggered by | Side effects |
|---|---|---|---|
| `draft` | `in_review` | `legal.contracts.update` | |
| `in_review` | `signed` | signed-PDF upload + `legal.contracts.sign-off` | `signed_at` |
| `signed` | `active` | start date (scheduled) or manual | |
| `active` | `expired` | end date passed, no renewal (scheduled) | |
| `active` | `terminated` | `legal.contracts.terminate` | reason required |
| `active` | renewed (stays active) | renew action | new dates, alert guards reset, audited |

---

## DTOs

### CreateLegalContractData — title, counterparty (or link id), type (in set), value_cents?, start/end (end after start), renewal_date?, notice_period_days
### AddObligationData — contract_id, description, due_date, responsible_id

## Services & Actions

- `LegalContractService::markSigned/renew/terminate`
- `ContractLifecycleCommand` — activation/expiry + notice-deadline alerts (notice deadline = renewal_date − notice_period_days) + obligation overdue alerts; all once-guarded

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `LegalContractLifecycleCommand` | notifications | daily 05:45 | alerted_levels / alerted guards |

---

## Filament

**Nav group:** Contracts

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `LegalContractResource` | #1 CRUD resource | filters type/status/renewal; obligations relation; sign/renew/terminate actions |
| `ContractRenewalWidget` | #6 widget | notice deadlines + renewals |

---

## Permissions

`legal.contracts.view-any` · `legal.contracts.create` · `legal.contracts.update` · `legal.contracts.sign-off` · `legal.contracts.terminate`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Lifecycle command: activate/expire/alerts once per level
- [ ] Notice deadline math (renewal − notice days)
- [ ] Sign requires PDF; terminate requires reason
- [ ] Obligation overdue alert once
- [ ] Renewal resets alert guards

---

## Build Manifest

```
database/migrations/xxxx_create_legal_contracts_table.php
database/migrations/xxxx_create_legal_contract_obligations_table.php
app/Models/Legal/{LegalContract,ContractObligation}.php
app/States/Legal/LegalContract/{LegalContractState,Draft,InReview,Signed,Active,Expired,Terminated}.php
app/Data/Legal/{CreateLegalContractData,AddObligationData}.php
app/Services/Legal/LegalContractService.php
app/Providers/Legal/LegalServiceProvider.php
app/Console/Commands/Legal/LegalContractLifecycleCommand.php
app/Filament/Legal/Resources/LegalContractResource.php
app/Filament/Legal/Widgets/ContractRenewalWidget.php
database/factories/Legal/{LegalContractFactory,ContractObligationFactory}.php
tests/Feature/Legal/{LegalContractTest,ContractLifecycleTest}.php
```

---

## Related

- [[domains/legal/matter-management]]
- [[domains/crm/contracts]]
- [[domains/dms/document-library]]
