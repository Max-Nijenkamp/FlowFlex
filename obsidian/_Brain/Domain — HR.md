---
tags: [brain, domain, hr]
last_updated: 2026-05-07
---

# Domain — HR

**Spec:** `02 - HR & People/` — Employee Profiles, Payroll, Leave Management, Onboarding  
**All models:** `app/Models/Hr/`  
**All resources:** `app/Filament/Hr/Resources/`  
**Panel:** `/hr` — guard `tenant`, color purple `#7C3AED`  
**Module key:** `hr`

All models carry: `BelongsToCompany`, `HasUlids`, `SoftDeletes`, `LogsActivity`.

---

## Models

### Employee
**Spec:** `02 - HR & People/Employee Profiles.md`  
**Table:** `employees`  
**Purpose:** Central HR record for every person in the company. All other HR modules (leave, payroll, onboarding, documents) reference this model. Self-referential for manager hierarchy.

**Fillable fields:**
- `company_id`
- `employee_number` (string, optional auto-gen)
- `first_name`, `middle_name` (nullable), `last_name`
- `email`, `phone`
- `date_of_birth` (date)
- `national_id_encrypted` → `encrypted` cast (NI number, SSN, BSN — sensitive PII)
- `emergency_contact_name`, `emergency_contact_phone`, `emergency_contact_relationship`
- `department_id` → `Department`
- `job_title`, `location`
- `manager_id` → self (Employee — nullable, self-referential)
- `start_date` (date), `probation_end_date` (date, nullable)
- `contracted_hours_per_week` (decimal)
- `employment_type` → `EmploymentType` enum
- `employment_status` → `EmploymentStatus` enum
- `profile_photo_file_id` → `File`

**Casts:**
- `date_of_birth`, `start_date`, `probation_end_date` → date
- `national_id_encrypted` → encrypted
- `employment_type` → `EmploymentType`
- `employment_status` → `EmploymentStatus`

**Relations:**
- `department()` → BelongsTo `Department`
- `manager()` → BelongsTo `Employee` (self, via `manager_id`)
- `directReports()` → HasMany `Employee` (self, via `manager_id`)
- `profilePhoto()` → BelongsTo `File`
- `documents()` → HasMany `EmployeeDocument`
- `customFieldValues()` → HasMany `EmployeeCustomFieldValue`
- `leaveBalances()` → HasMany `LeaveBalance`
- `leaveRequests()` → HasMany `LeaveRequest`
- `salaryRecords()` → HasMany `SalaryRecord`
- `onboardingFlows()` → HasMany `OnboardingFlow`
- `payslips()` → HasMany `Payslip`

**Notable attributes:**
- `getFullNameAttribute()` — `collect([$first, $middle, $last])->filter()->implode(' ')` — handles nullable middle name correctly

**Activity log:** excludes `national_id_encrypted` (encrypted field — would log plaintext)

---

### Department
**Table:** `departments`  
**Purpose:** Organisational unit. Employees belong to a department. A department can have a manager.

**Fillable fields:**
- `company_id`, `name`, `description`, `manager_employee_id` (nullable → Employee)

**Relations:**
- `employees()` → HasMany `Employee`
- `manager()` → BelongsTo `Employee` (via `manager_employee_id`)

---

### EmployeeDocument
**Table:** `employee_documents`  
**Purpose:** A file attachment on an employee record (contract, ID scan, certificate). Managed via `DocumentsRelationManager` on EmployeeResource.

**Fillable fields:**
- `company_id`, `employee_id`, `file_id`
- `name`, `document_type` (string)
- `expires_at` (date, nullable)

**Relations:**
- `employee()` → BelongsTo `Employee`
- `file()` → BelongsTo `File`

---

### EmployeeCustomField
**Table:** `employee_custom_fields`  
**Purpose:** Workspace-defined field definitions. HR admin configures which extra fields appear on employee records (e.g., "T-shirt size", "Badge number").

**Fillable fields:**
- `company_id`, `label`, `field_type` (text/number/date/boolean/dropdown), `is_required` (bool), `sort_order` (int)

---

### EmployeeCustomFieldValue
**Table:** `employee_custom_field_values`  
**Purpose:** Per-employee value for each custom field. One row per employee per custom field.

**Fillable fields:**
- `employee_id`, `employee_custom_field_id`, `value` (string)

**Relations:**
- `employee()` → BelongsTo `Employee`
- `customField()` → BelongsTo `EmployeeCustomField`

