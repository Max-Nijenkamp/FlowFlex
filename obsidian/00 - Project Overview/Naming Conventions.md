---
tags: [flowflex, naming, conventions, phase/1]
domain: Platform
status: built
last_updated: 2026-05-06
---

# Naming Conventions

All files, classes, database tables, and routes in FlowFlex follow these conventions. Follow them without exception.

## Files & Classes

| Type | Convention | Examples |
|---|---|---|
| Models | `PascalCase` singular | `Employee`, `PayRun`, `SalesOpportunity` |
| Controllers | `PascalCase` + `Controller` (API routes only) | `EmployeeController` |
| Filament Resources | `PascalCase` + `Resource` | `EmployeeResource`, `InvoiceResource` |
| Filament Pages | `PascalCase` | `ManagePayRoll`, `ViewDashboard` |
| Events | Past tense, descriptive | `EmployeeHired`, `TimeEntryApproved`, `InvoiceOverdue` |
| Listeners | Imperative action | `CreateOnboardingTasks`, `RevokeTenantAccess` |
| Services | Noun + `Service` | `PayrollCalculationService`, `StripeWebhookService` |
| Jobs | Imperative verb | `GeneratePayslipPDF`, `SyncInventoryToMarketplace` |

## Database

| Type | Convention | Examples |
|---|---|---|
| Tables | `snake_case` plural | `employees`, `pay_runs`, `time_entries` |
| Foreign keys | `{model}_id` | `employee_id`, `project_id` |
| Pivot tables | Alphabetical order | `employee_role`, `module_tenant` |

### Required Columns on Every Module Table

```sql
id          ULID PRIMARY KEY   -- ULID preferred over UUID or auto-increment
tenant_id   ULID NOT NULL      -- always present
created_at  TIMESTAMP
updated_at  TIMESTAMP
deleted_at  TIMESTAMP NULL     -- soft deletes on any table where data should be recoverable
```

### Required Indexes on Every Module Table

```sql
INDEX(tenant_id)
INDEX(status)              -- if the table has a status column
INDEX(created_at)
INDEX(foreign_key_columns) -- all FK columns
```

## Permissions (Spatie)

Pattern: `{module}.{resource}.{action}`

Examples:
- `hr.panel.access` — panel-level access
- `hr.employees.view` — resource view
- `hr.employees.create` — resource create
- `hr.employees.edit` — resource edit
- `hr.employees.delete` — resource delete
- `hr.employees.salary.view` — field-level view
- `hr.employees.salary.edit` — field-level edit
- `finance.invoices.send` — custom action

## Routes

| Type | Pattern | Example |
|---|---|---|
| API routes | `/api/v1/{module}/{resource}` | `/api/v1/hr/employees` |
| Filament panel routes | Managed by Filament automatically | — |
| Webhook routes | `/webhooks/{provider}` | `/webhooks/stripe` |

## Module Keys

Module keys (used in `tenant_modules`, permissions, config) are always `snake_case`:
- `hr`, `finance`, `projects`, `crm`, `marketing`, `operations`
- `analytics`, `it`, `legal`, `ecommerce`, `communications`, `learning`

## Events — Full Reference

Events follow past-tense naming that clearly describes what happened:

**HR Domain:**
- `EmployeeHired`, `EmployeeOffboarded`, `EmployeeDepartmentChanged`
- `OnboardingStarted`, `OnboardingCompleted`
- `LeaveRequested`, `LeaveApproved`, `LeaveRejected`
- `PayRunProcessed`, `PayslipGenerated`
- `CandidateHired`, `CandidateRejected`
- `ShiftPublished`, `ClockIn`, `ClockOut`
- `CertificationExpired`, `TrainingOverdue`

**Finance Domain:**
- `InvoiceCreated`, `InvoiceSent`, `InvoicePaid`, `InvoiceOverdue`
- `ExpenseSubmitted`, `ExpenseApproved`
- `PaymentRunCompleted`

**Projects Domain:**
- `TaskCreated`, `TaskCompleted`, `TaskOverdue`
- `ProjectMilestoneReached`, `ProjectCompleted`
- `TimeEntryApproved`
- `DocumentSigned`

**Operations Domain:**
- `StockBelowReorderPoint`
- `FieldJobCompleted`

**CRM Domain:**
- `TicketResolved`

**E-commerce Domain:**
- `OrderPlaced`

**LMS Domain:**
- `CourseCompleted`

## Related

- [[Architecture]]
- [[Security Rules]]
- [[Module Development Checklist]]
