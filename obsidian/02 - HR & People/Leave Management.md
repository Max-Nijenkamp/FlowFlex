---
tags: [flowflex, domain/hr, leave, absence, phase/2]
domain: HR & People
panel: hr
color: "#7C3AED"
status: complete
last_updated: 2026-05-07
---

# Leave Management

End-to-end leave request, approval, and tracking. Policy rules are fully configurable per company, per leave type, per employee category.

**Who uses it:** All employees, managers, HR team
**Filament Panel:** `hr`
**Depends on:** [[Employee Profiles]], [[Scheduling & Shifts]] (if active)
**Phase:** 2
**Build complexity:** High — 2 resources, 2 pages, 5 tables

## Implementation (Phase 2 — Built)

**Filament Resources:**
- `LeaveTypeResource` — nav group: Leave, sort: 1
- `LeaveRequestResource` — nav group: Leave, sort: 2, eager-loads `employee` and `leaveType`

**Models:** `LeaveType`, `LeavePolicy`, `LeaveBalance`, `LeaveRequest`, `PublicHoliday`

**Events wired:**
- `LeaveRequested` → `NotifyManagerOfLeaveRequest` → `LeaveRequestedNotification` to manager
- `LeaveApproved` → `NotifyEmployeeLeaveApproved` → `LeaveApprovedNotification` to employee
- `LeaveRejected` → `NotifyEmployeeLeaveRejected` → `LeaveRejectedNotification` to employee

**What's live:**
- Leave type config: name, is_active flag
- Leave request form: employee, leave type, start/end date, half-day toggle, reason, status
- Leave request table: employee name, leave type, dates, total_days, status badge with colour
- Status enum: `pending`, `approved`, `rejected`, `cancelled`
- N+1 prevented via `getEloquentQuery()->with(['employee', 'leaveType'])`

**Permissions enforced:** `hr.leave-types.*`, `hr.leave-requests.*`, `hr.leave.approve`

**Not yet built (future phases):** leave balance accrual engine, team calendar view, multi-level approval, blackout periods, public holiday calendar UI, year-end reporting

## Events Fired

- `LeaveRequested`
- `LeaveApproved` → consumed by [[Payroll]] (deducts unpaid leave), [[Scheduling & Shifts]] (removes from rota)
- `LeaveRejected`
- `LeaveBalanceLow` → notification to employee

## Sub-modules

### Leave Types & Policies

**Features:**
- Leave type configuration:
  - Annual / Holiday
  - Sick
  - Maternity / Paternity / Shared Parental
  - TOIL (Time Off In Lieu)
  - Unpaid
  - Compassionate
  - Study leave
  - Jury duty
  - Custom (workspace-defined)
- Accrual rules: monthly, per pay period, anniversary-based
- Carry-over rules (how much unused leave can roll into next year, max cap)
- Maximum balance cap
- Negative balance allowance (can employee go into leave debt?)
- Minimum notice period per leave type
- Probation period restrictions (no annual leave in first 3 months)
- **Multi-country leave law presets:** UK, Netherlands, Germany, France, US

### Leave Requests & Approval

**Features:**
- Employee submits leave request (type, dates, half-day options, notes)
- Manager receives notification and approves or rejects with reason
- Multi-level approval (for long leave or above a threshold of days)
- Team calendar overlap check (warns if too many people off simultaneously)
- Blackout periods (HR blocks certain dates — no leave during Q4 peak)
- Cancellation flow (employee can cancel if approved but not yet taken)

### Leave Balances & Calendar

**Features:**
- Employee self-service balance view (days taken, days remaining, days accrued)
- Manager team leave calendar (who is off when — weekly/monthly view)
- Public holiday calendar (per country/region, auto-populated)
- Year-end balance reporting
- Leave report export by employee, department, leave type

## Database Tables (5)

1. `leave_types` — configured leave types per tenant
2. `leave_policies` — accrual and carry-over rules per leave type
3. `leave_balances` — current balance per employee per leave type
4. `leave_requests` — leave request records with approval status
5. `public_holidays` — holiday calendars per country/region

## Related

- [[HR Overview]]
- [[Employee Profiles]]
- [[Payroll]]
- [[Scheduling & Shifts]]
- [[Resource & Capacity Planning]]
