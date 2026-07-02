---
domain: crm
module: deals
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `DealResource` | #1 CRUD resource | list filters: stage, owner, status; bulk owner reassign *(assumed)* |
| Deal view page | #2 detail with tabs | Overview, Activities (if crm.activities), Products, Files |
| `CreateInvoiceAction` | modal action on view page | visible only when `status=won` AND `hasModule('finance.invoicing')` |
| `CloseDealAction` | modal action | outcome + lost_reason form |

The Kanban board itself is [[../../crm/pipeline/_module|crm.pipeline]]'s `PipelineBoardPage` — ui-strategy row #3, Reverb broadcast.

**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('crm.deals.view-any') && BillingService::hasModule('crm.deals')` per [[../../../architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

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
