---
domain: crm
module: leads
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
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

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `LeadResource` | Contacts | Standard CRUD resource | Nav sort `-1` so Leads sits above Contacts. Section form; status + source filters. |
| `ListLeads` page | — | List page | Empty state teaches the capture → work → convert flow. |
| "Convert to deal" row action | — | Row action | Gated `crm.leads.convert`; hidden once the lead is converted. |
| Edit action | — | Row action | Standard edit. |

See [[../../../architecture/filament-patterns]] and [[../../../architecture/ui-strategy]].

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('crm.leads.view-any')
        && BillingService::hasModule('crm.leads');
}
```

## Jobs & Scheduling

None.

## Search & Realtime

None specified.