---

### LeaveType
**Spec:** `02 - HR & People/Leave Management.md`  
**Table:** `leave_types`  
**Purpose:** Configuration for each kind of leave (annual, sick, parental, TOIL, etc.). Workspace-specific — each company defines their own leave types.

**Fillable fields:**
- `company_id`, `name`
- `is_paid` (bool) — whether leave is paid or unpaid
- `accrual_rate_days_per_year` (decimal) — e.g. 25.0 for 25 days annual leave
- `max_carryover_days` (int, nullable) — how many days roll to next year
- `requires_approval` (bool)
- `color` (string — hex, for calendar display)

---

### LeavePolicy
**Table:** `leave_policies`  
**Purpose:** Maps leave entitlement to employment type. E.g. "Full-time employees get 25 days annual leave; part-time get 15 days." No Filament resource — managed as data, not via panel.

**Fillable fields:**
- `company_id`, `leave_type_id`, `employment_type` (string matching EmploymentType enum values), `days_per_year` (int)

**Relations:**
- `leaveType()` → BelongsTo `LeaveType`

---

### LeaveBalance
**Table:** `leave_balances`  
**Purpose:** Running tally of leave per employee per leave type per year. Updated when leave requests are approved.

**Fillable fields:**
- `company_id`, `employee_id`, `leave_type_id`
- `balance_days` (decimal), `accrued_days` (decimal), `used_days` (decimal)
- `year` (int — calendar year this balance applies to)

**Relations:**
- `employee()` → BelongsTo `Employee`
- `leaveType()` → BelongsTo `LeaveType`

---

### LeaveRequest
**Table:** `leave_requests`  
**Purpose:** An employee's request for time off. Goes through pending → approved/rejected workflow. `total_days` is calculated from the date range on creation.

**Fillable fields:**
- `company_id`, `employee_id`, `leave_type_id`
- `start_date` (date), `end_date` (date)
- `half_day` (bool), `total_days` (decimal)
- `status` → `LeaveRequestStatus` enum (Pending/Approved/Rejected/Cancelled)
- `approved_by` (tenant_id, nullable), `approved_at` (datetime, nullable)
- `notes` (text, nullable)

**Lifecycle:** `mutateFormDataBeforeCreate()` in resource computes `total_days` from the date range.

**Relations:**
- `employee()` → BelongsTo `Employee`
- `leaveType()` → BelongsTo `LeaveType`
- `approver()` → BelongsTo `Tenant` (via `approved_by`)

**Events fired:** `LeaveRequested`, `LeaveApproved`, `LeaveRejected`

---

### OnboardingTemplate
**Spec:** `02 - HR & People/Onboarding.md`  
**Table:** `onboarding_templates`  
**Purpose:** A reusable checklist of tasks for onboarding new employees. When an employee is hired, the template is cloned into an `OnboardingFlow`.

**Fillable fields:**
- `company_id`, `name`, `description`

**Relations:**
- `tasks()` → HasMany `OnboardingTemplateTask` (relation name is `tasks`, NOT `templateTasks`)

---

### OnboardingTemplateTask
**Table:** `onboarding_template_tasks`  
**Purpose:** A task definition within an onboarding template. These are the master definitions that get cloned when a flow is created.

**Fillable fields:**
- `onboarding_template_id`, `title`, `description`
- `due_days_after_start` (int — days relative to employee start date)
- `is_required` (bool), `sort_order` (int)

**Relations:**
- `template()` → BelongsTo `OnboardingTemplate`

---

### OnboardingFlow
**Table:** `onboarding_flows`  
**Purpose:** An instance of a template assigned to a specific employee. Tracks completion status.

**Fillable fields:**
- `company_id`, `employee_id`, `template_id`
- `status` → `OnboardingFlowStatus` enum
- `started_at` (datetime), `completed_at` (datetime, nullable)

**Relations:**
- `employee()` → BelongsTo `Employee`
- `template()` → BelongsTo `OnboardingTemplate`
- `tasks()` → HasMany `OnboardingTask`

**Events fired:** `OnboardingStarted`, `OnboardingCompleted`

---

### OnboardingTask
**Table:** `onboarding_tasks`  
**Purpose:** An instance of a template task within a specific employee's onboarding flow. Tracks individual completion.

**Fillable fields:**
- `onboarding_flow_id`, `title`, `description`
- `is_completed` (bool), `completed_at` (datetime, nullable)
- `due_date` (date)
- `assigned_to_tenant_id` (nullable — which team member is responsible)

