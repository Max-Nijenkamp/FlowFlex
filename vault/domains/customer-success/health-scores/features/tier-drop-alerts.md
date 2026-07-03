---
domain: customer-success
module: health-scores
feature: tier-drop-alerts
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Tier-Drop Alerts

When an account's health tier drops (green→amber, amber→red, or green→red), notify its CSM once per drop so the team can intervene early.

## Behaviour

- After `HealthScoreService::recalculate` writes the new current row, it compares the new tier to the prior current row's tier for that account.
- A **downward** tier transition (rank green > amber > red) raises one CSM notification via `core.notifications`. An upward transition or same-tier result raises nothing.
- Deduped **once per drop**: the notification keys on `(account_id, from_tier, to_tier, calculated_at date)` so a re-run on the same day does not re-alert. A subsequent further drop (amber→red after green→amber) is a new drop and alerts again.
- CSM identity = the CRM account `owner_id` *(assumed: crm account owner = CSM)* — read from `crm.contacts`, never written.
- This is a notification, **not** a cross-domain domain event v1 *(assumed)*. Downstream churn evaluation reads the score directly on its own chained schedule.

## UI

- **Kind**: background — no dedicated screen; the alert is produced inside the recalc job and delivered through the notifications centre (`core.notifications`).
- **Page**: none. The resulting alerts surface in the shared notification bell / inbox owned by `core.notifications`; the at-risk list on `HealthDashboardPage` reflects the same tier changes visually.
- **Layout**: n/a (background).
- **Key interactions**: n/a — triggered by the nightly recalc.
- **States**: n/a (no interactive surface); delivery failure is retried by the queue.
- **Gating**: recipients are the account's CSM; visibility of the notification follows `core.notifications` gating. No new permission of its own.

## Data

- Owns / writes: nothing new — reuses `cs_health_scores` (compares prior vs new current row, both owned by this module).
- Reads: CRM account `owner_id` (CSM) via `crm.contacts` read API — never CRM tables.
- Cross-domain writes: the alert is dispatched **via `core.notifications`** (its listener writes its own tables), never a direct write into another domain ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none — driven by this module's own recalc step.
- Feeds: `core.notifications` (CSM alert on downward tier change). `cs.churn` independently reads the same score signal on its chained nightly run.
- Shared entity: `crm_accounts.owner_id` (owned by `crm.contacts`) — read-only, resolves the CSM recipient.

## Test Checklist

### Unit
- [ ] Drop detection: green->amber, amber->red, green->red each detected once vs prior current row

### Feature (Pest)
- [ ] CSM notified once per drop; unchanged or improving tier sends nothing; re-drop after recovery alerts again
- [ ] Tenant isolation: notification targets the account's own-company CSM

### Livewire
- (none -- background alert path)

## Unknowns

- CSM = CRM account `owner_id` is assumed; a dedicated CS-owner assignment is not modelled v1.
- Once-per-drop dedupe key (per calendar day) is assumed.
- Tier-drop alert as notification (not domain event) is assumed — revisit if another domain needs to react.

## Related

- [[../_module|Health Scores]]
- [[./composite-scoring|Composite Scoring]]
- [[../../churn-risk/_module|cs.churn]]
- [[../../../../security/data-ownership]]
