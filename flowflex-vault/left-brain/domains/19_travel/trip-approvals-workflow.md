---
type: module
domain: Business Travel
panel: travel
cssclasses: domain-travel
phase: 7
status: complete
migration_range: 916000–917999
last_updated: 2026-05-12
---

# Trip Approvals & Workflow

Multi-level approval routing for travel requests. Deadline-based auto-approval, budget checks, and full approval audit trail.

---

## Approval Triggers

| Scenario | Approval Required |
|---|---|
| Trip cost < auto-approve threshold | None — auto-approved |
| Trip cost ≥ threshold | Line manager |
| International trip to high-risk country | Manager + HR/Security |
| Out-of-policy booking selection | Manager + justification |
| Business class request by standard employee | Manager + director |
| Trip during freeze period | Manager + HR |

Approval levels configurable per policy tier.

---

## Approval Request

When trip requires approval:
1. Travel request created in `travel_approval_requests`
2. First approver notified by email + in-app notification
3. Approver sees: destination, dates, itinerary summary, total cost, out-of-policy items (highlighted), justification
4. Actions: Approve / Request Changes / Decline (with reason)
5. If multi-level: next approver notified on previous approval

---

## Deadline-Based Auto-Approval

Prevents trips from being blocked by unresponsive approvers:

- **Auto-escalate**: if no response within N hours → notify backup approver
- **Auto-approve**: if no response within M hours from backup → auto-approved with flag logged
  - Only for within-policy trips (out-of-policy trips cannot auto-approve)
  - Alert sent to both approvers that auto-approval fired

Default timers: 24h escalate, 48h auto-approve (configurable per policy).

---

## Budget Check

At approval time, system checks:
- Department travel budget remaining (from Finance budget module if connected)
- Employee's personal travel allowance remaining (if annual travel budget per employee)
- Flag: "This trip will exhaust 90% of Q3 travel budget for Marketing"

Approver can override with justification, or decline and route to finance review.

---

## Approval Audit Trail

Every approval action logged:
- Who acted (or "System" for auto-approve)
- Timestamp
- Action taken (approved/declined/escalated/auto-approved)
- Justification (if provided)

Immutable — cannot be edited after the fact. Feeds into travel analytics and compliance reports.

---

## Data Model

### `travel_approval_requests`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| tenant_id | ulid | |
| trip_id | ulid | FK |
| requester_id | ulid | FK `employees` |
| current_approver_id | ulid | FK `employees` |
| approval_level | int | 1, 2, 3 |
| status | enum | pending/approved/declined/auto_approved/escalated |
| auto_approve_at | datetime | deadline for auto-approve |
| decided_at | timestamp | nullable |
| decided_by | ulid | nullable FK |
| justification | text | nullable |
| out_of_policy_items | json | array of policy violations |
| estimated_cost | decimal(12,2) | |

### `travel_approval_actions`
| Column | Type | Notes |
|---|---|---|
| id | ulid | |
| approval_request_id | ulid | FK |
| actor_id | ulid | nullable FK (null = system) |
| action | enum | approved/declined/escalated/auto_approved/changes_requested |
| note | text | nullable |
| acted_at | timestamp | |

---

## Migration

```
916000_create_travel_approval_requests_table
916001_create_travel_approval_actions_table
```

---

## Related

- [[MOC_Travel]]
- [[travel-booking-portal]]
- [[travel-policy-engine]]
- [[MOC_Finance]] — budget check integration
- [[MOC_HR]] — manager hierarchy for approval routing
