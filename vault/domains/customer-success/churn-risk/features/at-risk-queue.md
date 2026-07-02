---
domain: customer-success
module: churn-risk
feature: at-risk-queue
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# At-Risk Queue

A severity-sorted worklist of open churn risks so CSMs can triage and act, with a one-click recovery-playbook launch.

## Behaviour

- Lists all open `cs_churn_risks` (resolved excluded by default), sorted critical → low.
- Each row shows the account, risk level badge, factor breakdown, assigned CSM, and detected-at.
- Row actions: **Run recovery playbook** (launches the seeded at-risk playbook via `cs.playbooks` — hidden when that module is inactive) and **Resolve** (manual close with a note).
- Resolving sets `resolved_at`; the row leaves the open queue. Auto-resolution (factors cleared) removes it on the next evaluation without user action.
- The dashboard widget shows counts by risk level.

## UI

- **Kind**: simple-resource (read-only list + row actions) — `ChurnRiskResource`. Plus a `ChurnRiskWidget`.
- **Page**: "Churn Risk" at `/crm/churn-risk` (Customer Success nav group).
- **Layout**: table sorted by severity; risk-level badge column (red/orange/amber/grey); factor breakdown in the view/infolist; filters by risk level and CSM.
- **Key interactions**: filter by level/CSM · open row → factor detail · Run recovery playbook (confirm → `PlaybookService::run`) · Resolve (note → `resolve`).
- **States**: empty (no open risks → "no accounts at risk" reassurance) · loading (table skeleton) · error (toast + retry) · selected (row expanded / action confirming). Run-playbook action disabled/hidden when `cs.playbooks` inactive.
- **Gating**: `cs.churn.view-any` to view; `cs.churn.resolve` for Resolve and Run-recovery actions.

## Data

- Owns / writes: `cs_churn_risks` (resolve writes `resolved_at`).
- Reads: account name + owner via `crm.contacts` read API; running a playbook calls `cs.playbooks` service — never their tables.
- Cross-domain writes: none directly — launching a recovery playbook goes through `PlaybookService::run`, which writes only `cs.playbooks` tables ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (rows are produced by [[./rule-based-detection|detection]]).
- Feeds: `cs.playbooks` (one-click recovery run); `cs.analytics` reads open-risk counts + recovery rate.
- Shared entity: `crm_accounts` (read-only display + owner).

## Unknowns

- Whether resolving a risk should also cancel an in-flight recovery playbook run is unspecified — [[../unknowns]].
- Manual `resolve` note is assumed optional.

## Related

- [[../_module|Churn Risk]] · [[./rule-based-detection|Rule-Based Detection]]
- [[../../playbooks/_module|cs.playbooks]] · [[../../../../security/data-ownership]]
