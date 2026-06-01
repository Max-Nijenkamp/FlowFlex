---
type: module
domain: Legal & Compliance
panel: legal
module-key: legal.policies
status: planned
color: "#4ADE80"
---

# Policy Library

Company policies (privacy, security, HR, code of conduct) with versioning, acknowledgement tracking, and publication to employees.

## Core Features

- Policy record: title, category, body (rich text), version, effective date, status
- Versioning: track policy revisions over time
- Publication: publish to all employees or specific groups
- Acknowledgement tracking: employees confirm they've read the policy
- Acknowledgement report: who has/hasn't acknowledged, with reminders
- Review cycle: flag policies due for periodic review
- Rich text editing (Tiptap)
- Linked to compliance controls

## Data Model

| Table | Key Columns |
|---|---|
| `legal_policies` | company_id, title, category, body, version, effective_date, review_date, status, author_id |
| `legal_policy_acknowledgements` | policy_id, company_id, employee_id, acknowledged_at |

## Filament

**Nav group:** Compliance

- `PolicyResource` — create, edit (Tiptap), publish, version
- `PolicyAcknowledgementPage` (custom page) — acknowledgement status matrix
- Employee self-service: read + acknowledge policies

## Cross-Domain

- Acknowledgement reminders via Core Notifications
- Linked to compliance registers

## Related

- [[domains/legal/compliance-registers]]
- [[domains/hr/employee-profiles]]
- `awcodes/filament-tiptap-editor`
