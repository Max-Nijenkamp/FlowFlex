---
domain: legal
module: policy-library
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Policy Library

Company policies (privacy, security, HR, code of conduct) with versioning, acknowledgement tracking, and publication to employees.

---

## Module-key

`legal.policies`

**Priority:** p3
**Panel:** legal
**Permission prefix:** `legal.policies`
**Tables:** `legal_policies`, `legal_policy_acknowledgements`

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../../hr/employee-profiles/_module\|hr.profiles]] | acknowledgements per employee |
| Hard | [[../../core/billing-engine/_module\|core.billing]] + [[../../core/rbac/_module\|core.rbac]] + [[../../core/notifications/_module\|core.notifications]] | gating, permissions, reminders |
| Soft | [[../compliance-registers/_module\|legal.compliance]] | policy ↔ control links |

---

## Core Features

- [[./features/policy-authoring|Policy authoring]] — rich text (purified), category, review cycle
- [[./features/publication-versioning|Publication & versioning]] — publish to audience, version bump resets acks
- [[./features/acknowledgement-tracking|Acknowledgement tracking]] — who has/hasn't, reminders, self-service acknowledge

Full data model + flow: [[./data-model]] · [[./architecture]].

---

## Build Manifest

```
database/migrations/xxxx_create_legal_policies_table.php
database/migrations/xxxx_create_legal_policy_acknowledgements_table.php
app/Models/Legal/{Policy,PolicyAcknowledgement}.php
app/Data/Legal/{CreatePolicyData,AcknowledgeData}.php
app/Services/Legal/PolicyService.php
app/Actions/Legal/AcknowledgePolicyAction.php
app/Console/Commands/Legal/PolicyAckReminderCommand.php
app/Filament/Legal/Resources/PolicyResource.php
app/Filament/Legal/Pages/{PolicyAcknowledgementPage,MyPoliciesPage}.php
database/factories/Legal/{PolicyFactory,PolicyAcknowledgementFactory}.php
tests/Feature/Legal/{PolicyTest,PolicyAckTest}.php
```

---

## Test Checklist

- [ ] Tenant isolation: company A cannot see company B policies or acknowledgements
- [ ] Module gating: artifacts hidden when `legal.policies` inactive
- [ ] Publish notifies audience; department audience scoped correctly
- [ ] New version resets acknowledgements; ack unique per version
- [ ] Reminder targets unacknowledged only
- [ ] Body purified
- [ ] Review-due flagged at review_date

---

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Reads | `hr.profiles` employee/department API | hr.profiles | audience resolution + per-employee acks (read-only) |
| Reads | `legal.compliance` control API | legal.compliance | policy ↔ control link (soft) |
| Feeds | acknowledgement status | legal.compliance | evidence that a control-linked policy is acknowledged (read) |

**Data ownership:** `legal.policies` writes only `legal_policies`, `legal_policy_acknowledgements`; employee/department + control data is read-only; reminders dispatched via `core.notifications` ([[../../../security/data-ownership]]).

---

## Related

- [[../compliance-registers/_module|legal.compliance]]
- [[../../hr/employee-profiles/_module|hr.profiles]]
- [[../../../architecture/packages]] (`awcodes/filament-tiptap-editor`)
- [[./decisions]] · [[./unknowns]] · [[./security]] · [[./api]]
