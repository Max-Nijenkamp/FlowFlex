---
domain: customer-success
module: health-scores
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Health Scores — Architecture

## Services & Actions

Interface→Service: `HealthScoreServiceInterface` → `HealthScoreService`.

- `recalculate(?string $accountId = null): void` — recompute score(s); all customer accounts when null, one when given; upserts one `cs_health_scores` row per account per run, marks it current
- `breakdown(string $accountId): HealthScoreData` — per-factor contributions for explainability (each factor value × weight = contribution)
- `trend(string $accountId, int $days = 90): array` — score history over time from prior calculation rows

`SignalRegistry` — resolves the active signal sources (support ticket volume, NPS sentiment, payment status, engagement recency) and renormalises weights over only the modules that are active. Each signal is fetched through the owning domain's read API, never its tables.

---

## Events

### Fires

None v1. A tier drop raises a CSM notification via `core.notifications`, not a cross-domain domain event *(assumed)*.

### Consumes

None v1. Signals are pulled on the nightly schedule, not event-driven *(assumed)*.

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `RecalculateHealthScoresCommand` | default | nightly 04:30 | upsert current row per account; per-account failure continues batch |

Full queue context rules in [[../../../architecture/queue-jobs]].

---

## Filament Artifacts

**Nav group:** Customer Success

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `HealthScoreResource` | #1 CRUD resource (read-only) | list per account: score, tier badge, factor breakdown; filters by tier; no create/edit (scores are computed) |
| `HealthDashboardPage` | #4 custom page | tier segmentation, distribution, drill-down; factor-weight configuration form |

**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('cs.health.view-any') && BillingService::hasModule('cs.health')` per [[../../../architecture/filament-patterns]] #1 — custom pages state it explicitly. The configuration form additionally checks `cs.health.configure`. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[../../../architecture/ui-strategy]]).

---

## Search & Realtime

- Search: none (scores are internal, not a global-search surface).
- Realtime: none — dashboard reflects the last nightly recalc.

---

## Security Notes

- No encrypted fields; no public endpoints; no rate limiter (internal read API only).

See [[./security]] for full access contract and permissions.
