---
tags: [brain, relations]
last_updated: 2026-05-07
---

# Relations Map

Full cross-domain foreign key map. Every model relation listed once, with direction, FK column, and notes. Use this to trace data flow before writing queries or migrations.

---

## Core Anchors

Every tenant-scoped model has `company_id → companies.id` via `BelongsToCompany` global scope.  
Exceptions with no `BelongsToCompany`: `Tenant`, `Module`, `SubModule`, `Address` (polymorphic), `CompanyModule` (pivot), all Marketing models.

| Model | Namespace | Notes |
|---|---|---|
| `Company` | `App\Models\Company` | workspace entity; companies.id is the root FK |
| `Tenant` | `App\Models\Tenant` | auth model for all tenant panels; has company_id but no global scope |
| `User` | `App\Models\User` | super-admin auth model; no company_id |
| `File` | `App\Models\File` | S3/local storage reference; BelongsToCompany |
| `Module` | `App\Models\Module` | platform-wide; no company scope |

---

## Company Relations

```
Company
├── hasMany       → Tenant            (tenants.company_id)
├── belongsToMany → Module            via company_module (CompanyModule pivot)
├── belongsTo     → File              (logo_file_id)
└── morphMany     → Address           (addressable_type/addressable_id)
```

---

## Tenant Relations

```
Tenant
├── belongsTo → Company              (company_id)
├── hasMany   → NotificationPreference (notification_preferences.tenant_id)
└── morphMany → Address              (addressable_type/addressable_id)
```

Tenant is referenced as FK in many models across all domains:

| Table | Column | Meaning |
|---|---|---|
| `tasks` | `assignee_tenant_id` | task assigned to |
| `time_entries` | `tenant_id` | who logged the time |
| `timesheets` | `tenant_id` | whose timesheet |
| `timesheet_approvals` | `approver_tenant_id` | who approved |
| `documents` | `uploaded_by_tenant_id` | uploader |
| `document_folders` | `created_by_tenant_id` | folder creator |
| `document_shares` | `created_by_tenant_id` | share creator |
| `expenses` | `tenant_id` | submitter |
| `expenses` | `approved_by` | approver |
| `expense_reports` | `tenant_id` | submitter |
| `tickets` | `assigned_to` | support agent |
| `ticket_messages` | `sender_tenant_id` | message author |
| `pay_runs` | `created_by_tenant_id` | who created the run |
| `pay_runs` | `approved_by_tenant_id` | who approved |
| `crm_contacts` | `owner_tenant_id` | contact owner |
| `crm_companies` | `owner_tenant_id` | company owner |
| `deals` | `owner_tenant_id` | sales rep |
| `deal_notes` | `tenant_id` | note author |
| `crm_activities` | `tenant_id` | activity performed by |
| `onboarding_tasks` | `assigned_to_tenant_id` | task responsible |
| `contractor_payments` | `tenant_id` | contractor Tenant |
| `inbox_emails` | `assigned_tenant_id` | inbox email assignee |

---

## HR Domain Relations

```
Department
├── hasMany       → Employee          (employees.department_id)
└── belongsTo     → Employee          (manager_employee_id — the dept manager)

Employee
├── belongsTo     → Company           (company_id)
├── belongsTo     → Department        (department_id)
├── belongsTo     → Employee [self]   (manager_id — manager)
├── hasMany       → Employee [self]   (manager_id — direct reports)
├── belongsTo     → File              (profile_photo_file_id)
├── hasMany       → EmployeeDocument
├── hasMany       → EmployeeCustomFieldValue
├── hasMany       → LeaveBalance
├── hasMany       → LeaveRequest
├── hasMany       → SalaryRecord
├── hasMany       → OnboardingFlow
└── hasMany       → Payslip

EmployeeDocument
├── belongsTo     → Employee
└── belongsTo     → File              (file_id — the actual document)

EmployeeCustomFieldValue
├── belongsTo     → Employee
└── belongsTo     → EmployeeCustomField

LeavePolicy
└── belongsTo     → LeaveType

LeaveBalance
├── belongsTo     → Employee
└── belongsTo     → LeaveType

LeaveRequest
├── belongsTo     → Employee
├── belongsTo     → LeaveType
└── belongsTo     → Tenant            (approved_by — approver)

OnboardingTemplate
└── hasMany       → OnboardingTemplateTask  (relation name: tasks, NOT templateTasks)

OnboardingFlow
├── belongsTo     → Employee
├── belongsTo     → OnboardingTemplate
└── hasMany       → OnboardingTask

OnboardingTask
├── belongsTo     → OnboardingFlow
└── belongsTo     → Tenant            (assigned_to_tenant_id)

OnboardingCheckin
├── belongsTo     → Employee
└── hasMany       → OnboardingCheckinResponse

PayrollEntity
└── hasMany       → PayRun

PayRun
├── belongsTo     → PayrollEntity
├── belongsTo     → Tenant            (created_by_tenant_id)
├── belongsTo     → Tenant            (approved_by_tenant_id)
├── hasMany       → PayRunEmployee
└── hasMany       → Payslip

PayRunEmployee
├── belongsTo     → PayRun
├── belongsTo     → Employee
└── hasMany       → PayRunLine

PayRunLine
├── belongsTo     → PayRunEmployee
└── belongsTo     → PayElement

Payslip
├── belongsTo     → PayRun
├── belongsTo     → Employee
├── belongsTo     → PayRunEmployee
└── belongsTo     → File              (pdf_file_id — the PDF)

Deduction
├── belongsTo     → Employee
└── belongsTo     → PayElement

ContractorPayment
└── belongsTo     → Tenant            (contractor is a Tenant)

SalaryRecord
└── belongsTo     → Employee
```

