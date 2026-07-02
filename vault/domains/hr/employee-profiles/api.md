---
domain: hr
module: employee-profiles
type: api
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# API — Employee Profiles

> Planned. Service surface, DTOs, and domain events. Service methods detailed in [[architecture]]. Event contracts per [[../../../architecture/event-bus]].

## Service Surface

`EmployeeServiceInterface` → `EmployeeService` (see [[architecture]]):
`hire`, `update`, `offboard`, `directReports`, `managerChain`. No public REST endpoints specified; surface is Filament-driven CRUD plus the service API.

## DTOs

### CreateEmployeeData (input)

| Field | Type | Validation |
|---|---|---|
| first_name / last_name | string | required, max:100 |
| email | string | required, email, unique per company (`hr_employees`) |
| phone | ?string | nullable, `phone:AUTO` → E.164 |
| personal_email | ?string | nullable, email |
| date_of_birth | ?CarbonImmutable | nullable, date, before:today |
| national_id | ?string | nullable, max:50 |
| hire_date | CarbonImmutable | required, date |
| job_title | string | required, max:150 |
| department_id | ?string | nullable, ulid, exists in company |
| manager_id | ?string | nullable, ulid, exists in company, not self |
| employment_type | string | required, in:full-time,part-time,contractor |
| create_user_account | bool | default false — triggers invitation *(assumed)* |

Message: "An employee with this email already exists in your company."
Cross-field: `manager_id` must not create a cycle (validated in service → `ManagerCycleException`).

### OffboardEmployeeData (input)

| Field | Type | Validation |
|---|---|---|
| employee_id | string | required, ulid |
| termination_date | CarbonImmutable | required, on/after hire_date |
| termination_reason | string | required, max:1000 |

### UpdateEmployeeData (input)

Referenced by `EmployeeService::update`; full field list to be derived from `CreateEmployeeData` minus immutable fields *(UNVERIFIED — spec lists the DTO file but no field table)*.

### EmployeeData (output)

`id`, `employee_number`, `first_name`, `last_name`, `full_name`, `email`, `phone`, `job_title`, `department_id`, `department_name`, `manager_id`, `manager_name`, `employment_type`, `status`, `hire_date`, `termination_date`, `direct_report_count`.

## Events Fired

### EmployeeHired

| Payload field | Type |
|---|---|
| company_id | string |
| employee_id | string |
| user_id | ?string |
| start_date | CarbonImmutable |
| job_title | string |

### EmployeeOffboarded

| Payload field | Type |
|---|---|
| company_id | string |
| employee_id | string |
| user_id | ?string |
| termination_date | CarbonImmutable |

Consumers per [[../../../architecture/event-bus]]: payroll record stub, onboarding plan, IT provisioning (P3); final pay + access revocation. Consumes: none.

## Related

- [[architecture]] · [[data-model]] · [[security]]
- [[../../../architecture/event-bus]]