**Relations:**
- `flow()` → BelongsTo `OnboardingFlow`
- `assignedTo()` → BelongsTo `Tenant`

---

### OnboardingCheckin
**Table:** `onboarding_checkins`  
**Purpose:** Scheduled check-in meeting between employee and manager/HR during onboarding period.

**Fillable fields:**
- `company_id`, `employee_id`, `scheduled_at` (datetime), `completed_at` (datetime, nullable), `notes` (text)

**Relations:**
- `employee()` → BelongsTo `Employee`
- `responses()` → HasMany `OnboardingCheckinResponse`

---

### OnboardingCheckinResponse
**Table:** `onboarding_checkin_responses`  
**Purpose:** Q&A responses recorded during a check-in session.

**Fillable fields:**
- `onboarding_checkin_id`, `question` (string), `response` (text)

---

### PayrollEntity
**Spec:** `02 - HR & People/Payroll.md`  
**Table:** `payroll_entities`  
**Purpose:** Company's payroll configuration — legal entity, PAYE registration, bank details, tax reference. Sensitive fields encrypted at rest. One company can have multiple payroll entities (e.g., UK entity + NL entity).

**Fillable fields:**
- `company_id`, `name`, `registration_number`
- `tax_reference_encrypted` → `encrypted` cast (PAYE reference, tax ID — renamed with `_encrypted` suffix to make sensitivity explicit)
- `bank_account_number_encrypted` → `encrypted` cast
- `bank_sort_code`
- `address_line_1`, `address_line_2` (nullable), `city`, `postcode`, `country`

**Security:** Both encrypted fields use `encrypted` cast and `_encrypted` suffix. Never log them.  
**Form field:** Use `tax_reference_encrypted` as the form field name — not `tax_reference`.

**Relations:**
- `payRuns()` → HasMany `PayRun`

---

### PayElement
**Table:** `pay_elements`  
**Purpose:** Reusable pay component definitions. "Basic Salary", "Overtime", "Pension (Employee)", "NI (Employer)". Used in PayRunLines.

**Fillable fields:**
- `company_id`, `name`
- `type` → `PayElementType` enum (Earnings/Deduction/EmployerNI/EmployeeNI/Pension)
- `is_pensionable` (bool), `is_taxable` (bool), `is_niable` (bool)
- `default_amount` (decimal, nullable)

---

### PayRun
**Table:** `pay_runs`  
**Purpose:** A complete payroll batch for a pay period. Covers all employees on a specific payroll entity. Lifecycle: Draft → PendingApproval → Approved → Processing → Processed.

**Fillable fields:**
- `company_id`, `payroll_entity_id`
- `status` → `PayRunStatus` enum
- `pay_frequency` → `PayFrequency` enum
- `pay_period_start` (date), `pay_period_end` (date), `payment_date` (date)
- `total_gross` (decimal), `total_net` (decimal), `total_deductions` (decimal)
- `created_by_tenant_id`, `approved_by_tenant_id` (nullable), `approved_at` (datetime, nullable), `processed_at` (datetime, nullable)

**Casts:** `status` → `PayRunStatus`, `pay_frequency` → `PayFrequency`, all dates → date/datetime

**Relations:**
- `payrollEntity()` → BelongsTo `PayrollEntity`
- `runEmployees()` → HasMany `PayRunEmployee`
- `payslips()` → HasMany `Payslip`
- `createdBy()` → BelongsTo `Tenant` (via `created_by_tenant_id`)
- `approvedBy()` → BelongsTo `Tenant` (via `approved_by_tenant_id`)

**Activity log:** excludes `total_gross`, `total_net`, `total_deductions` (financial aggregates)  
**Events fired:** `PayRunApproved`, `PayRunProcessed`

---

### PayRunEmployee
**Table:** `pay_run_employees`  
**Purpose:** Per-employee snapshot within a pay run. Holds the gross/net amounts for one employee in one pay run. Detailed breakdown in PayRunLine.

**Fillable fields:**
- `pay_run_id`, `employee_id`
- `gross_pay` (decimal), `net_pay` (decimal)
- `tax_amount` (decimal), `ni_amount` (decimal), `pension_amount` (decimal)
- `notes` (text, nullable)

**Relations:**
- `payRun()` → BelongsTo `PayRun`
- `employee()` → BelongsTo `Employee`
- `lines()` → HasMany `PayRunLine`

