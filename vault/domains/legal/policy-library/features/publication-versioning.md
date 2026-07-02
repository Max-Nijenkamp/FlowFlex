---
domain: legal
module: policy-library
feature: publication-versioning
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Publication & Versioning

Publish a policy to its audience; a changed body bumps the version and resets acknowledgements.

## Behaviour

- `PolicyService::publish` — if the body changed, `version++` and existing acknowledgements are reset (re-ack required).
- Publishes to all employees or scoped departments (`audience`).
- Notifies the audience via `core.notifications`.
- Status `draft → published → archived`.

## UI

- **Kind**: custom-page — publish is a bespoke action with audience preview + version diff, not a plain form save.
- **Page**: publish action/modal launched from `PolicyResource` (`/legal/policies/{id}` → Publish).
- **Layout**: modal showing target audience count, version change (n → n+1), "resets acknowledgements" warning; confirm.
- **Key interactions**: pick audience (all / departments) → preview recipient count → confirm publish → version bump + notify.
- **States**: empty (no audience selected → disabled) · loading (publishing) · error (toast) · selected (audience chips highlighted).
- **Gating**: `legal.policies.publish`.

## Data

- Owns / writes: `legal_policies` (`status`, `version`), `legal_policy_acknowledgements` (reset on version bump).
- Reads: `hr.profiles` departments/employees to resolve audience (read-only).
- Cross-domain writes: none — audience notified via `core.notifications` ([[../../../../security/data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: publish event triggers audience notification + fresh ack requirement.
- Shared entity: `hr` departments/employees (owned by hr.profiles).

## Test Checklist

### Unit
- [ ] Publish with unchanged body keeps the version; changed body bumps `version++`

### Feature (Pest)
- [ ] Version bump resets existing acknowledgements (re-ack required)
- [ ] Publish notifies exactly the resolved audience (all vs scoped departments) via core.notifications
- [ ] Concurrent publish bumps the version once (row lock; second publisher rejected)

### Livewire
- [ ] Publish modal previews recipient count + reset warning; confirm publishes
- [ ] Denied without `legal.policies.publish`

## Unknowns

- `*(assumed)*` reset-all-acks on any version bump — [[../unknowns]].

## Related

- [[../_module|Policy Library]] · [[./policy-authoring]] · [[./acknowledgement-tracking]]
