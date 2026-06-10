---
type: module
domain: Legal & Compliance
domain-key: legal
panel: legal
module-key: legal.policies
status: planned
priority: p3
depends-on: [hr.profiles, core.billing, core.rbac, core.notifications]
soft-depends: [legal.compliance]
fires-events: []
consumes-events: []
patterns: [custom-pages]
tables: [legal_policies, legal_policy_acknowledgements]
permission-prefix: legal.policies
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Policy Library

Company policies (privacy, security, HR, code of conduct) with versioning, acknowledgement tracking, and publication to employees.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/hr/employee-profiles\|hr.profiles]] | acknowledgements per employee |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] | gating, permissions, reminders |
| Soft | [[domains/legal/compliance-registers\|legal.compliance]] | policy ↔ control links |

---

## Core Features

- Policy record: title, category, body (rich text, purified), version, effective date, status (draft/published/archived)
- Versioning: new version on publish of changed body; **new version resets acknowledgements** *(assumed)*
- Publication: publish to all employees or specific departments
- Acknowledgement tracking: employees confirm they've read the policy (per version)
- Acknowledgement report: who has/hasn't acknowledged, with reminders (weekly until done *(assumed)*)
- Review cycle: review_date flags policies due for periodic review
- Linked to compliance controls

---

## Data Model

### legal_policies

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| title / category | string | |
| body | text | purified |
| version | int default 1 | |
| effective_date / review_date | date | |
| status | string default `draft` | draft / published / archived |
| audience | jsonb nullable | department ids; null = all |
| author_id | ulid FK users | |
| deleted_at | timestamp nullable | |

### legal_policy_acknowledgements — id, policy_id FK, company_id, employee_id FK, version, acknowledged_at; unique `(policy_id, employee_id, version)`

---

## DTOs

### CreatePolicyData — title, category, body (purified), effective_date, review_date?, audience?
### AcknowledgeData — policy_id (published, in audience) — actor's own employee record

## Services & Actions

- `PolicyService::publish(...)` — version bump on body change, resets acks, notifies audience
- `AcknowledgePolicyAction` — own only
- `PolicyAckReminderCommand` / review-due flagging

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `PolicyAckReminderCommand` | notifications | weekly Mon | only unacknowledged audience; natural re-remind |

---

## Filament

**Nav group:** Compliance

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `PolicyResource` | #1 CRUD resource | Tiptap, publish/version actions |
| `PolicyAcknowledgementPage` | #9 matrix custom page | employees × policies status, export |
| `MyPoliciesPage` | self-service custom page | read + acknowledge |

---

## Permissions

`legal.policies.view-any` · `legal.policies.create` · `legal.policies.publish` · `legal.policies.acknowledge-own` (all employees)

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Publish notifies audience; department audience scoped correctly
- [ ] New version resets acknowledgements; ack unique per version
- [ ] Reminder targets unacknowledged only
- [ ] Body purified
- [ ] Review-due flagged at review_date

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

## Related

- [[domains/legal/compliance-registers]]
- [[domains/hr/employee-profiles]]
- [[architecture/packages]] (`awcodes/filament-tiptap-editor`)
