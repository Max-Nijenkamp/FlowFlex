---
domain: legal
module: compliance-registers
feature: control-management
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Control Management

The requirement/control register per framework: status, evidence, and policy links.

## Behaviour

- Control = reference (unique per framework) + requirement + status.
- Status: compliant / partial / non-compliant / not-applicable.
- Compliant/partial requires an evidence note *(assumed)*; evidence files attach via Media Library.
- Optional `policy_id` links a control to a [[../../policy-library/_module|policy]].
- Gap report = non-compliant controls.

## UI

- **Kind**: simple-resource
- **Page**: `ControlResource` — list + create/edit at `/legal/compliance/controls`.
- **Layout**: table (reference, requirement snippet, status badge, owner, framework); filters framework/status + gap filter; form = reference/requirement + status + evidence note + file upload + policy link.
- **Key interactions**: set status (evidence required for green); upload evidence; link policy; filter to gaps.
- **States**: empty ("No controls yet") · loading (skeleton) · error ("Evidence note required for compliant") · selected (row → edit, evidence panel).
- **Gating**: view `legal.compliance.view-any`; update `legal.compliance.update-controls`.

## Data

- Owns / writes: `legal_controls` (+ evidence media).
- Reads: `legal.policies` for the policy link (read-only).
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: control statuses drive readiness + gap report.
- Shared entity: `legal_policies` (owned by legal.policies).

## Test Checklist

### Unit
- [ ] `SetControlStatusData` rejects compliant/partial without an evidence note
- [ ] Control reference uniqueness enforced per framework, not globally

### Feature (Pest)
- [ ] Setting status to compliant with evidence note persists + attaches media
- [ ] Gap report returns only non-compliant controls for the active company
- [ ] Policy link resolves read-only to `legal.policies` record

### Livewire
- [ ] Status filter + gap filter narrow the `ControlResource` table
- [ ] Save without evidence on compliant shows the validation error ("Evidence note required")

## Unknowns

- `*(assumed)*` evidence required for compliant/partial; cross-framework mapping absent — [[../unknowns]].

## Related

- [[../_module|Compliance Registers]] · [[./compliance-tasks]] · [[./audit-readiness-dashboard]]
