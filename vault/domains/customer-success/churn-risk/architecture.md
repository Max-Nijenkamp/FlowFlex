---
domain: customer-success
module: churn-risk
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Churn Risk — Architecture

## Services & Actions

Interface→Service: `ChurnRiskServiceInterface` → `ChurnRiskService`.

- `evaluate(?string $accountId = null): EvalResult` — runs after the health recalc (chained). For each account: gathers active risk factors, derives `risk_level` from factor count/severity, then opens / updates / resolves the `cs_churn_risks` row. Alerts only on a **new** open risk or an **escalation** (higher level), never on the same level.
- `resolve(ResolveRiskData): void` — manual resolution with a note *(assumed)*.

`RunRecoveryPlaybookAction` — soft-dep bridge; when `cs.playbooks` is active, launches the seeded "at-risk recovery" playbook for the account by calling `PlaybookService::run` (cs.playbooks writes its own tables).

Detection rules (v1, rule-based):

| Factor | Source (read API) | Rule |
|---|---|---|
| Red health tier | cs.health | current tier = red |
| Steep tier drop | cs.health | dropped ≥2 tiers since prior current row |
| NPS detractor | cs.nps (soft) | latest response category = detractor |
| Overdue invoices | finance.invoicing (soft) | ≥1 overdue invoice |
| No engagement | crm/cs.health engagement signal | last activity older than N days |

`risk_level` = low (1 factor) / medium (2) / high (3) / critical (≥4 or a critical single factor) *(assumed)*.

---

## Events

### Fires
None v1. An at-risk detection raises a CSM notification via `core.notifications`, not a cross-domain domain event *(assumed)*.

### Consumes
None v1. Evaluation is chained after the nightly health recalc rather than event-driven *(assumed)*.

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `EvaluateChurnRiskCommand` | default | nightly, after `RecalculateHealthScoresCommand` | open-risk upsert per account; alert only on new/escalated level; per-account failure continues batch |

The health→churn chain is the domain's key sequencing rule ([[../_index]]). Full queue context in [[../../../architecture/queue-jobs]].

---

## Filament Artifacts

**Nav group:** Customer Success

| Artifact | Kind ([[../../../architecture/patterns/feature-ui-spec]]) | Notes |
|---|---|---|
| `ChurnRiskResource` | simple-resource (read-only + actions) | severity-sorted at-risk queue; factor breakdown on view; row actions: run recovery playbook, resolve |
| `ChurnRiskWidget` | widget | counts by risk level for the CS dashboard |

**Access contract:** `canAccess() = Auth::user()->can('cs.churn.view-any') && BillingService::hasModule('cs.churn')` per [[../../../architecture/filament-patterns]] #1. The resolve action additionally requires `cs.churn.resolve`. No public/portal surface.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| `evaluate` open/update/resolve risk rows | n-a | Single scheduled writer chained after health recalc; per-account upsert |
| Manual `resolve` | Optimistic | Version-checked save per [[../../../architecture/patterns/optimistic-locking]] -- raced auto-evaluate vs manual resolve converges next run |
| `RunRecoveryPlaybookAction` | n-a | Delegates to `PlaybookService::run`, deduped there by unique-active-run |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Search & Realtime

- Search: none.
- Realtime: none — the queue reflects the last nightly evaluation.

---

## Security Notes

- No encrypted fields; no public endpoints; no rate limiter (internal actions only).

See [[./security]] for the full access contract and permissions.
