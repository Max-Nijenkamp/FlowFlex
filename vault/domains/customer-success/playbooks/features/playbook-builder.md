---
domain: customer-success
module: playbooks
feature: playbook-builder
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Playbook Builder

Define a reusable playbook: name, trigger, and an ordered list of steps with roles and due offsets.

## Behaviour

- A playbook has a `name`, a `trigger_type` (manual / health-drop / renewal / new-customer) with `trigger_config`, and ≥1 ordered `cs_playbook_steps`.
- Each step: title, description, `owner_role` (csm / manager), `day_offset` (days from run start), `order`.
- `is_active` toggles whether the playbook can be run / auto-triggered.
- Three templates seeded via `CsPlaybookTemplatesSeeder`: onboarding, renewal, at-risk recovery.

## UI

- **Kind**: simple-resource — `PlaybookResource` (with a step repeater in the form).
- **Page**: "Playbooks" at `/crm/playbooks` (Customer Success nav group).
- **Layout**: table (name, trigger, step count, active); form = name + trigger select (+ config fields shown per trigger) + ordered step **repeater** (title, description, owner role, day offset).
- **Key interactions**: create/edit playbook; add/reorder steps in the repeater; toggle active; trigger-specific config fields appear on trigger change.
- **States**: empty (only seeded templates → "duplicate a template or create your own") · loading (table skeleton) · error (validation: ≥1 step, offsets ≥0) · selected (playbook opened in edit).
- **Gating**: `cs.playbooks.view-any` to view; `cs.playbooks.manage` to create/edit.

## Data

- Owns / writes: `cs_playbooks`, `cs_playbook_steps` (own tables only).
- Reads: nothing cross-domain at build time (trigger config references are validated lazily at run/poll).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: definitions consumed by [[./playbook-runs|Playbook Runs]] and [[./auto-triggers|Auto Triggers]].
- Shared entity: none written; owner-role resolution happens at run time against `crm_accounts.owner_id`.

## Test Checklist

### Unit
- [ ] Step validation: ordered list, `day_offset` >= 0, `owner_role` in allowed set

### Feature (Pest)
- [ ] Template edits do not mutate steps of already-running runs (steps are materialised copies)
- [ ] Tenant isolation + permission: builder gated, templates per company

### Livewire
- [ ] Builder repeater orders steps; validates offsets/roles; hidden without permission/module

## Unknowns

- `manager` role resolution unspecified; linear-only step ordering — [[../unknowns]].

## Related

- [[../_module|Playbooks]] · [[./playbook-runs|Playbook Runs]] · [[./auto-triggers|Auto Triggers]]
