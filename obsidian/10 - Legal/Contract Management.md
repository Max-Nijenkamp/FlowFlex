---
tags: [flowflex, domain/legal, contracts, clm, e-signature, phase/7]
domain: Legal
panel: legal
color: "#DC2626"
status: planned
last_updated: 2026-05-07
---

# Contract Management (CLM)

Full contract lifecycle from template to signature to renewal. Never miss a renewal again — auto-renewal alerts create CRM tasks before the expiry date arrives.

**Who uses it:** Legal team, sales, procurement, HR
**Filament Panel:** `legal`
**Depends on:** [[Document Approvals & E-Sign]], [[CRM — Contact & Company Management]], [[File Storage]]
**Phase:** 7
**Build complexity:** High — 4 resources, 2 pages, 4 tables

---

## Features

- **Full contract lifecycle** — draft → review → approved → signed → active → expired/terminated; status transitions tracked with timestamps
- **Contract types** — customer, supplier, employment, NDA, partnership, other; type drives default template selection
- **Template library** — reusable contract templates with variable placeholders; merge company and counterparty details automatically
- **Version history** — every uploaded revision stored in `contract_versions` with version number and upload notes; download any prior version
- **Multi-party contracts** — `contract_parties` table supports multiple CRM contacts per contract with role: signatory, reviewer, cc
- **E-signature** — built-in e-signature powered by [[Document Approvals & E-Sign]]; `ContractSigned` event fires on final signature
- **Auto-renewal management** — `auto_renewal` flag; `renewal_notice_days` determines how far ahead to alert; `ContractExpiring` event fires N days before `end_date`
- **Contract value tracking** — record contract value and currency; aggregate in dashboard by type and status
- **Contract reminders** — configurable reminder rules per contract: X days before renewal or end date; `is_sent` flag prevents duplicates
- **Full-text search** — search across contract title, counterparty, and notes; filter by type, status, and date range
- **Linked to CRM** — attach a contract to a CRM contact or deal; contract appears in the deal's related records
- **Activity log** — all status changes and uploads logged via `LogsActivity`; immutable audit trail for legal purposes

---

## Database Tables

> All tables include standard columns: `id` (ULID PK), `company_id` (ULID FK → companies), `deleted_at` (soft deletes), `created_at`, `updated_at`.

### `contracts`
| Column | Type | Notes |
|---|---|---|
| `title` | string | |
| `type` | enum | `customer`, `supplier`, `employment`, `nda`, `partnership`, `other` |
| `status` | enum | `draft`, `review`, `approved`, `signed`, `active`, `expired`, `terminated` |
| `counterparty` | string nullable | name if not a CRM contact |
| `crm_contact_id` | ulid FK nullable | → crm_contacts |
| `crm_company_id` | ulid FK nullable | → crm_companies |
| `start_date` | date nullable | |
| `end_date` | date nullable | |
| `auto_renewal` | boolean default false | |
| `renewal_notice_days` | integer nullable | |
| `value` | decimal(12,2) nullable | |
| `currency` | string(3) default 'GBP' | |
| `file_id` | ulid FK nullable | → files (current version) |
| `owner_id` | ulid FK nullable | → tenants |

### `contract_parties`
| Column | Type | Notes |
|---|---|---|
| `contract_id` | ulid FK | → contracts |
| `crm_contact_id` | ulid FK nullable | → crm_contacts |
| `name` | string nullable | if not a CRM contact |
| `email` | string nullable | |
| `role` | enum | `signatory`, `cc`, `reviewer` |
| `signed_at` | timestamp nullable | |

### `contract_versions`
| Column | Type | Notes |
|---|---|---|
| `contract_id` | ulid FK | → contracts |
| `version_number` | integer | |
| `file_id` | ulid FK | → files |
| `uploaded_at` | timestamp | |
| `uploaded_by` | ulid FK | → tenants |
| `notes` | string nullable | |

### `contract_reminders`
| Column | Type | Notes |
|---|---|---|
| `contract_id` | ulid FK | → contracts |
| `reminder_type` | enum | `expiry`, `renewal`, `review` |
| `reminder_date` | date | |
| `days_before` | integer | |
| `is_sent` | boolean default false | |
| `sent_at` | timestamp nullable | |

---

## Events Fired

| Event | Payload | Consumed By |
|---|---|---|
| `ContractExpiring` | `contract_id`, `days_until_expiry` | CRM renewal task creation; notification to contract owner |
| `ContractSigned` | `contract_id` | Updates status to `active`; notifies legal team |

---

## Events Consumed

None — contract events are triggered by status changes or scheduled checks.

---

## Permissions

```
legal.contracts.view
legal.contracts.create
legal.contracts.edit
legal.contracts.delete
legal.contracts.sign
legal.contract-parties.view
legal.contract-parties.create
legal.contract-versions.view
legal.contract-versions.upload
legal.contract-reminders.view
legal.contract-reminders.create
```

---

## Related

- [[Legal Overview]]
- [[Document Approvals & E-Sign]]
- [[Sales Pipeline]]
- [[CRM — Contact & Company Management]]
