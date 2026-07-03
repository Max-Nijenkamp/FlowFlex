---
domain: crm
module: contracts
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

**Nav group:** Pipeline

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `ContractResource` | #1 CRUD resource | tweaks: state-badge-column (contract status), custom-header-actions (create-from-deal, send, sign-off, renew, terminate), pdf-preview-panel (signed PDF) | signed-PDF upload; lifecycle actions ([[./features/contract-lifecycle]]) |
| `ContractRenewalsPage` *(assumed)* | #3 custom page | [[../../../architecture/patterns/page-blueprints#Kanban]] — read-only queue grouped by urgency (90 / 30 / overdue), renew/terminate per row | "Renewals" at `/crm/renewals` ([[./features/renewal-tracking]]); **not yet in Build Manifest** — see [[./unknowns]] |
| `ContractRenewalWidget` | #6 dashboard widget | [[../../../architecture/patterns/page-blueprints#Dashboard]] | renewals due next 90 days; widget polling 30–60s |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('crm.contracts.view-any') && BillingService::hasModule('crm.contracts')`
per [[../../../architecture/filament-patterns]] #1. `ContractRenewalsPage` is a custom page and MUST state this explicitly — Filament does not auto-gate custom pages. This module has no public/portal surface — all artifacts live behind the `/crm` panel guard.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Contract CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Status transition (send / sign-off / activate / expire / renew / terminate) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read, validate, write per [[../../../architecture/patterns/states]] |
| `ContractLifecycleCommand` (`alerted_levels` once-guard) | n-a | append-only alert guard on a scheduled command — no interactive concurrent writer |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

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
