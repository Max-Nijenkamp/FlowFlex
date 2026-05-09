---
type: module
domain: Finance & Accounting
panel: finance
cssclasses: domain-finance
phase: 6
status: planned
migration_range: 200000–249999
last_updated: 2026-05-09
---

# Travel & Expense Management

Business travel booking, travel policy enforcement, per diem management, and expense reporting. Replaces Navan, TravelPerk, and Expensify.

---

## Features

### Travel Booking
- Flight search (Amadeus/Sabre GDS or TravelPerk API)
- Hotel search and booking
- Car rental
- Rail tickets
- All bookings centralised — no personal card needed
- Company travel portal: employees self-book within policy

### Travel Policy
- Policy builder: max hotel rate per city, cabin class rules, advance booking requirements
- Out-of-policy booking requests (manager approval)
- CO₂ emissions tracking per trip
- Preferred suppliers / negotiated rates

### Per Diem
- Country-based per diem rates (EU/HMRC/IRS tables)
- Auto-calculate based on destination and trip duration
- Flat-rate or actual-cost method
- Auto-post to payroll as non-taxable allowance

### Expense Reports
- Group multiple card transactions into one expense report
- Trip-linked expenses (everything attached to one trip record)
- Manager approval workflow
- Finance approval + GL posting
- Reimbursement via payroll run or direct bank transfer

### Reporting
- Travel spend by person/department/destination
- Policy compliance rate
- Top merchants
- CO₂ report for ESG reporting

---

## Data Model

```erDiagram
    travel_trips {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        string destination
        date departure_date
        date return_date
        string purpose
        string status
        decimal per_diem_amount
        decimal total_spend
    }

    travel_bookings {
        ulid id PK
        ulid trip_id FK
        string type
        string provider_reference
        decimal amount
        string currency
        json booking_details
        string status
    }

    expense_reports {
        ulid id PK
        ulid company_id FK
        ulid employee_id FK
        ulid trip_id FK
        string status
        decimal total_amount
        timestamp submitted_at
        timestamp approved_at
        ulid approved_by FK
    }
```

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `TripBooked` | Booking confirmed | Notifications (traveller, manager) |
| `ExpenseReportApproved` | Manager approves | Finance (GL post), Payroll (reimbursement) |
| `TravelPolicyViolation` | Out-of-policy booking | Notifications (manager, travel admin) |

---

## Permissions

```
finance.travel.view-any
finance.travel.book
finance.travel.approve
finance.expenses.manage-policy
```

---

## Competitors Displaced

Navan · TravelPerk · Expensify · Concur · Rydoo

---

## Related

- [[MOC_Finance]]
- [[corporate-cards-spend-management]]
- [[entity-employee]]
