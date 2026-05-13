---
type: module
domain: Customer Success
panel: cs
module-key: cs.playbooks
status: planned
color: "#4ADE80"
---

# Playbooks

> Success playbooks with trigger conditions, ordered task sequences, and automatic CSM assignment to drive consistent customer outcomes.

**Panel:** `cs`
**Module key:** `cs.playbooks`

---

## What It Does

Playbooks define the standardised CS processes the team runs in response to specific customer events. A playbook is created for situations like "customer health score drops below 50", "30 days since no product login", or "contract renewal approaching 90 days". When the trigger condition is met, the playbook automatically creates a set of tasks assigned to the responsible CSM — schedule a call, send a resource, review usage data, escalate to account executive. This ensures consistent, timely action across the entire customer portfolio regardless of CSM experience level.

---

## Features

### Core
- Playbook creation: name, description, trigger type (health score drop, manual, scheduled, event-based)
- Trigger conditions: define the exact threshold or event that fires the playbook
- Task sequence: ordered tasks with assignee, due date offset from trigger, and instructions
- Auto-activation: playbook fires automatically when trigger conditions are met
- CSM assignment: tasks routed to the account's primary CSM or a configurable team member
- Active playbook view: all currently running playbook instances with progress and due tasks

### Advanced
- Branching tasks: conditional next steps based on the outcome of a previous task
- Escalation rules: auto-escalate to a team lead if a task is overdue by X days
- Multi-assignee tasks: tasks that require collaboration between CSM and AE or support
- Playbook templates: pre-built templates for common scenarios (renewal, onboarding, at-risk)
- Outcome tracking: record the result of each completed playbook (churned, retained, expanded)

### AI-Powered
- Trigger recommendation: AI suggests additional trigger conditions based on patterns in historical churn data
- Task effectiveness analysis: identify which playbook tasks are most correlated with positive outcomes
- Optimal timing suggestion: recommend the best trigger threshold based on account segment and historical outcomes

---

## Data Model

```erDiagram
    playbooks {
        ulid id PK
        ulid company_id FK
        string name
        text description
        string trigger_type
        json trigger_conditions
        json task_templates
        boolean is_active
        timestamps created_at_updated_at
    }

    playbook_instances {
        ulid id PK
        ulid playbook_id FK
        ulid account_id FK
        ulid company_id FK
        string status
        string outcome
        timestamp triggered_at
        timestamp completed_at
    }

    playbook_tasks {
        ulid id PK
        ulid instance_id FK
        ulid assigned_to FK
        string title
        text instructions
        date due_date
        string status
        timestamp completed_at
    }

    playbooks ||--o{ playbook_instances : "runs as"
    playbook_instances ||--o{ playbook_tasks : "generates"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `playbooks` | Playbook definitions | `id`, `company_id`, `name`, `trigger_type`, `trigger_conditions`, `task_templates` |
| `playbook_instances` | Active runs | `id`, `playbook_id`, `account_id`, `status`, `outcome`, `triggered_at` |
| `playbook_tasks` | Generated tasks | `id`, `instance_id`, `assigned_to`, `title`, `due_date`, `status` |

---

## Permissions

```
cs.playbooks.view
cs.playbooks.create
cs.playbooks.update
cs.playbooks.delete
cs.playbooks.manage-instances
```

---

## Filament

- **Resource:** `App\Filament\Cs\Resources\PlaybookResource`
- **Pages:** `ListPlaybooks`, `CreatePlaybook`, `EditPlaybook`, `ViewPlaybook`
- **Custom pages:** `ActivePlaybookInstancesPage`, `PlaybookOutcomesPage`
- **Widgets:** `ActivePlaybooksWidget`, `OverdueTasksWidget`
- **Nav group:** Playbooks

---

## Displaces

| Feature | FlowFlex | Gainsight | ChurnZero | Totango |
|---|---|---|---|---|
| Triggered task sequences | Yes | Yes | Yes | Yes |
| Conditional branching | Yes | Yes | No | No |
| Outcome tracking | Yes | Yes | Yes | Yes |
| AI trigger recommendations | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Implementation Notes

**Trigger evaluation:** Playbook triggers are evaluated by `PlaybookTriggerEvaluatorJob` scheduled every 15 minutes. It loads all `playbooks` where `is_active = true` and evaluates each `trigger_conditions` JSON against current account data. If a trigger fires and no active instance exists for that account+playbook pair, it creates a `playbook_instances` record and fires `PlaybookTriggered` event which dispatches `GeneratePlaybookTasksJob`.

**`trigger_conditions` JSON schema:** The schema must be documented to be buildable. Example: `{field: "health_score", operator: "less_than", value: 50}` or `{event: "no_product_login", days: 30}`. Multiple conditions can be ANDed: `{operator: "and", conditions: [...]}`. The evaluator must interpret this schema — define the full schema spec before building the evaluator.

**`task_templates` JSON schema:** Each entry: `{title: string, instructions: string, assignee_type: "account_csm"|"user_id", assignee_id: nullable, due_offset_days: int, depends_on_step: nullable int}`. The `GeneratePlaybookTasksJob` iterates this array and creates `playbook_tasks` records with `due_date = trigger_date + due_offset_days`.

**Relationship to `ai.workflow-builder`:** Playbooks and the AI workflow builder (also a trigger→condition→action engine) are architecturally similar. Consider whether playbooks should be built as a specialised view on top of the workflow builder engine rather than a separate engine. Recommendation: in the current phase, build them separately (simpler, more targeted for CS use case). Document as a future consolidation opportunity.

**`ActivePlaybookInstancesPage`:** A custom Filament `Page` — shows all active instances in a card grid, each card showing: account name, playbook name, next task due, progress indicator (completed tasks / total tasks). Not a standard Resource list. The cards link to the full instance view.

**Escalation rules:** `EscalateOverduePlaybookTasksJob` runs daily. Queries `playbook_tasks` where `due_date < today`, `status = pending`, `instance.playbook.escalation_days` days overdue. Creates a notification to the team lead (configurable on the playbook). If `escalation_days` is not in the current data model, add it to `playbooks` as `integer escalation_after_days nullable`.

**AI features:** Trigger recommendation calls `app/Services/AI/PlaybookInsightService.php` with historical churn data (accounts that churned with their pre-churn attribute values) — OpenAI GPT-4o suggests additional trigger conditions to catch at-risk patterns earlier. Task effectiveness analysis is a PHP aggregate: for each task type, count completion rate × outcome rate correlation. No LLM needed.

## Related

- [[health-scores]] — health score drops trigger playbooks
- [[churn-risk]] — at-risk accounts trigger churn playbooks
- [[success-plans]] — playbook tasks reference success plan milestones
- [[onboarding-tracking]] — onboarding stall triggers a playbook
