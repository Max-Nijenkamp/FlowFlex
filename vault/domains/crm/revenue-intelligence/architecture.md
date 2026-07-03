---
domain: crm
module: revenue-intelligence
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
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

**Nav group:** Intelligence

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `DealHealthResource` | #1 CRUD resource | tweaks: read-only-flow-owned (scores written by the `DealHealthService` recalc job — `canCreate(): false`) | at-risk queue sorted by score, factor breakdown |
| `WinLossPage` | #9 Report custom page | [[../../../architecture/patterns/page-blueprints#Report Builder / Query UI]] | apex charts: reasons, competitors, funnel; date-range filter |
| `RevenueIntelligenceDashboard` | #6 Dashboard page | [[../../../architecture/patterns/page-blueprints#Dashboard]] | velocity, conversion, health distribution; widget polling 30–60s |

**Access contract (mandatory):** every artifact gates on
`canAccess() = Auth::user()->can('crm.revenue-intelligence.view-any') && BillingService::hasModule('crm.revenue-intelligence')`
per [[../../../architecture/filament-patterns]] #1. `WinLossPage` and `RevenueIntelligenceDashboard` are custom pages
and MUST state this explicitly — Filament does not auto-gate custom pages.

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Deal-health recalc (`RecalculateDealHealthCommand`) | n-a | Derived scores — single nightly writer, idempotent per-deal upsert keyed by `deal_id`; no concurrent user edits |
| Win/loss row on deal close (direct service call from `DealService`) | n-a | Append-only, one row per closed deal keyed by `deal_id`; single writer on the close path |
| `DealHealthResource` / `WinLossPage` / dashboard | n-a | Read-only surfaces — no user writes |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

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
