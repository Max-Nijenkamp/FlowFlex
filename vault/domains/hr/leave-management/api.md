---
domain: hr
module: leave-management
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Leave Management — API, Events & DTOs

Service surface lives in [[architecture]]; this note covers DTOs and the cross-domain event. No REST API surface — the module is driven through Filament + the service. Event contracts: [[../../../architecture/event-bus]].

## DTOs (spatie/laravel-data)

### SubmitLeaveRequestData (input)

| Field | Type | Validation |
|---|---|---|
| employee_id | string | required, ulid, exists in company |
| leave_type_id | string | required, ulid, exists in company |
| start_date | CarbonImmutable | required, date |
| end_date | CarbonImmutable | required, date |
| note | ?string | nullable, max:1000 *(assumed)* |

Cross-field: `end_date >= start_date` ("End date must be on or after start date"); requested span must yield ≥ 0.5 working days *(assumed)*; warn (not block) on overlap with approved leave / public holiday.

### ApproveLeaveRequestData (input)

| Field | Type | Validation |
|---|---|---|
| leave_request_id | string | required, ulid |

### RejectLeaveRequestData (input)

| Field | Type | Validation |
|---|---|---|
| leave_request_id | string | required, ulid |
| rejection_reason | string | required, max:1000 |

### LeaveRequestData (output)

id, employee_id, employee_name, leave_type_id, leave_type_name, start_date, end_date, days_requested, status, note, approved_by, approved_at, rejection_reason

### LeaveBalanceData (output)

employee_id, leave_type_id, year, allocated_days, taken_days, pending_days, remaining_days (computed)

## Events

### Fires: LeaveRequestApproved

| Payload field | Type | Notes |
|---|---|---|
| company_id | string | always first |
| leave_request_id | string | |
| employee_id | string | |
| leave_type_id | string | |
| start_date | CarbonImmutable | |
| end_date | CarbonImmutable | |
| days | float | working days |

Consumed by: [[../payroll/_module\|hr.payroll]] (`UpdatePayrollDeductionsListener` — unpaid-type deductions), [[../shift-scheduling/_module\|hr.shifts]] (block scheduling over the range). Contract source of truth: [[../../../architecture/event-bus]].

Consumes events: none.

## Notifications

`LeaveApprovedMail` / `LeaveRejectedMail` queued on transition via [[../../../infrastructure/mail]]; in-app via [[../../core/notifications/_module\|core.notifications]].

## Related

- [[_module]]
- [[architecture]]
- [[features/leave-request-workflow]]
