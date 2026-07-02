---
domain: customer-success
module: churn-risk
feature: rule-based-detection
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Rule-Based Detection

Deterministic, explainable churn-risk detection over existing CS/finance signals, run nightly after the health recalc.

## Behaviour

- `ChurnRiskService::evaluate` iterates customer accounts. For each, it collects **active** risk factors: red health tier, ≥2-tier drop, NPS detractor (soft), overdue invoices (soft), no engagement for N days.
- `risk_level` is derived from factor count / severity: low (1) / medium (2) / high (3) / critical (≥4 or a critical single factor) *(assumed)*.
- Upserts the open `cs_churn_risks` row: opens a new row if none, escalates `risk_level` if higher, refreshes `risk_factors`.
- Alerts the CSM via `core.notifications` **only** on a new open risk or an escalation — same-level re-detection does not re-alert.
- Auto-resolves (sets `resolved_at`) when all factors clear on a later run.
- Factors of inactive soft-dep modules are simply absent (not counted), mirroring health's renormalisation philosophy.

## UI

- **Kind**: background — detection has no screen of its own; it is the nightly `EvaluateChurnRiskCommand`. Results are viewed via the [[./at-risk-queue|At-Risk Queue]] resource + widget.
- **Page**: none (job). Produces rows consumed by `ChurnRiskResource` / `ChurnRiskWidget`.
- **Key interactions**: n/a (scheduled).
- **States**: n/a; per-account failure is caught and the batch continues; alert delivery retried by the queue.
- **Gating**: no interactive surface; downstream views gate on `cs.churn.view-any`.

## Data

- Owns / writes: `cs_churn_risks` (its own table only).
- Reads: health tier/breakdown (`cs.health`), latest NPS (`cs.nps`), overdue invoices (`finance.invoicing`), engagement + account owner (`crm.contacts`) — all via read APIs, never their tables.
- Cross-domain writes: none — the alert is dispatched via `core.notifications` (its own listener writes its own tables) ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none as events v1 — chained after the health recalc on schedule.
- Feeds: `core.notifications` (CSM at-risk alert); exposes open risks to `cs.analytics` (at-risk count, recovery rate).
- Shared entity: `crm_accounts` (read-only, keyed on) + `crm_accounts.owner_id` (CSM recipient).

## Unknowns

- Risk-level thresholds and the no-engagement window (N days) are assumed.
- Rule-based (not ML) is a deliberate v1 decision — [[../decisions/decision-2026-06-20-rule-based-churn-v1]].

## Related

- [[../_module|Churn Risk]] · [[./at-risk-queue|At-Risk Queue]]
- [[../../health-scores/_module|cs.health]] · [[../../../../security/data-ownership]]
