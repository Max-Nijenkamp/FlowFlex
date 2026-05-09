---
type: module
domain: IT & Security
panel: it
phase: 3
status: planned
cssclasses: domain-it
migration_range: 604000–604499
last_updated: 2026-05-09
---

# Change Management (ITIL)

ITIL-aligned IT change management. Controlled process for all changes to IT infrastructure, systems, and applications. Prevents uncontrolled changes causing outages.

---

## Change Types

| Type | Description | Approval |
|---|---|---|
| Standard | Pre-approved, low-risk, routine | Auto-approve |
| Normal | Requires CAB review | CAB approval |
| Emergency | Critical fix, urgent | Emergency CAB |

---

## Change Record

Every change tracked:
- Change title, description, category (infrastructure/application/security)
- Justification: why is this change needed?
- Risk assessment: impact and probability (Low/Medium/High/Critical)
- Implementation plan: step-by-step
- Backout plan: how to reverse if it fails
- Test plan: how to verify success
- Change window: when will it be executed (minimise user impact)
- Affected systems and services
- Linked to incident (if change is a fix for a problem)

---

## CAB (Change Advisory Board)

For Normal changes:
1. Change record submitted
2. Auto-scheduled for next CAB meeting (weekly or ad-hoc)
3. CAB reviews: technical team + business owners + security
4. Approved / rejected / deferred
5. Scheduled for implementation

Emergency CAB: fast-track approval via async voting (email/Slack) within 2 hours.

---

## Implementation

On change window:
- Implementer updates status to "In Progress"
- Post-implementation review: did it work? Any incidents caused?
- Status: Successful / Partially successful / Failed (execute backout)

---

## Change Calendar

Visual calendar of all approved changes:
- See upcoming change windows
- Conflict detection: two changes to same system in same window
- Blackout periods: no changes during peak business (month-end, product launch)

---

## Data Model

### `it_changes`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| change_type | enum | standard/normal/emergency |
| title | varchar(300) | |
| status | enum | draft/submitted/cab_review/approved/scheduled/in_progress/completed/failed |
| risk | enum | low/medium/high/critical |
| scheduled_start | timestamp | nullable |
| scheduled_end | timestamp | nullable |
| submitted_by | ulid | FK |
| approved_by | ulid | nullable FK |
| outcome | enum | successful/partial/failed/cancelled |

---

## Migration

```
604000_create_it_changes_table
604001_create_it_cab_reviews_table
604002_create_it_change_blackouts_table
```

---

## Related

- [[MOC_IT]]
- [[itsm-helpdesk]]
- [[service-catalog-it]]