---

## Projects Domain Relations

```
Task
├── belongsTo     → Tenant [self]     (assignee_tenant_id — assignee)
├── belongsTo     → Task [self]       (parent_id — parent task)
├── hasMany       → Task [self]       (parent_id — child tasks)
├── belongsTo     → Task [self]       (parent_task_id — original spec FK)
├── hasMany       → Task [self]       (parent_task_id — subtasks)
├── belongsToMany → TaskLabel         via task_label_assignments
├── hasMany       → TaskDependency
├── hasMany       → TimeEntry
└── hasMany       → TaskAutomationLog

TaskDependency
├── belongsTo     → Task              (task_id — the dependent task)
└── belongsTo     → Task              (depends_on_task_id — blocking task)

TaskAutomationLog
├── belongsTo     → TaskAutomation
└── belongsTo     → Task

TimeEntry
├── belongsTo     → Task
├── belongsTo     → Tenant            (tenant_id — who logged)
└── belongsTo     → Timesheet

Timesheet
├── belongsTo     → Tenant            (tenant_id)
├── hasMany       → TimeEntry
└── hasMany       → TimesheetApproval

TimesheetApproval
├── belongsTo     → Timesheet
└── belongsTo     → Tenant            (approver_tenant_id)

DocumentFolder
├── belongsTo     → DocumentFolder [self] (parent_folder_id — parent)
├── hasMany       → DocumentFolder [self] (parent_folder_id — children)
├── hasMany       → Document
└── belongsTo     → Tenant            (created_by_tenant_id)

Document
├── belongsTo     → DocumentFolder    (folder_id)
├── belongsTo     → File              (current_file_id — active version)
├── belongsTo     → Tenant            (uploaded_by_tenant_id)
├── hasMany       → DocumentVersion
└── hasMany       → DocumentShare

DocumentVersion
├── belongsTo     → Document
├── belongsTo     → File              (file_id — this version's file)
└── belongsTo     → Tenant            (uploaded_by_tenant_id)

DocumentShare
├── belongsTo     → Document
└── belongsTo     → Tenant            (created_by_tenant_id)
```

---

## Finance Domain Relations

```
RecurringInvoice
├── hasMany       → Invoice
└── belongsTo     → CrmContact        (contact_id) [CROSS-DOMAIN Finance → CRM]

Invoice
├── belongsTo     → CrmContact        (contact_id) [CROSS-DOMAIN Finance → CRM]
├── belongsTo     → RecurringInvoice  (recurring_invoice_id — nullable)
├── hasMany       → InvoiceLine
├── hasMany       → InvoicePayment
├── hasOne        → CreditNote
└── hasMany       → InvoiceEmailEvent

InvoiceLine
└── belongsTo     → Invoice

InvoicePayment
└── belongsTo     → Invoice

InvoiceEmailEvent
└── belongsTo     → Invoice

CreditNote
└── belongsTo     → Invoice

Expense
├── belongsTo     → Tenant            (tenant_id — submitter)
├── belongsTo     → Tenant            (approved_by — approver)
├── belongsTo     → ExpenseReport     (nullable)
├── belongsTo     → ExpenseCategory
└── belongsTo     → File              (receipt_file_id — scanned receipt)

ExpenseReport
├── belongsTo     → Tenant            (tenant_id)
└── hasMany       → Expense
```

---

## CRM Domain Relations

