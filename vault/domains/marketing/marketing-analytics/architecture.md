---
domain: marketing
module: marketing-analytics
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Marketing Analytics — Architecture

Parent: [[_module]]

## Services & Actions

| Class | Signature | Responsibility |
|---|---|---|
| `MarketingAnalyticsService::metrics` | `metrics(from, to): MarketingMetricsData` | Aggregate queries across marketing tables (no N+1); soft-dep sections null when the source module is inactive; CSV export path. |

Reads only — no writes, no state machine, no jobs.

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:marketing:metrics:{from}:{to}` | 1 h historical / 15 min current | TTL only |

See [[../../../architecture/caching]].

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `MarketingDashboardPage` | Analytics | #6 dashboard page + apex charts | date-range filter; soft-dep widgets conditional; polling 60s |
| `CampaignPerformanceWidget` | Analytics | #6 widget | open/click/bounce series |
| `FormConversionWidget` | Analytics | #6 widget | views vs submissions |
| `AttributionWidget` | Analytics | #6 widget | source/campaign (from marketing.utm) |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('marketing.analytics.view')
        && BillingService::hasModule('marketing.analytics');
}
```

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| All dashboard/widget/export paths | n-a | Read-only analytics; no writes |
| Redis aggregate cache writes | n-a | TTL-keyed cache set; idempotent recompute, last-write-wins safe |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Related

- [[_module]] · [[api]] · [[../../../architecture/caching]] · [[../../../architecture/performance]]
