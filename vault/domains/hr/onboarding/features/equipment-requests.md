---
domain: hr
module: onboarding
feature: equipment-requests
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: Equipment Requests

Part of [[../_module]].

## Purpose

Request equipment (laptop, phone, access cards) for a new hire, routed to IT.

## Behavior

- v1: modeled as onboarding tasks with `assigned_role = it` — task type only.
- P3: intended to create real IT tickets (deferred; no target module named).

## Tables / Permissions / Events

- Tables: `hr_onboarding_tasks`, `hr_onboarding_plan_tasks`.
- Permissions: `hr.onboarding.complete-task`, `hr.onboarding.view`.
- Events: none.

## UI

- **Kind**: simple-resource (fires event to IT)
- **Page**: "Equipment Requests" (`/hr/onboarding/equipment`)
- **Layout**: table/form of equipment requests (laptop, phone, access) tied to a plan; v1 = task-type only, P3 = real IT tickets.
- **Key interactions**: create/view an equipment request against a plan.
- **States**: empty = "No equipment requests" · loading = skeleton · error = validation · selected = request detail.
- **Gating**: visible with `hr.onboarding.view`; create requires `hr.onboarding.update` *(assumed)*.

> [!warning] UNVERIFIED
> v1 is a task-type only; the IT ticket integration (event to IT provisioning) is P3/soft and unconfirmed.

## Data

- Owns / writes: `hr_onboarding_plan_tasks` (equipment task type) — v1 no dedicated table *(assumed)*.
- Reads: none.
- Cross-domain writes: via events only (equipment/asset request event to IT — P3/UNVERIFIED).

## Relations

- Consumes: none.
- Feeds: equipment/asset request event → consumed by IT provisioning *(P3, soft, UNVERIFIED)*.
- Shared entity: none.

> UNVERIFIED: real IT-ticket integration deferred to P3 — see [[../unknowns]].

## Test Checklist

### Unit
- [ ] Equipment request is modeled as a plan task with `assigned_role = it`

### Feature (Pest)
- [ ] Creating an equipment request against a plan persists an `it` task
- [ ] v1 fires no IT provisioning event (P3 integration deferred)

### Livewire
- [ ] Create-request action available on the plan
- [ ] Action denied without `hr.onboarding.update`
