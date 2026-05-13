---
type: module
domain: Support & Help Desk
panel: support
module-key: support.sla
status: planned
color: "#4ADE80"
---

# SLA Management

> SLA policy builder for first response and resolution times by priority and business hours, with breach alerts, SLA pause on pending status, and per-policy performance reporting.

**Panel:** `/support`
**Module key:** `support.sla`

## What It Does

SLA Management lets support managers define service level agreements — time-bound commitments for how quickly agents must respond to and resolve tickets. Policies are configured per priority level and can be restricted to business hours (e.g. 09:00–18:00 Mon–Fri), so SLA timers pause overnight and on weekends. Multiple SLA policies allow different response targets for different customer tiers (Enterprise vs Standard). When a ticket is approaching or has breached its SLA target, the assigned agent and their manager are alerted by email and in-app notification. The SLA Management module also pauses timers when a ticket is in `pending` status (waiting for the customer), so agents are not penalised for customer delays.

## Features

### Core
- SLA policy builder: name, target priority (urgent/high/normal/low), first response time (hours), resolution time (hours), business hours only toggle
- Business hours configuration per policy: define working days and hours (e.g. Mon–Fri, 09:00–18:00) and timezone
- Multiple active SLA policies per company — assign policies to tickets manually or by automation rule
- SLA timer display on `TicketDetailPage`: colour-coded countdown (green → amber → red → breached)
- SLA pause: timer pauses automatically when ticket status changes to `pending`, resumes on `open`
- Breach events: `SlaBreachEvent` fired when first response or resolution time expires — triggers notification and creates `support_sla_breaches` record

### Advanced
- SLA calendar exceptions: define public holidays as non-working days per policy so timers skip them
- SLA assignment rules: automatically assign SLA policy based on customer tier (from CRM contact tags) or ticket channel or tag
- Breach escalation: on breach, optionally auto-reassign ticket to senior agent or notify support manager via separate notification rule
- SLA retroactive view: on any closed ticket, show whether SLA was met, breached, or paused — and for how long each state lasted

### AI-Powered
- SLA breach risk prediction: AI analyses ticket content, assignee workload, and historical resolution times to flag tickets likely to breach before the timer expires, enabling proactive intervention

## Data Model

```erDiagram
    support_sla_policies {
        ulid id PK
        ulid company_id FK
        string name
        string priority
        integer first_response_hours
        integer resolution_hours
        boolean business_hours_only
        json business_hours
        json holiday_dates
        boolean is_default
        boolean is_active
        timestamps created_at/updated_at
    }

    support_ticket_sla_trackers {
        ulid id PK
        ulid ticket_id FK
        ulid policy_id FK
        timestamp first_response_due_at
        timestamp resolution_due_at
        timestamp first_response_met_at
        timestamp resolution_met_at
        integer pause_duration_seconds
        timestamp last_paused_at
        boolean first_response_breached
        boolean resolution_breached
        timestamps created_at/updated_at
    }

    support_sla_breaches {
        ulid id PK
        ulid ticket_id FK
        ulid policy_id FK
        string breach_type
        timestamp breached_at
        ulid assignee_at_breach FK
        timestamps created_at/updated_at
    }
```

| Column | Notes |
|---|---|
| `business_hours` | JSON object: `{ "mon": ["09:00","18:00"], "tue": [...], ... }` with timezone key |
| `holiday_dates` | JSON array of ISO date strings for non-working days |
| `breach_type` | first_response / resolution |
| `pause_duration_seconds` | total accumulated seconds the SLA timer has been paused (status = pending) |
| `last_paused_at` | timestamp when the timer was most recently paused — used to calculate elapsed pause time on resume |
| `is_default` | if true, this policy is applied to new tickets when no other policy is matched by automation |

## Permissions

```
support.sla.view
support.sla.create
support.sla.edit
support.sla.delete
support.sla.reports
```

## Filament

- **Resource:** `SlaPolicyResource` — standard CRUD for defining SLA policies. Form includes business hours builder (Livewire repeater per day with time pickers) and holiday date multi-select.
- **Pages:** `ListSlaPolicies`, `CreateSlaPolicy`, `EditSlaPolicy`
- **Custom pages:** None
- **Widgets:** `SlaBreachWidget` — custom widget on the Support panel dashboard showing tickets currently in breach or approaching breach (< 15 minutes remaining). `SlaPerformanceWidget` — donut chart showing met vs breached rate for the current week.
- **Nav group:** Settings (support panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Zendesk | SLA policies, breach alerts |
| Freshdesk | SLA management, business hours |
| Helpscout | Response time targets |
| Zoho Desk | SLA rules engine |

## Related

- [[support-tickets]]
- [[ticket-automations]]
- [[support-analytics]]

## Implementation Notes

- **SLA timer calculation:** A `SlaTimerService` class handles all timer arithmetic. When a ticket is created and a policy is matched, the service calculates `first_response_due_at` and `resolution_due_at` by starting from `created_at` and counting forward only through business hours windows (excluding weekends and holidays). The service is used both on ticket creation and on status change.
- **Scheduled checks:** A `CheckSlaBreach` command runs every minute via `schedule()->everyMinute()`. It queries `support_ticket_sla_trackers` for trackers where `first_response_due_at <= now()` and `first_response_met_at IS NULL` (and similarly for resolution), and fires `SlaBreachEvent` for each. The command is idempotent — already-recorded breaches are skipped via the `breached_at` check.
- **Pause logic:** `SupportTicketObserver::updating()` detects status transitions to/from `pending`. On pause: records `last_paused_at`. On resume: adds `(now - last_paused_at)` seconds to `pause_duration_seconds` and shifts both due-at timestamps forward by the same amount.
- **Policy matching:** On ticket create, a `SlaPolicyMatcher` service evaluates automation-defined assignment rules (checked first), then falls back to the `is_default` policy. If no default is set, SLA tracking is skipped and `policy_id` is null.
