---
type: module
domain: Whistleblowing & Ethics
panel: ethics
module-key: ethics.policies
status: planned
color: "#4ADE80"
---

# Policy Acknowledgments

> Policy document acknowledgment tracking — publish a policy, assign to employees, track sign-off, enforce deadlines.

**Panel:** `ethics`
**Module key:** `ethics.policies`

---

## What It Does

Policy Acknowledgments ensures that every employee has formally acknowledged reading and understanding the company's key policies — Code of Conduct, Anti-Bribery Policy, Data Protection Policy, Whistleblowing Policy, and others. Ethics or compliance managers publish a policy document, assign it to a target audience (all employees, specific departments, or new hires), set a sign-off deadline, and the system tracks completion. Employees receive an in-app prompt and email asking them to read and sign off. Overdue sign-offs escalate to the employee's manager.

---

## Features

### Core
- Policy document creation: title, version, effective date, and document content or link to DMS document
- Audience assignment: assign to all employees, specific departments, specific roles, or individual employees
- Sign-off deadline: date by which all assigned employees must acknowledge
- Employee acknowledgment flow: employee reviews the policy and clicks to confirm they have read and understood it
- Completion dashboard: percentage complete per policy with employee-level status
- Reminder notifications: automated reminders at 7 days, 3 days, and 1 day before the deadline

### Advanced
- Escalation: notify the line manager when a direct report has not acknowledged by the deadline
- Quiz gating: require employees to pass a short quiz on the policy content before acknowledgment is accepted
- Multi-language policies: publish the same policy in multiple languages; employee sees their preferred language
- Acknowledgment certificate: generate a PDF certificate for employees with compliance audit needs
- Historical sign-off record: retain all past acknowledgments even after a policy is superseded

### AI-Powered
- Policy plain-language summary: AI generates a plain-language summary of the policy for employees to read before the full document
- Completion risk prediction: flag employees unlikely to acknowledge before the deadline based on past behaviour
- Policy change detection: when a new policy version is uploaded, AI highlights what changed from the prior version

---

## Data Model

```erDiagram
    ethics_policies {
        ulid id PK
        ulid company_id FK
        string title
        integer version
        date effective_date
        string document_url
        json audience_config
        date acknowledgment_deadline
        boolean is_active
        timestamps created_at_updated_at
    }

    policy_acknowledgments {
        ulid id PK
        ulid policy_id FK
        ulid employee_id FK
        ulid company_id FK
        boolean acknowledged
        timestamp acknowledged_at
        string quiz_pass
        timestamps created_at_updated_at
    }

    ethics_policies ||--o{ policy_acknowledgments : "tracked via"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `ethics_policies` | Policy records | `id`, `company_id`, `title`, `version`, `effective_date`, `acknowledgment_deadline`, `is_active` |
| `policy_acknowledgments` | Sign-off records | `id`, `policy_id`, `employee_id`, `acknowledged`, `acknowledged_at` |

---

## Permissions

```
ethics.policies.view-all
ethics.policies.create
ethics.policies.publish
ethics.policies.view-completion
ethics.policies.export
```

---

## Filament

- **Resource:** `App\Filament\Ethics\Resources\EthicsPolicyResource`
- **Pages:** `ListEthicsPolicies`, `CreateEthicsPolicy`, `EditEthicsPolicy`, `ViewEthicsPolicy`
- **Custom pages:** `PolicyCompletionDashboardPage`, `EmployeeAcknowledgmentPage`
- **Widgets:** `PolicyCompletionWidget`, `OverdueAcknowledgmentsWidget`
- **Nav group:** Policies

---

## Displaces

| Feature | FlowFlex | NAVEX | EthicsPoint | Donesafe |
|---|---|---|---|---|
| Policy sign-off tracking | Yes | Yes | Yes | Yes |
| Deadline and escalation | Yes | Yes | Yes | Yes |
| Quiz gating | Yes | Yes | Partial | Yes |
| AI plain-language summary | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[dms/document-library]] — policy documents stored in DMS
- [[reporting-analytics]] — policy acknowledgment rates in ethics metrics
- [[lms/compliance-training]] — policies can be linked to compliance training courses