```
Pipeline
├── hasMany       → DealStage
└── hasMany       → Deal

DealStage
├── belongsTo     → Pipeline
└── hasMany       → Deal

CrmCompany
├── hasMany       → CrmContact
├── hasMany       → Deal
├── hasMany       → Ticket
└── morphMany     → CrmActivity       (subject_type/subject_id)

CrmContact
├── belongsTo     → CrmCompany        (crm_company_id)
├── belongsTo     → Tenant            (owner_tenant_id)
├── hasMany       → Deal
├── hasMany       → Ticket
├── hasMany       → CrmContactCustomFieldValue
├── hasMany       → CsatSurvey        (crm_contact_id)
├── hasMany       → InboxEmail        (crm_contact_id)
└── morphMany     → CrmActivity       (subject_type/subject_id)

Deal
├── belongsTo     → Pipeline
├── belongsTo     → DealStage
├── belongsTo     → CrmContact
├── belongsTo     → CrmCompany
├── belongsTo     → Tenant            (owner_tenant_id)
├── hasMany       → DealNote
└── morphMany     → CrmActivity       (subject_type/subject_id)

DealNote
├── belongsTo     → Deal
└── belongsTo     → Tenant            (tenant_id — author)

Ticket
├── belongsTo     → CrmContact        (crm_contact_id)
├── belongsTo     → CrmCompany        (crm_company_id)
├── belongsTo     → Tenant            (assigned_to column — non-standard FK name)
├── hasMany       → TicketMessage
├── hasMany       → CsatSurvey
└── hasMany       → TicketSlaBreach

TicketMessage
├── belongsTo     → Ticket
└── belongsTo     → Tenant            (sender_tenant_id)

TicketSlaRule
└── (no relations — standalone config per priority)

TicketSlaBreach
├── belongsTo     → Ticket
└── belongsTo     → TicketSlaRule

CsatSurvey
├── belongsTo     → Ticket
├── belongsTo     → CrmContact        (crm_contact_id)
└── hasOne        → CsatResponse

CsatResponse
└── belongsTo     → CsatSurvey        (csat_survey_id)

CrmContactCustomField
└── hasMany       → CrmContactCustomFieldValue

CrmContactCustomFieldValue
├── belongsTo     → CrmContact
└── belongsTo     → CrmContactCustomField

CrmActivity
├── morphTo       → subject           (subject_type/subject_id — CrmContact, CrmCompany, or Deal)
└── belongsTo     → Tenant            (tenant_id — who performed the activity)

SharedInbox
└── hasMany       → InboxEmail

InboxEmail
├── belongsTo     → SharedInbox
├── belongsTo     → CrmContact        (crm_contact_id — nullable)
└── belongsTo     → Tenant            (assigned_tenant_id — nullable)
```

---

## Cross-Domain Links (Summary)

The critical joins that cross domain boundaries:

| From Model | Column | To Model | Direction | Why |
|---|---|---|---|---|
| `Invoice` | `contact_id` | `CrmContact` | Finance → CRM | Invoices are issued to CRM contacts |
| `RecurringInvoice` | `contact_id` | `CrmContact` | Finance → CRM | Template also linked to contact |
| `Expense` | `tenant_id` | `Tenant` | Finance → Core | Submitter is a workspace team member |
| `Expense` | `approved_by` | `Tenant` | Finance → Core | Approver is a team member |
| `ExpenseReport` | `tenant_id` | `Tenant` | Finance → Core | Report owner is a team member |
| `Ticket` | `assigned_to` | `Tenant` | CRM → Core | Support agent is a team member |
| `TicketMessage` | `sender_tenant_id` | `Tenant` | CRM → Core | Message sender is a team member |
| `Deal` | `owner_tenant_id` | `Tenant` | CRM → Core | Sales rep is a team member |
| `CrmContact` | `owner_tenant_id` | `Tenant` | CRM → Core | Contact owner is a team member |
| `CrmActivity` | `tenant_id` | `Tenant` | CRM → Core | Activity performer is a team member |
| `InboxEmail` | `assigned_tenant_id` | `Tenant` | CRM → Core | Inbox email assignee is a team member |
| `DealNote` | `tenant_id` | `Tenant` | CRM → Core | Note author is a team member |
| `Payslip` | `pdf_file_id` | `File` | HR → Core | PDF stored in File system |
| `Document` | `current_file_id` | `File` | Projects → Core | Active version file |
| `DocumentVersion` | `file_id` | `File` | Projects → Core | Historical version file |
| `Employee` | `profile_photo_file_id` | `File` | HR → Core | Profile photo |
| `Company` | `logo_file_id` | `File` | Core → Core | Company logo |
| `Expense` | `receipt_file_id` | `File` | Finance → Core | Receipt scan |
| `EmployeeDocument` | `file_id` | `File` | HR → Core | Attached document file |
