---
type: module
domain: IT & Security
panel: it
module-key: it.changes
status: planned
color: "#4ADE80"
---

# Change Management

> Submit, review, approve, and schedule IT changes with a risk-assessed workflow and a change calendar to prevent conflicting deployments.

**Panel:** `it`
**Module key:** `it.changes`

## What It Does

Change Management enforces a structured review-and-approval process before IT changes are applied to production systems. A change can be routine (standard), significant (normal), or urgent (emergency). Each change request captures the proposed change, the reason, rollback plan, risk assessment, and implementation steps. An advisory board or approver reviews and authorises before implementation. A change calendar shows all planned changes so teams can avoid scheduling conflicts during critical business periods.

## Features

### Core
- Change request: title, type (standard/normal/emergency), affected system/service, description, risk level (low/medium/high), implementation steps, rollback plan
- Change types:
  - Standard: pre-approved low-risk changes (e.g., password reset procedure) — no approval needed
  - Normal: requires CAB or approver sign-off before implementation window
  - Emergency: expedited approval for urgent production fixes; retrospective review required
- Approval workflow: configurable approvers per change type; CAB (change advisory board) review for normal changes
- Change calendar: month view of all approved change windows; colour-coded by system or team
- Implementation confirmation: change implementer marks the change as implemented and records the outcome (successful, partially successful, rolled back)
- Change closure: link post-implementation review notes; close or escalate to incident if change caused issues

### Advanced
- Conflict detection: warn if a proposed change overlaps with another change scheduled for the same system in the same window
- Blackout periods: configure periods when no changes are allowed (e.g., month-end, peak trading season)
- Risk scoring matrix: system calculates composite risk score from impact, likelihood, and reversibility inputs
- Change templates: pre-defined templates for common change types (OS patching, firewall rule change, DNS update) with pre-populated steps
- Retrospective enforcement: emergency changes automatically create a retrospective task with due date
- CAB meeting agenda: auto-generate agenda of changes pending CAB review for the upcoming meeting

### AI-Powered
- Risk assessment assist: analyse change description and suggest appropriate risk level and rollback recommendations
- Similar change search: surface past approved changes to the same system to reuse their implementation steps

## Data Model

```erDiagram
    it_change_requests {
        ulid id PK
        ulid company_id FK
        string change_number
        string title
        string type
        string affected_system
        string risk_level
        text description
        text implementation_steps
        text rollback_plan
        string status
        timestamp proposed_start
        timestamp proposed_end
        ulid requested_by FK
        ulid approved_by FK
        timestamp approved_at
        timestamp implemented_at
        string outcome
        timestamps timestamps
    }

    it_change_approvals {
        ulid id PK
        ulid change_id FK
        ulid approver_id FK
        string decision
        text notes
        timestamp decided_at
    }

    it_change_requests ||--o{ it_change_approvals : "reviewed by"
```

| Table | Purpose |
|---|---|
| `it_change_requests` | Change record with risk, steps, and outcome |
| `it_change_approvals` | Per-approver decision records |

## Permissions

```
it.changes.view-any
it.changes.create
it.changes.approve
it.changes.implement
it.changes.manage-blackouts
```

## Filament

**Resource class:** `ChangeRequestResource`
**Pages:** List, Create, Edit, View
**Custom pages:** `ChangeCalendarPage` (month view of all scheduled changes), `CabAgendaPage` (pending CAB items)
**Widgets:** `PendingApprovalWidget` (changes awaiting the current user's approval)
**Nav group:** Access

## Displaces

| Competitor | Feature Replaced |
|---|---|
| ServiceNow Change Management | ITIL change control workflow |
| Freshservice Change Management | CAB and change calendar |
| Jira Service Management Changes | Change advisory and scheduling |
| BMC Remedy (SMB) | Change request and approval workflow |

## Related

- [[incident-management]] — emergency changes raised from major incidents
- [[service-desk]] — normal changes may originate from service desk requests
- [[it-analytics]] — change volume and success rate tracked as IT KPIs
- [[audit-compliance]] — change records are evidence in IT audits
