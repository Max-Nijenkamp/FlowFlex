---
domain: customer-success
module: playbooks
feature: playbook-runs
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Playbook Runs

Launch a playbook against an account, track step completion, and close the run when all steps are done.

## Behaviour

- `PlaybookService::run(RunPlaybookData)` creates a `cs_playbook_runs` row (blocked by the unique-active-run constraint if one already exists), materialises `cs_playbook_run_steps` from the template with `due_date = started_at + day_offset` and `assignee_id` resolved from `owner_role` (CSM = account owner *(assumed)*), and notifies assignees.
- `CompletePlaybookStepAction` marks a step `done` (or `skipped`); when the last open step closes, the run → `completed`.
- Due-date reminders fire once per step (guarded by `reminded`).
- A run can be cancelled (frees the unique-active slot).

## UI

- **Kind**: simple-resource — `PlaybookRunResource`.
- **Page**: "Playbook Runs" at `/crm/playbook-runs` (Customer Success nav group).
- **Layout**: table (account, playbook, status, progress, started); run detail = step checklist with due dates, assignees, and complete/skip actions; progress bar.
- **Key interactions**: launch run (from playbook or churn one-click) · check off / skip steps · cancel run · filter by status/account.
- **States**: empty (no runs → "no active playbooks running") · loading (checklist skeleton) · error (duplicate active run rejected → toast; step complete failure → retry) · selected (run opened, step focused).
- **Gating**: `cs.playbooks.view-any` to view; `cs.playbooks.run` to launch/cancel; `cs.playbooks.complete-steps` to check off steps.

## Data

- Owns / writes: `cs_playbook_runs`, `cs_playbook_run_steps` (own tables only).
- Reads: account name + `owner_id` (assignee) via `crm.contacts` read API — never CRM tables.
- Cross-domain writes: none — assignment/reminder notifications dispatch via `core.notifications` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: launched by [[./auto-triggers|Auto Triggers]] and by `cs.churn` one-click recovery (`RunRecoveryPlaybookAction`).
- Feeds: `core.notifications` (assignments, reminders); `cs.analytics` reads run/step completion for playbook effectiveness.
- Shared entity: `crm_accounts` (read-only) + owner (assignee).

## Test Checklist

### Unit
- [ ] Step materialisation: `due_date = started_at + day_offset`, assignee from `owner_role` (CSM = account owner *(assumed)*)

### Feature (Pest)
- [ ] Completing the last open step transitions the run to `completed` exactly once under raced completions
- [ ] Cancel stops reminders; duplicate active run for playbook+account rejected
- [ ] Tenant isolation + permission: run/complete/cancel verbs enforced

### Livewire
- [ ] Run view lists steps with due dates; complete action gated to assignee/CSM; cancel confirms

## Unknowns

- Re-trigger cooldown after completion, and `manager` assignee resolution — [[../unknowns]].

## Related

- [[../_module|Playbooks]] · [[./playbook-builder|Playbook Builder]] · [[./auto-triggers|Auto Triggers]]
- [[../../churn-risk/_module|cs.churn]] · [[../../../../security/data-ownership]]
