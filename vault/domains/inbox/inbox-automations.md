---
type: module
domain: Omnichannel Inbox
panel: inbox
module-key: inbox.automations
status: planned
color: "#4ADE80"
---

# Inbox Automations

> Rule-based IF/THEN automation for the inbox: auto-assign, auto-label, first-response templates, escalation after inactivity, close idle conversations, and round-robin assignment within teams — all business-hours aware.

**Panel:** `/inbox`
**Module key:** `inbox.automations`

## What It Does

Inbox Automations reduces manual triage work for the shared inbox by firing configurable rules when conversations arrive or reach certain conditions. Rules follow an IF (trigger + conditions) → THEN (actions) structure built in a visual Livewire-based interface. Unlike Support Ticket Automations — which operate on structured SLA-tracked tickets — Inbox Automations is designed for the higher-volume, lower-structure flow of social and messaging conversations. Key use cases include routing conversations from a specific channel to the right team, sending an instant first-response template so customers know their message was received, closing conversations that have been inactive for days, and escalating conversations to the support ticket system when appropriate.

## Features

### Core
- Triggers: `conversation.created`, `conversation.first_message` (first inbound message after agent reply), `conversation.resolved`, `conversation.reopened`, `time.idle` (no activity for N minutes/hours), `time.first_reply_overdue` (no agent reply for N minutes)
- Conditions: channel type (is / is-not), label (has / does-not-have), assigned agent (is / is-not / is-unassigned), team (is / is-not), keyword in message body (contains), contact tier (from CRM), business hours (inside / outside)
- Actions: assign to agent, assign to team, round-robin assign to team, add label, remove label, send reply (from predefined template text, with variable substitution), snooze for N hours, resolve conversation, escalate to support ticket, trigger webhook
- Active/inactive toggle per rule. Run order via drag-and-drop.
- Business hours awareness: any rule can be restricted to fire only inside or outside the configured business hours window

### Advanced
- Round-robin assignment: within a selected team, assigns to the team member with the fewest currently open conversations. Excludes offline agents.
- One-time-per-conversation guard: option to fire the rule only once per conversation (prevents repeat-fire on every new message)
- First-response template: sends a predefined text message immediately on conversation creation so the customer gets an instant response. Variables: `{{contact_first_name}}`, `{{channel_name}}`, `{{business_hours_message}}`
- Automation log per conversation: conversation detail panel in `InboxPage` shows which automations fired on that conversation and when
- Conflict detection: warns if two active automations for the same trigger would assign the same conversation to different agents/teams

### AI-Powered
- Keyword intent routing: instead of simple keyword matching, use AI-powered intent detection as a condition — e.g. "route conversations where the customer's message intent is `billing_question` to the billing team" without listing every possible keyword
- Automation suggestion: AI analyses the last 7 days of manual assignments, labels, and replies and suggests automation rules that would have automated the most repetitive actions

## Data Model

```erDiagram
    inbox_automations {
        ulid id PK
        ulid company_id FK
        string name
        string trigger_event
        json conditions
        json actions
        boolean is_active
        boolean is_one_time_per_conversation
        integer run_order
        integer run_count
        timestamp last_run_at
        timestamps created_at/updated_at
    }

    inbox_automation_logs {
        ulid id PK
        ulid automation_id FK
        ulid conversation_id FK
        timestamp ran_at
        json actions_taken
        boolean success
        string failure_reason
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `trigger_event` | conversation.created / conversation.first_message / time.idle / time.first_reply_overdue / conversation.resolved / conversation.reopened |
| `conditions` | JSON array using same schema as Support Ticket Automations conditions |
| `actions` | JSON array of action objects |
| `is_one_time_per_conversation` | enforced by checking `inbox_automation_logs` for existing record with same (automation_id, conversation_id) |

## Permissions

```
inbox.automations.view
inbox.automations.create
inbox.automations.edit
inbox.automations.delete
inbox.automations.test
```

## Filament

- **Resource:** `InboxAutomationResource` — list view with active/inactive toggle and run count. Ordered list respects run order.
- **Custom pages:** `InboxAutomationBuilderPage` — full-page Livewire builder. Same UX pattern as `AutomationBuilderPage` in the Support domain but with inbox-specific trigger events and action types. Condition row: field selector → operator → value. Action row: action type selector → value input. Plain-English rule preview panel. Class: `App\Filament\Inbox\Pages\InboxAutomationBuilderPage`.
- **Widgets:** `InboxAutomationHealthWidget` on the Inbox panel dashboard: rules fired today, failed runs
- **Nav group:** Automation (inbox panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Respond.io | Automation flows for inbox routing |
| Chatwoot | Automation rules |
| Freshdesk Messaging | Automated assignment and labels |
| Intercom | Workflow automation for conversations |

## Related

- [[shared-inbox]]
- [[inbox-analytics]]
- [[domains/support/ticket-automations]]

## Implementation Notes

- **Shared automation engine:** The `ConditionEvaluator` and `AutomationActionExecutor` services from the Support domain are extended (via interface) to support inbox-specific fields and action types. The core condition evaluation and logging logic is shared; only the entity type (conversation vs ticket) and available fields differ.
- **Event dispatch:** `InboxConversationObserver` fires after `created`, `updated` events. It dispatches `EvaluateInboxAutomations` queued job with the conversation ULID and event type. `time.*` triggers are handled by a `CheckInboxTimeBasedAutomations` scheduled command running every 5 minutes.
- **Round-robin implementation:** `RoundRobinTeamAssigner` service queries team members who are active and have been seen online within the last 15 minutes. Sorts by open conversation count ascending. Assigns to the first in the list. Tie-breaking uses `last_assigned_at` timestamp on the user record.
- **First-response template send:** Template text is stored as a string in the action JSON (not a FK to a canned response — inbox conversations don't have canned responses yet). Variable interpolation uses the same `CannedResponseInterpolator` service with a conversation context adapter.
- **Business hours check:** The `BusinessHoursChecker` service reads the company's default business hours from the SLA domain configuration (or a standalone `company_business_hours` config if SLA domain is not active). Returns true/false for "is it currently business hours?". Used by both inbox and ticket automations.
