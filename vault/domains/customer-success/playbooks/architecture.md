---
domain: customer-success
module: playbooks
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Playbooks — Architecture

## Services & Actions

Interface→Service: `PlaybookServiceInterface` → `PlaybookService`.

- `run(RunPlaybookData): PlaybookRun` — creates a run (guarded by the unique-active-run constraint), materialises `cs_playbook_run_steps` from the template steps with `due_date = started_at + day_offset` and `assignee_id` resolved from `owner_role` (CSM = account owner *(assumed)*), notifies assignees.
- `PlaybookService::cancel(runId)` — cancels an active run.

`CompletePlaybookStepAction` — marks a run step done; when the last open step closes, the run transitions to `completed`.

Auto-trigger hooks (daily poll, in `PlaybookTriggerCommand`):

| Trigger | Signal source (read API) | Fires |
|---|---|---|
| health-drop | cs.churn / cs.health tier drop | run the mapped playbook for the account |
| renewal | crm.contracts renewal date within window | run the renewal playbook |
| new-customer | crm account lifecycle → customer *(assumed)* | run the onboarding playbook |

Each auto-run is deduped by the unique-active-run constraint (no duplicate concurrent runs per playbook+account).

---

## Events

### Fires
None v1. Step assignment + due reminders are `core.notifications`, not cross-domain events *(assumed)*.

### Consumes
None v1. Auto-triggers poll signal sources daily rather than reacting to events *(assumed)*.

---

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `PlaybookTriggerCommand` | default | daily | unique-active-run guard prevents duplicate auto-runs |
| Step due reminders | notifications | daily | `reminded` flag, once per step |

Full queue context in [[../../../architecture/queue-jobs]].

---

## Filament Artifacts

**Nav group:** Customer Success

| Artifact | Kind ([[../../../architecture/patterns/feature-ui-spec]]) | Notes |
|---|---|---|
| `PlaybookResource` | simple-resource | name + trigger + ordered step **repeater**; templates seeded |
| `PlaybookRunResource` | simple-resource | per-account run: step checklist, complete/skip actions, progress |

**Access contract:** `canAccess() = Auth::user()->can('cs.playbooks.view-any') && BillingService::hasModule('cs.playbooks')` per [[../../../architecture/filament-patterns]] #1. Create/edit requires `cs.playbooks.manage`; launching a run requires `cs.playbooks.run`; completing steps requires `cs.playbooks.complete-steps`. No public/portal surface.

---

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| `run` (create run + steps) | Pessimistic | Unique-active-run constraint checked under `lockForUpdate` -- auto-trigger + manual launch race yields one run |
| `CompletePlaybookStepAction` | Pessimistic | Step + run locked -- last-step close transitions the run to `completed` exactly once |
| `cancel` | Pessimistic | Run state transition under lock per patterns/states |
| Template/builder CRUD | Optimistic | Version-checked save per [[../../../architecture/patterns/optimistic-locking]] |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].

## Search & Realtime

- Search: none.
- Realtime: none — run checklist reflects saved step state.

---

## Security Notes

- No encrypted fields; no public endpoints; no rate limiter (internal panel actions only).

See [[./security]] for the full access contract and permissions.
