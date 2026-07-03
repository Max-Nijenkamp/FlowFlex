---
domain: crm
module: deals
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Deals — Architecture

## State Machine

Column: `crm_deals.status` — `spatie/laravel-model-states`, base `DealState`. Stage movement within `open` is NOT a state transition — it updates `stage_id` + `stage_entered_at`.

| State | Transitions to | Triggered by (permission) | Side effects |
|---|---|---|---|
| `open` | `won` | `crm.deals.close` | fires `DealWon`; sets `actual_close_date`, probability 100 |
| `open` | `lost` | `crm.deals.close` | fires `DealLost`; requires `lost_reason`; probability 0 |
| `won` | `open` | `crm.deals.reopen` *(assumed)* | clears close fields; audited |
| `lost` | `open` | `crm.deals.reopen` *(assumed)* | clears lost fields; audited |

Initial: `open`. Transitions audited via activitylog.

---

## Services & Actions

Interface→Service: `DealServiceInterface` → `DealService` (`Providers/CRM`).

| Method | Notes |
|---|---|
| `create(CreateDealData $data): DealData` | |
| `update(string $dealId, UpdateDealData $data): DealData` | |
| `moveToStage(string $dealId, string $stageId): DealData` | throws `ClosedDealImmutableException`; resets `stage_entered_at`, probability from stage default |
| `close(CloseDealData $data): DealData` | throws `InvalidStateTransitionException`; fires `DealWon`/`DealLost` |
| `duplicate(string $dealId): DealData` | copies deal + contacts + products, status `open`, first stage *(assumed)* |
| `weightedPipelineValue(?string $ownerId = null): Money` | brick/money sum of `value × probability` |

---

## Events

### Fires: DealWon

| Payload field | Type | Notes |
|---|---|---|
| company_id | string | always first |
| deal_id | string | |
| account_id | ?string | |
| contact_id | ?string | |
| owner_id | string | |
| value_cents | int | |
| currency | string | ISO 4217 |
| won_at | CarbonImmutable | |

Intended consumers:
- `finance.invoicing` → `CreateInvoiceStubListener` — draft invoice, line items from `crm_deal_products`, due date = company default terms, no auto-send
- CRM sequences → `EnrollInSuccessSequenceListener`

Contract source of truth: [[../../../architecture/event-bus]].

### Fires: DealLost

| Payload field | Type | Notes |
|---|---|---|
| company_id | string | |
| deal_id | string | |
| owner_id | string | |
| lost_reason | string | |
| lost_at | CarbonImmutable | |

No v1 consumers — analytics intended to consume in Phase 3 *(assumed)*.

---

## Filament Artifacts

**Nav group:** Pipeline

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `DealResource` | #1 CRUD resource | tweaks: view-page-tabs, state-badge-column, custom-header-actions (close / reopen / create-invoice) | list filters: stage, owner, status; bulk owner reassign *(assumed)* |
| Deal view page | #2 record detail with tabs | tweaks: view-page-tabs, relation-manager-timeline (Activities, if crm.activities) | Overview, Activities, Products, Files |
| `CreateInvoiceAction` | #2 view-page header action | tweak: custom-header-actions | visible only when `status=won` AND `hasModule('finance.invoicing')` |
| `CloseDealAction` | #2 view-page header action | tweak: custom-header-actions | outcome + lost_reason form; needs `crm.deals.close` |

The Kanban board itself is [[../../crm/pipeline/_module|crm.pipeline]]'s `PipelineBoardPage` — ui-strategy row #3 ([[../../../architecture/patterns/page-blueprints#Kanban]]), Reverb broadcast.

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('crm.deals.view-any') && BillingService::hasModule('crm.deals')`
per [[../../../architecture/filament-patterns]] #1. Custom pages MUST state this explicitly — Filament does not auto-gate them. Public/portal surfaces declare their guest or scoped-portal guard + signed-token semantics instead (Vue+Inertia per [[../../../architecture/ui-strategy]]).

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Deal CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Stage move (`moveToStage`) | Pessimistic | `DB::transaction()` + `lockForUpdate()`, re-read, validate, write per [[../../../architecture/patterns/states]] |
| Close won/lost + reopen | Pessimistic | `DB::transaction()` + `lockForUpdate()` state transition per [[../../../architecture/patterns/states]] |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

---

## Search & Realtime

Meilisearch (Scout) — see [[../../../architecture/search]]:

- Deals indexed: `name`, account name, contact name — searchable from CRM global search *(assumed)*
- Realtime: none on `DealResource` (CRUD default). Board realtime lives in crm.pipeline.

---

## Implementation Notes (tense-softened)

The following notes from the original spec described the module as if it were built (2026-06-12 session). They are retained here as **intended design** pending actual build:

- File attachments on deals are intended via `spatie/laravel-media-library` (`attachments` collection, tenant-scoped paths)
- Deal form: value entered in euros (stored cents), stage select grouped per pipeline, organisation (account) link
- Board "New deal" action is intended to create open deals directly in any stage of the active pipeline
