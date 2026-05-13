---
type: module
domain: Support & Help Desk
panel: support
module-key: support.automations
status: planned
color: "#4ADE80"
---

# Ticket Automations

> Rule-based IF/THEN automation with a visual condition-action builder, automation execution log, and conflict detection — eliminating repetitive manual triage work.

**Panel:** `/support`
**Module key:** `support.automations`

## What It Does

Ticket Automations lets support managers define rules that run automatically when tickets are created, updated, or when time-based thresholds are met. Each automation has a trigger event, one or more conditions (ANDed together), and one or more actions to execute. The visual builder lets non-technical managers construct rules without writing code — selecting conditions and actions from dropdowns with value pickers. An automation log shows every time a rule fired, what ticket it acted on, and what actions were taken, providing a full audit trail. Conflict detection warns when two active automations would target the same ticket field with different values.

## Features

### Core
- Trigger events: `ticket.created`, `ticket.updated`, `ticket.status_changed`, `ticket.assigned`, `ticket.replied` (customer reply received), `ticket.note_added`, `time.first_response_overdue`, `time.resolution_overdue`, `time.idle` (no activity for N hours)
- Conditions: ticket status, priority, channel, tags (contains/not-contains), subject (contains), assignee (is/is-not), contact tier (from CRM), ticket age (> N hours), SLA state (breached/approaching)
- Actions: assign to agent, assign to team, round-robin assign, set priority, add tag, remove tag, send reply (from canned response), send notification to agent/manager, close ticket, escalate (set priority to urgent + notify), trigger webhook (POST to external URL)
- Multiple conditions per automation (AND logic); multiple actions per automation (all execute sequentially)
- Active/inactive toggle per automation without deleting it
- Run order: automations run in defined order (drag-and-drop sort). Lower order runs first.

### Advanced
- Automation log: `support_automation_logs` table records every execution with timestamp, ticket ID, automation ID, and exact actions taken JSON. Viewable per automation or per ticket.
- Conflict detection: on save, the system checks all active automations for the same trigger + overlapping condition range and warns if two automations would both set the same field to different values on the same ticket
- One-time vs recurring: automations can be configured to fire once per ticket (skip if already fired on that ticket) or every time the trigger condition is met
- Simulation mode: test an automation against the last 50 tickets to see which ones would have triggered, before activating
- Business hours condition: automations can be constrained to fire only inside or outside business hours (references the default SLA business hours calendar)

### AI-Powered
- Automation suggestion: AI analyses the last 30 days of ticket tags, assignments, and status changes and suggests automation rules that would have saved the most manual actions
- Natural language rule builder: agent describes the rule in plain English ("when a ticket about billing comes in, assign to the finance-support team and set priority to high") and Claude generates the conditions and actions JSON

## Data Model

```erDiagram
    support_automations {
        ulid id PK
        ulid company_id FK
        string name
        string trigger_event
        json conditions
        json actions
        boolean is_active
        boolean is_one_time_per_ticket
        integer run_order
        integer run_count
        timestamp last_run_at
        timestamps created_at/updated_at
    }

    support_automation_logs {
        ulid id PK
        ulid automation_id FK
        ulid ticket_id FK
        timestamp ran_at
        json conditions_evaluated
        json actions_taken
        boolean success
        string failure_reason
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `trigger_event` | Enum: ticket.created / ticket.updated / ticket.status_changed / ticket.replied / time.idle / time.first_response_overdue / time.resolution_overdue |
| `conditions` | JSON array: `[{ "field": "priority", "operator": "equals", "value": "urgent" }]` |
| `actions` | JSON array: `[{ "type": "assign", "value": "agent-ulid" }, { "type": "set_tag", "value": "billing" }]` |
| `is_one_time_per_ticket` | if true, a `support_automation_ticket_runs` pivot prevents re-firing on the same ticket |
| `run_count` | total lifetime executions of this automation — display only |
| `failure_reason` | populated if an action throws an exception (e.g. webhook timeout) |

## Permissions

```
support.automations.view
support.automations.create
support.automations.edit
support.automations.delete
support.automations.test
```

## Filament

- **Resource:** `TicketAutomationResource` — standard list view with active/inactive toggle and run count column.
- **Pages:** `ListTicketAutomations`, `CreateTicketAutomation`, `EditTicketAutomation`
- **Custom pages:** `AutomationBuilderPage` — replaces standard create/edit form. Livewire component with dynamic repeater rows for conditions (field selector → operator dropdown → value picker auto-generated based on field type) and actions (action type dropdown → value input). Drag-and-drop sort for run order. Preview panel shows a plain-English summary of the rule being built.
- **Widgets:** `AutomationHealthWidget` — shows count of automations fired today and any failed runs — on the Support panel dashboard
- **Nav group:** Knowledge (support panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Zendesk | Triggers and automations |
| Freshdesk | Automation rules, time-based triggers |
| Helpscout | Workflows |
| Intercom | Automated conversation rules |

## Related

- [[support-tickets]]
- [[sla-management]]
- [[canned-responses]]
- [[support-analytics]]

## Implementation Notes

- **Event-driven execution:** All event-based automations (`ticket.*`) are triggered by Laravel model observers on `SupportTicket`. The observer dispatches a queued `EvaluateTicketAutomations` job per update, passing the ticket ULID and trigger event name. The job loads all active automations for the company with that trigger, evaluates each condition set against the current ticket state, and executes matched actions via an `AutomationActionExecutor` service.
- **Time-based triggers:** A `CheckTimeBasedAutomations` scheduled command runs every 5 minutes. It queries tickets that have been idle for longer than the configured threshold and dispatches `EvaluateTicketAutomations` jobs with the `time.idle` trigger. Similarly handles `time.first_response_overdue` and `time.resolution_overdue` by cross-referencing `support_ticket_sla_trackers`.
- **Condition evaluation:** The `ConditionEvaluator` class maps field names to ticket attribute accessors and applies the operator (equals, not_equals, contains, not_contains, greater_than, less_than). All conditions must pass (AND logic). Short-circuits on first failure for performance.
- **Action executor:** The `AutomationActionExecutor` class maps action types to handler methods. Webhook action uses `Http::post()` with a 5-second timeout and retries once. All action results (including webhook response code) are serialised into `actions_taken` JSON in the log record.
- **Conflict detection:** Implemented as a `AutomationConflictDetector` service called on save. Compares the new automation's conditions against all active automations with the same trigger. Conflict = two automations with overlapping conditions that both perform a `set_field` action on the same field. Warns via Filament notification — does not block save.
