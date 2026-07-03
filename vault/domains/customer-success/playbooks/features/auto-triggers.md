---
domain: customer-success
module: playbooks
feature: auto-triggers
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Auto Triggers

Automatically launch the right playbook when a lifecycle signal fires — health drop, renewal approaching, or a new customer.

## Behaviour

- `PlaybookTriggerCommand` runs daily. For each active playbook with a non-manual `trigger_type`, it evaluates the trigger against the read-API signal and launches a run for each matching account:
  - **health-drop** — account crossed into a worse tier (via the `cs.churn` / `cs.health` signal) matching `trigger_config`.
  - **renewal** — a `crm.contracts` renewal date falls within the configured window.
  - **new-customer** — a CRM account lifecycle transitioned to "customer" *(assumed)*.
- Each launch calls `PlaybookService::run`; the unique-active-run constraint dedupes so a persisting condition does not spawn duplicate concurrent runs.
- Manual playbooks are never auto-launched.

## UI

- **Kind**: background — no screen; it is the daily `PlaybookTriggerCommand`. Resulting runs appear in [[./playbook-runs|Playbook Runs]].
- **Page**: none (job). Trigger config is edited in the [[./playbook-builder|Playbook Builder]] form.
- **Key interactions**: n/a (scheduled).
- **States**: n/a; per-account launch failure is caught and the batch continues.
- **Gating**: no interactive surface; launches execute under the system context with the module's run semantics.

## Data

- Owns / writes: `cs_playbook_runs` + `cs_playbook_run_steps` (via `run`).
- Reads: health/churn tier signal (`cs.health` / `cs.churn`), renewal dates (`crm.contracts`), account lifecycle + owner (`crm.contacts`) — all via read APIs, never their tables.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: read signals from `cs.churn`/`cs.health`, `crm.contracts`, `crm.contacts` (poll, not events v1).
- Feeds: creates runs consumed by [[./playbook-runs|Playbook Runs]]; notifications via `core.notifications`.
- Shared entity: `crm_accounts` + contract renewal dates (read-only).

## Test Checklist

### Unit
- [ ] Trigger mapping: health-drop / renewal-window / new-customer each resolves the mapped playbook

### Feature (Pest)
- [ ] Daily poll firing twice creates no duplicate run (unique-active-run under lock)
- [ ] Inactive signal module -> trigger skipped silently
- [ ] Tenant isolation: triggers evaluate per company

### Livewire
- (none -- scheduled command)

## Unknowns

- Poll-not-event and single-source for the health-drop signal — [[../unknowns]].
- Re-trigger cooldown after a completed run is unspecified.

## Related

- [[../_module|Playbooks]] · [[./playbook-runs|Playbook Runs]] · [[./playbook-builder|Playbook Builder]]
- [[../../churn-risk/_module|cs.churn]] · [[../../../crm/contracts/_module|crm.contracts]]
