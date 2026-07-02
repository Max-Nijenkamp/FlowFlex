---
domain: support
module: sla
feature: sla-policies
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Feature: SLA Policies

Define per-priority response and resolution targets, optionally counted only during business hours.

## Behaviour

- A policy has a name, a `business_hours_only` flag, and one target row per priority (`first_response_minutes`, `resolution_minutes` where resolution > first-response).
- Policies attach to tickets by category or priority; a category may set a default policy.
- Compliance report: % of tickets meeting first-response and resolution targets over a period.

## UI

- **Kind**: simple-resource — CRUD with a per-priority targets repeater.
- **Page**: `SlaPolicyResource` (`/support/sla-policies`).
- **Layout**: list (name, business-hours flag, target count); form = name + flag + repeater of {priority, first-response min, resolution min}.
- **Key interactions**: add/remove target rows; validation resolution > first-response; save → `CreateSlaPolicyData`.
- **States**: empty (no policies → "create your first SLA" CTA) · loading (form save) · error (resolution ≤ first-response rejected inline) · selected (editing a policy).
- **Gating**: view `support.sla.view`; edit `support.sla.manage`.

## Data

- Owns / writes: `sup_sla_policies`, `sup_sla_targets`.
- Reads: business hours + timezone from `core.settings` (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `core.settings` business hours/timezone (read).
- Feeds: policies referenced by `sup_tickets.sla_policy_id` (support.tickets reads).
- Shared entity: company business-hours settings (owned by core.settings).

## Unknowns

- Warning threshold per-policy configurability *(assumed 80%)* — [[../unknowns]].

## Related

- [[../_module|SLA Management]] · [[./breach-monitoring]] · [[../../tickets/_module|support.tickets]]
