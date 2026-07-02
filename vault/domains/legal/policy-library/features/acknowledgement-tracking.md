---
domain: legal
module: policy-library
feature: acknowledgement-tracking
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Acknowledgement Tracking

Track which employees have acknowledged each policy version; reminders and a self-service acknowledge surface.

## Behaviour

- Ack row unique per `(policy_id, employee_id, version)`.
- Employees acknowledge their **own** record only ([[../security]]).
- `PolicyAckReminderCommand` (weekly Mon) reminds only unacknowledged audience members.
- Acknowledgement report: who has / hasn't, per policy × employee.

## UI

- **Kind**: custom-page (two surfaces)
- **Page**: `PolicyAcknowledgementPage` — matrix (`/legal/policies/acknowledgements`); `MyPoliciesPage` — self-service (`/legal/my-policies`).
- **Layout**: matrix = employees (rows) × policies (columns), cells acknowledged/pending, export; self-service = list of policies I must read, each with read + "I acknowledge" button.
- **Key interactions**: matrix — filter by policy/department, export CSV; self-service — open policy body, click acknowledge → cell flips.
- **States**: empty (matrix: "No published policies" · self: "You're all caught up") · loading (skeleton grid/list) · error (toast) · selected (pending cells highlighted).
- **Gating**: matrix `legal.policies.view-any`; self-service `legal.policies.acknowledge-own` (all employees).

## Data

- Owns / writes: `legal_policy_acknowledgements`.
- Reads: `hr.profiles` employees/departments to build the matrix + resolve "my" employee record (read-only).
- Cross-domain writes: none — reminders via `core.notifications` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: acknowledgement status read by legal.compliance as control evidence.
- Shared entity: `hr_employees` (owned by hr.profiles).

## Test Checklist

### Unit
- [ ] Ack uniqueness enforced per `(policy_id, employee_id, version)`

### Feature (Pest)
- [ ] Employee can acknowledge only their own record (`acknowledge-own` scope)
- [ ] Reminder command targets only unacknowledged audience members, weekly
- [ ] Matrix aggregates acknowledged/pending per policy × employee for the active company only

### Livewire
- [ ] Self-service acknowledge flips the cell + disappears from "to read" list
- [ ] Matrix export cites the `exports` limiter; matrix denied without `legal.policies.view-any`

## Unknowns

- `*(assumed)*` weekly reminder cadence — [[../unknowns]].

## Related

- [[../_module|Policy Library]] · [[./publication-versioning]] · [[../../compliance-registers/_module|legal.compliance]]
