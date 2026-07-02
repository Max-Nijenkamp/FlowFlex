---
domain: lms
module: lms-analytics
type: architecture
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# LMS Analytics — Architecture

## Data Model

**No tables.** Aggregates read-only over `lms_enrolments`, `lms_lesson_progress`, `lms_certificates`, `lms_employee_skills` (each owned by its module).

## Services & Actions

| Method | Responsibility |
|---|---|
| `LmsAnalyticsService::metrics(CarbonImmutable $from, CarbonImmutable $to): LmsMetricsData` | Aggregate metrics for the window; soft-dep sections conditional on module activation; no N+1. |

Output DTO `LmsMetricsData` carries: completion rates, compliance %, engagement, quiz performance, certification counts, skill trends, course popularity.

## Caching

| Key | TTL | Invalidated by |
|---|---|---|
| `company:{id}:lms:metrics:{from}:{to}` | 1 h historical / 15 min current | TTL only |

See [[../../../architecture/caching]].

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `LmsDashboardPage` | Analytics | #6 dashboard page + apex charts | Compliance tab, export. |
| `CompletionRateWidget` / `ComplianceWidget` / `EngagementWidget` | Analytics | #6 widgets | On the dashboard. |

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('lms.analytics.view')
        && BillingService::hasModule('lms.analytics');
}
```

## Jobs & Scheduling

None (metrics computed on demand + cached).

## Events

None fired or consumed. Pure read/aggregation layer.