**Activity log:** explicitly excludes `gross_pay` and `net_pay` — financial data is not logged. Uses `->logOnly(['notes'])`.

---

### PayRunLine
**Table:** `pay_run_lines`  
**Purpose:** Individual line item (one pay element) within a PayRunEmployee. E.g., "Basic Salary: £3,000", "Overtime: £200", "NI: -£300".

**Fillable fields:**
- `pay_run_employee_id`, `pay_element_id`
- `description`, `amount` (decimal)
- `quantity` (decimal, nullable), `unit_rate` (decimal, nullable)

**Relations:**
- `payRunEmployee()` → BelongsTo `PayRunEmployee`
- `payElement()` → BelongsTo `PayElement`

---

### Payslip
**Table:** `payslips`  
**Purpose:** Generated PDF payslip for one employee in one pay run. The PDF is stored as a File record; Payslip holds metadata.

**Fillable fields:**
- `company_id`, `pay_run_id`, `employee_id`, `pay_run_employee_id`
- `pdf_file_id` (nullable — set when PDF is generated) — **not in `$fillable`** — use `pdf_file_id` via FileStorageService only
- `period_start` (date), `period_end` (date)
- `status` (string: pending/generated/sent)
- `generated_at` (datetime, nullable), `sent_at` (datetime, nullable)

**Pending:** `GeneratePayslipPdf` job is a stub — needs a PDF package (Browsershot or DomPDF) before payslips render.

**Relations:**
- `payRun()` → BelongsTo `PayRun`
- `employee()` → BelongsTo `Employee`
- `payRunEmployee()` → BelongsTo `PayRunEmployee`
- `pdfFile()` → BelongsTo `File` (via `pdf_file_id`)

**Queue job:** `GeneratePayslipPdf` — uses `withoutGlobalScopes()` + explicit `company_id` (queue has no auth context).  
**Activity log:** only `generated_at`, `sent_at` — never financial amounts.

---

### SalaryRecord
**Table:** `salary_records`  
**Purpose:** Salary history per employee. Every time salary changes, a new record is added. Current salary = latest by `effective_date`.

**Fillable fields:**
- `company_id`, `employee_id`, `amount` (decimal), `currency`, `effective_date` (date), `pay_frequency`, `notes` (nullable)

**Relations:**
- `employee()` → BelongsTo `Employee`

**Activity log:** excludes `amount` (financial data)

---

### Deduction
**Table:** `deductions`  
**Purpose:** Recurring or one-off deduction applied to an employee's pay (e.g., season ticket loan, attachment of earnings order).

**Fillable fields:**
- `company_id`, `employee_id`, `pay_element_id`
- `amount` (decimal), `recurring` (bool)
- `start_date` (date), `end_date` (date, nullable)
- `notes` (nullable)

**Relations:**
- `employee()` → BelongsTo `Employee`
- `payElement()` → BelongsTo `PayElement`

---

### ContractorPayment
**Table:** `contractor_payments`  
**Purpose:** Ad-hoc payment records for contractors (not on the payroll, no PAYE). Separate from PayRun which covers employed staff.

**Fillable fields:**
- `company_id`, `tenant_id` (which team member the contractor is linked to)
- `amount` (decimal), `currency`, `payment_date` (date)
- `reference`, `status` (string: pending/processed)
- `notes` (nullable)

**Relations:**
- `tenant()` → BelongsTo `Tenant`

---

### TaxConfiguration
**Table:** `tax_configurations`  
**Purpose:** Tax band configuration per country per tax year. Used by payroll calculation engine.

**Fillable fields:**
- `company_id`, `country` (ISO code), `tax_year` (int)
- `personal_allowance` (decimal), `basic_rate` (decimal), `higher_rate` (decimal)
- `ni_primary_threshold` (decimal), `ni_rate` (decimal)

---

### PublicHoliday
**Table:** `public_holidays`  
**Purpose:** Company-specific public holiday calendar. Used by leave calculations to exclude public holidays from leave day counts.

**Fillable fields:**
- `company_id`, `name`, `date` (date), `country` (ISO code), `is_mandatory` (bool)

---

## Resources (HR Panel)

