---
domain: crm
module: contracts
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Contracts — Architecture

## State Machine

Implemented with spatie/laravel-model-states. See [[../../../architecture/patterns/states]].

| From | To | Trigger | Notes |
|---|---|---|---|
| `draft` | `sent` | `crm.contracts.send` | Contract dispatched to customer. |
| `sent` | `signed` | signed-PDF upload + `crm.contracts.sign-off` | Sets `signed_at`. |
| `signed` | `active` | `start_date` reached (scheduled) or manual | Activation. |
| `active` | `expired` | `end_date` passed, no renewal (scheduled) | Terminal for the cycle. |
| `active` | `terminated` | `crm.contracts.terminate` | Reason required. |
| `active` | `active` (renewed) | auto_renew or manual renew | New end/renewal dates, audited. |

## Services & Actions

| Method | Signature | Purpose |
|---|---|---|
| `ContractService::createFromDeal` | `(dealId): ContractData` | Prefills from deal / accepted quote. |
| `ContractService::markSigned` | `(contractId, signedPdf): ContractData` | Attaches signed PDF, sets `signed_at`. |
| `ContractService::renew` | `(contractId, newEnd): ContractData` | New end / renewal dates. |
| `ContractService::terminate` | `(TerminateContractData): ContractData` | Reason required, audited. |
| `ContractService::recurringRevenue` | `(): Money` | Sum of active recurring contracts, normalised monthly. |

## Events

None. This module fires and consumes no cross-domain events.

## Filament Artifacts

Nav group: **Pipeline**.

| # | Artifact | ui-strategy row | Notes |
|---|---|---|---|
| 1 | `ContractResource` | CRUD resource | Create-from-deal action, signed-PDF upload, renew/terminate actions. |
| 6 | `ContractRenewalWidget` | Widget | Renewals due in the next 90 days. |

**Access contract**: `canAccess()` = `can('crm.contracts.view-any') && hasModule('crm.contracts')`. See [[../../../architecture/filament-patterns]].

### Upload note

`markSigned` accepts `application/pdf` only, enforces a max size cap, and stores under `companies/{company_id}/contracts/` via Media Library. PDF generation uses spatie/laravel-pdf; storage via spatie/laravel-media-library — see [[../../../architecture/packages]].

## Jobs & Scheduling

| Command | Queue | Schedule | Purpose |
|---|---|---|---|
| `ContractLifecycleCommand` | default | daily 05:30 | signed → active at `start_date`; active → expired past `end_date`; auto-renew handled; `alerted_levels` once-guards for 90 / 30-day alerts. |

See [[../../../infrastructure/queue-horizon]].

## Caching

None.

## Search & Realtime

None beyond standard resource listing.
