---
type: module
domain: Business Travel
panel: travel
module-key: travel.requests
status: planned
color: "#4ADE80"
---

# Travel Requests

> Employee travel request workflow — submit trip details, manager approval, and handoff to booking.

**Panel:** `travel`
**Module key:** `travel.requests`

---

## What It Does

Travel Requests is the entry point for all corporate travel. Employees submit a trip request with destination, travel dates, purpose, estimated cost, and preferred transport. The request is routed to the employee's line manager for approval, with policy checks run automatically (advance booking window, estimated cost vs per-diem limits). Once approved, the request is passed to the travel coordinator or the employee to arrange bookings, and the approval record provides the audit trail for expense processing.

---

## Features

### Core
- Request form: destination, travel dates, purpose, transport type (air, rail, car), estimated cost
- Policy auto-check: system validates the request against active travel policies and flags violations
- Approval routing: automatic routing to the employee's line manager for approval or rejection
- Rejection with reason: manager can reject with a written reason; employee notified and can resubmit
- Approval notification: approved employee notified; request status updated to ready-to-book
- Request history: employee view of all past and pending travel requests

### Advanced
- Multi-leg trips: request trips with multiple destinations and date ranges in one submission
- Alternative approver: configurable backup approver when the primary approver is unavailable
- Approval delegation: managers can delegate their approval authority during absence
- Cost centre selection: employee selects the cost centre for budget allocation at submission
- Emergency travel: bypass approval for urgent trips with post-travel ratification workflow

### AI-Powered
- Cost estimation: AI estimates total trip cost from destination and duration using historical travel data
- Policy violation explanation: plain-language explanation of which policy rule is being violated
- Trip duplicate detection: flag if a similar trip to the same destination has been requested recently

---

## Data Model

```erDiagram
    travel_requests {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        ulid approver_id FK
        string destination
        date depart_date
        date return_date
        string purpose
        string transport_type
        decimal estimated_cost
        string currency
        ulid cost_centre_id FK
        string status
        text rejection_reason
        timestamp approved_at
        timestamps created_at_updated_at
    }

    travel_policy_checks {
        ulid id PK
        ulid request_id FK
        string policy_rule
        boolean passed
        text violation_message
        timestamps created_at_updated_at
    }

    travel_requests ||--o{ travel_policy_checks : "validated by"
```

| Table | Purpose | Key Columns |
|---|---|---|
| `travel_requests` | Trip requests | `id`, `company_id`, `employee_id`, `approver_id`, `destination`, `depart_date`, `status` |
| `travel_policy_checks` | Policy validation results | `id`, `request_id`, `policy_rule`, `passed`, `violation_message` |

---

## Permissions

```
travel.requests.submit-own
travel.requests.view-own
travel.requests.approve
travel.requests.view-all
travel.requests.admin
```

---

## Filament

- **Resource:** `App\Filament\Travel\Resources\TravelRequestResource`
- **Pages:** `ListTravelRequests`, `CreateTravelRequest`, `ViewTravelRequest`
- **Custom pages:** `ApprovalQueuePage`, `MyTravelRequestsPage`
- **Widgets:** `PendingApprovalsWidget`, `TravelSpendWidget`
- **Nav group:** Requests

---

## Displaces

| Feature | FlowFlex | TravelPerk | Concur | Egencia |
|---|---|---|---|---|
| Request and approval workflow | Yes | Yes | Yes | Yes |
| Automated policy checking | Yes | Yes | Yes | Yes |
| Native HR approval chain | Yes | No | No | No |
| AI cost estimation | Yes | No | No | No |
| Included in platform | Yes | No | No | No |

---

## Related

- [[bookings]] — approved requests link to booking records
- [[travel-policies]] — policies evaluated on request submission
- [[traveller-profiles]] — traveller preferences used in booking
- [[expense-reports]] — expense reports link back to the travel request