| Resource | Model | Nav Group | Sort | Permissions | Key Features |
|---|---|---|---|---|---|
| `DepartmentResource` | `Department` | People | 1 | `hr.departments.*` | CRUD |
| `EmployeeResource` | `Employee` | People | 2 | `hr.employees.*` | CRUD, DocumentsRelationManager |
| `LeaveTypeResource` | `LeaveType` | Leave | 1 | `hr.leave-types.*` | CRUD |
| `LeaveRequestResource` | `LeaveRequest` | Leave | 2 | `hr.leave-requests.*`, `hr.leave.approve` | CRUD, approve/reject actions, eager-loads employee+leaveType |
| `OnboardingTemplateResource` | `OnboardingTemplate` | Onboarding | 1 | `hr.onboarding-templates.*` | CRUD, TemplateTasksRelationManager (relation name: `tasks`) |
| `OnboardingFlowResource` | `OnboardingFlow` | Onboarding | 2 | `hr.onboarding-flows.*` | CRUD |
| `PayrollEntityResource` | `PayrollEntity` | Payroll | 1 | `hr.payroll-entities.*` | CRUD, form field is `tax_reference_encrypted` |
| `PayElementResource` | `PayElement` | Payroll | 2 | `hr.pay-elements.*` | CRUD |
| `PayRunResource` | `PayRun` | Payroll | 3 | `hr.pay-runs.*` | CRUD, approve action |
| `PayslipResource` | `Payslip` | Payroll | 4 | `hr.payslips.*` | Read-only list, eager-loads employee+payRun |
| `SalaryRecordResource` | `SalaryRecord` | Payroll | 5 | `hr.salary-records.*` | CRUD |
| `ContractorPaymentResource` | `ContractorPayment` | Payroll | 6 | `hr.contractor-payments.*` | CRUD |
| `DeductionResource` | `Deduction` | Payroll | 7 | `hr.deductions.*` | CRUD |
| `PublicHolidayResource` | `PublicHoliday` | Payroll | 8 | `hr.public-holidays.*` | CRUD |

---

## Enums

### EmploymentStatus
`App\Enums\Hr\EmploymentStatus`  
`Active`, `OnLeave`, `Probation`, `Terminated`

### EmploymentType
`App\Enums\Hr\EmploymentType`  
`FullTime`, `PartTime`, `Contractor`, `Intern`

### PayRunStatus
`App\Enums\Hr\PayRunStatus`  
`Draft`, `PendingApproval`, `Approved`, `Processing`, `Processed`, `Failed`  
Colors: Draft=gray, PendingApproval=warning, Approved=info, Processing=info, Processed=success, Failed=danger

### PayFrequency
`App\Enums\Hr\PayFrequency`  
`Weekly`, `BiWeekly`, `Monthly`, `FourWeekly`  
Labels: Weekly, Bi-weekly, Monthly, 4-Weekly

### LeaveRequestStatus
`App\Enums\Hr\LeaveRequestStatus`  
`Pending`, `Approved`, `Rejected`, `Cancelled`

### PayElementType
`App\Enums\Hr\PayElementType`  
`Earnings`, `Deduction`, `EmployerNI`, `EmployeeNI`, `Pension`

### OnboardingFlowStatus
`App\Enums\Hr\OnboardingFlowStatus`  
`NotStarted`, `InProgress`, `Completed`, `Cancelled`

---

## Events (Phase 2)

All wired in `EventServiceProvider`. All listeners implement `ShouldQueue`.

| Event | Listener | Type | What it does |
|---|---|---|---|
| `EmployeeProfileCreated` | `SendWelcomePackEmail` | stub | Ready for email integration |
| `EmployeeProfileUpdated` | stub | stub | Ready for webhooks |
| `EmployeeDepartmentChanged` | stub | stub | — |
| `EmployeeRoleChanged` | stub | stub | — |
| `LeaveRequested` | `NotifyManagerOfLeaveRequest` | real | Sends LeaveRequestedNotification to manager |
| `LeaveApproved` | `NotifyEmployeeLeaveApproved` | real | Sends LeaveApprovedNotification to employee |
| `LeaveRejected` | `NotifyEmployeeLeaveRejected` | real | Sends LeaveRejectedNotification to employee |
| `LeaveBalanceLow` | `NotifyEmployeeLeaveBalanceLow` | real | Sends LeaveBalanceLowNotification to employee |
| `OnboardingStarted` | `SendOnboardingStartedNotification` | real | — |
| `PayRunApproved` | `GeneratePayslipPdfJob` | stub | Dispatches GeneratePayslipPdf job |
| `PayslipGenerated` | `SendPayslipEmail` | real | Sends PayslipGeneratedNotification |
| `PayRunProcessed` | stub | stub | — |
