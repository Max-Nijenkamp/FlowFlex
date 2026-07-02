---
domain: crm
module: revenue-intelligence
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Revenue Intelligence — Architecture

## State Machine

None. This module derives scores and analyses from CRM deal/activity data; it holds no lifecycle state of its own.

## Services & Actions

| Method | Signature | Purpose |
|---|---|---|
| `DealHealthService::recalculate` | `(): RecalcResult` | Scans open deals, per-deal try/catch, upserts health rows. |
| `DealHealthService::atRisk` | `(threshold=40): Collection` | Open deals scoring below the threshold *(assumed threshold)*. |
| `WinLossService::analysis` | `(from, to): WinLossAnalysisData` | Reason breakdown, competitor table, conversion funnel, velocity stats. |

Win/loss rows are created by CRM's own deal-close path via a **direct service call** from `DealService`, not by cross-domain events — this is a same-domain relationship, so no event bus is used. See [[../../../architecture/event-bus]] for why intra-domain calls stay direct.

## Events

None fired or consumed. Win/loss capture is same-domain (direct service call).

## Filament Artifacts

Nav group: **Intelligence**.

| # | Artifact | ui-strategy row | Notes |
|---|---|---|---|
| 1 | `DealHealthResource` | CRUD (read-only) | At-risk queue sorted by score, factor breakdown. |
| 9 | `WinLossPage` | Report custom page | Apex charts: reasons, competitors, funnel. |
| 6 | `RevenueIntelligenceDashboard` | Dashboard page | Velocity, conversion, health distribution. |

**Access contract**: `canAccess()` = `can('crm.revenue-intelligence.view-any') && hasModule('crm.revenue-intelligence')`. See [[../../../architecture/filament-patterns]].

## Jobs & Scheduling

| Command | Queue | Schedule | Purpose |
|---|---|---|---|
| `RecalculateDealHealthCommand` | default | nightly 04:15 | Upsert per deal; re-run safe (idempotent). |

See [[../../../infrastructure/queue-horizon]].

## Caching

| Key | TTL | Invalidation |
|---|---|---|
| `company:{id}:crm:winloss:{from}:{to}` | 1h | TTL only |

The win/loss analysis dashboard is cached per date range. See [[../../../architecture/caching]] and [[../../../infrastructure/cache-redis]].

## Search & Realtime

None.
