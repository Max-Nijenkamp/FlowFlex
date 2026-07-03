---
domain: crm
module: leads
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Leads — Architecture

## Status Lifecycle

Leads carry a simple status field (no `spatie/laravel-model-states` machine specified in source *(assumed)*):

```
new → working → qualified → converted
                   └──────→ unqualified
```

- `converted` is terminal and is stamped by `ConvertLeadAction` alongside `converted_deal_id` + `converted_at`.
- `unqualified` is a dead-end status set manually.

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `App\Actions\CRM\ConvertLeadAction` | lorisleiva action | Convert a lead → deal in a DB transaction; resolve/create the contact; throw `ValidationException` when the lead is already converted or when no pipeline/stage exists. |

### ConvertLeadAction flow

1. Load the lead; guard: if `status = converted` (or `converted_deal_id` is set), throw `ValidationException` (idempotent — no reconversion).
2. Resolve the default pipeline and its first stage; if none exists, throw `ValidationException`.
3. Match a `crm_contacts` row by the lead email within the company; create it if none matches.
4. Create a `crm_deals` row in the first stage, seeded with the lead's `estimated_value_cents` and the stage's probability.
5. Stamp the lead: `status = converted`, `converted_deal_id = deal.id`, `converted_at = now()`.
6. Commit the transaction.

## Events

None fired or consumed. See [[../../../architecture/event-bus]] for the platform contract — this module currently defines no cross-domain events. Emitting a `LeadConverted` event is an open design question (see [[unknowns]]).

## Filament Artifacts

**Nav group:** Contacts

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `LeadResource` | #1 CRUD resource | tweaks: custom-header-actions (convert-to-deal — own permission `crm.leads.convert`, hidden once `status = converted`) | Nav sort `-1` (Leads above Contacts); section form; status + source filters; standard Edit row action |
| `ListLeads` page | #1 CRUD resource (list page) | standard list page | Empty state teaches the capture → work → convert flow |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('crm.leads.view-any') && BillingService::hasModule('crm.leads')`
per [[../../../architecture/filament-patterns]] #1. The "Convert to deal" action is additionally gated on
`crm.leads.convert` and hidden once the lead is converted. There are no custom pages or public/portal surfaces in
this module.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Lead CRUD (form, API) | Optimistic | `updated_at` stale-check on save → `StaleRecordException` → conflict notification ([[../../../architecture/patterns/optimistic-locking]]) |
| Lead convert (`ConvertLeadAction` — status → `converted`) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the lead, re-read the idempotency guard (`status`/`converted_deal_id`), then create the deal/contact via `DealService`/`ContactService` per [[../../../architecture/patterns/states]] — prevents a double-convert race creating two deals |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Jobs & Scheduling

None.

## Search & Realtime

None specified.
