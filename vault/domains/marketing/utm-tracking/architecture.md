---
domain: marketing
module: utm-tracking
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# UTM Tracking — Architecture

Parent: [[_module]]

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `UtmService::record` | `record(RecordTouchData)` | Creates the first touch if absent (never overwritten); upserts the last touch. |
| `UtmService::attribution` | `attribution(model, from, to): AttributionData` | Contacts + deal value by source/medium/campaign, first- vs last-touch model; aggregate queries, no N+1. |
| `RecordUtmFromFormListener` | queued | On `FormSubmissionReceived`; extracts UTM from the submission's hidden fields / cookie payload *(assumed forms carry UTM hidden fields)*. |
| `BuildUtmUrlAction` | action | Generates a correctly encoded tagged URL. |

## Events

Consumes `FormSubmissionReceived` (from [[../forms/_module|forms]]). Fires none. See [[../../../architecture/event-bus]].

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `UtmBuilderPage` | Analytics | #7 custom page (form) | URL generator with copy |
| Attribution tables | — | rendered inside [[../marketing-analytics/_module\|Marketing Analytics]] dashboard | first/last toggle |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('marketing.utm.view')
        && BillingService::hasModule('marketing.utm');
}
```

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| `UtmService::record` first touch | Pessimistic | `lockForUpdate` (or insert-or-ignore on unique key) in transaction — first touch is immutable and must not be overwritten by a raced second submission |
| `UtmService::record` last touch | n-a | Upsert, last-write-wins is the intended semantic |
| `BuildUtmUrlAction` | n-a | Pure function, no persistence |
| Attribution reads | n-a | Read-only aggregation |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Related

- [[_module]] · [[data-model]] · [[../marketing-analytics/_module]] · [[../../../architecture/event-bus]]
