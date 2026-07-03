---
type: roadmap-phase
color: "#F97316"
updated: 2026-07-03
---

# Phase 2 — First business slice (v1-core)

The smallest sellable slice: HR profiles/leave/payroll-core, Finance ledger/invoicing, CRM contacts/deals/pipeline. After this phase a real company can run on FlowFlex.

**11 modules · 35 features.** Work top-to-bottom; within a domain, modules are ordered fewest-dependencies-first. Tick a feature only after BOTH gates pass: AI gate (spec Test Checklist covered by green Pest tests + `/flowflex:verify`) AND your hand check.

## crm

### Contacts — `crm.contacts`

Build: `/flowflex:start crm.contacts` · Done: `/flowflex:done crm.contacts` · Spec: [[../../domains/crm/contacts/_module|hub]] · Hard deps: core.billing, core.rbac

- [ ] **Duplicate Detection** ([[../../domains/crm/contacts/features/duplicate-detection|spec]]) — hand-check: open `ContactResource` create/edit form and list at `/crm/contacts`; merge is a row action on the list/view.; inline validation on `email` blur/save; merge action modal (pick keep vs merge record) → confirm; CSV import shows a per-row dedup
- [ ] **Lifecycle Stages** ([[../../domains/crm/contacts/features/lifecycle-stages|spec]]) — hand-check: open `ContactResource` list/edit at `/crm/contacts`.; inline stage change (optimistic select) → `ContactService::moveLifecycleStage`; tab switch re-filters the table.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Pipeline Board — `crm.pipeline`

Build: `/flowflex:start crm.pipeline` · Done: `/flowflex:done crm.pipeline` · Spec: [[../../domains/crm/pipeline/_module|hub]] · Hard deps: crm.deals, core.billing, core.rbac

- [ ] **Kanban Board** ([[../../domains/crm/pipeline/features/kanban-board|spec]]) — hand-check: open `PipelineBoardPage` at `/crm/pipeline` (custom Filament page + Livewire `PipelineBoard`).; drag card between columns → `DealService::moveToStage` → broadcast `DealStageChanged`; quick-add deal from column header; filter b
- [ ] **Realtime Board Sync** ([[../../domains/crm/pipeline/features/realtime-sync|spec]]) — hand-check: open `PipelineBoardPage` at `/crm/pipeline` — Livewire listens on the per-company Reverb channel.; optimistic local move + `DealStageChanged` broadcast → remote boards patch the card into its new column without refresh.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Activities — `crm.activities`

Build: `/flowflex:start crm.activities` · Done: `/flowflex:done crm.activities` · Spec: [[../../domains/crm/activities/_module|hub]] · Hard deps: crm.contacts, core.billing, core.rbac, core.notifications

- [ ] **Task Reminders** ([[../../domains/crm/activities/features/task-reminders|spec]]) — hand-check: background — trigger it (`TaskReminderCommand` (scheduled) scans due/overdue activities; results surface as Core No), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Deals — `crm.deals`

Build: `/flowflex:start crm.deals` · Done: `/flowflex:done crm.deals` · Spec: [[../../domains/crm/deals/_module|hub]] · Hard deps: crm.contacts, crm.pipeline, core.billing, core.rbac

- [ ] **Invoice Creation from Won Deal** ([[../../domains/crm/deals/features/invoice-creation|spec]]) — hand-check: open `CreateInvoiceAction` on the Deal view page at `/crm/deals`.; manual action → confirm modal → Finance creates a draft invoice (deep-link to `/finance/invoices` on success). Also fires automati
- [ ] **Won/Lost Flow** ([[../../domains/crm/deals/features/won-lost-flow|spec]]) — hand-check: open `CloseDealAction` modal on the `DealResource` view/edit page at `/crm/deals`.; modal action → outcome select → conditional fields → confirm; on won, `CreateInvoiceAction` becomes visible on the view page.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## finance

### General Ledger — `finance.ledger`

Build: `/flowflex:start finance.ledger` · Done: `/flowflex:done finance.ledger` · Spec: [[../../domains/finance/general-ledger/_module|hub]] · Hard deps: core.billing, core.rbac, core.settings

- [ ] **Feature — Fiscal Period Lock** ([[../../domains/finance/general-ledger/features/fiscal-period-lock|spec]]) — hand-check: open `FiscalPeriodResource` — `/finance/ledger/periods`; close a period (locks it); reopen a closed period (owner-level, audited).
- [ ] **Feature — Trial Balance** ([[../../domains/finance/general-ledger/features/trial-balance|spec]]) — hand-check: open `TrialBalancePage` — `/finance/ledger/trial-balance`; pick a from/to range; click an account row to drill down to its `fin_journal_lines`.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Bank Accounts — `finance.bank`

Build: `/flowflex:start finance.bank` · Done: `/flowflex:done finance.bank` · Spec: [[../../domains/finance/bank-accounts/_module|hub]] · Hard deps: finance.ledger, core.billing, core.rbac, core.files

- [ ] **Feature — CSV Statement Import** ([[../../domains/finance/bank-accounts/features/csv-import|spec]]) — hand-check: open "Import statement" under `/finance/bank/{account}/import`; file upload (max 10MB, `text/csv`), column mapping, date-format pick, submit → queued job; malformed rows land in an error report,
- [ ] **Feature — Reconciliation** ([[../../domains/finance/bank-accounts/features/reconciliation|spec]]) — hand-check: open "Reconciliation" under `/finance/bank/{account}/reconcile`; `suggestMatches` exact-amount within a ±5-day window *(assumed)*, click to link a txn to a journal line, unreconcile, `balanceComp
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Expenses — `finance.expenses`

Build: `/flowflex:start finance.expenses` · Done: `/flowflex:done finance.expenses` · Spec: [[../../domains/finance/expenses/_module|hub]] · Hard deps: finance.ledger, core.billing, core.rbac, core.files, core.notifications

- [ ] **Feature — Approval Workflow (State Machine)** ([[../../domains/finance/expenses/features/approval-workflow|spec]]) — hand-check: open `ExpenseResource` — `/finance/expenses`; submit (draft→submitted); approve/reject (submitted→approved/rejected, reason required on reject); reimburse (approved→reimbursed)
- [ ] **Feature — Expense Policy** ([[../../domains/finance/expenses/features/expense-policy|spec]]) — hand-check: open `fin_expense_categories` CRUD — `/finance/expenses/categories`; create/edit a category, set its transaction limit and GL posting target.
- [ ] **Feature — Expense Reports** ([[../../domains/finance/expenses/features/expense-reports|spec]]) — hand-check: open `ExpenseReportResource` — `/finance/expenses/reports`; create a report, attach expenses, bulk-submit (cascades submit to member drafts), export CSV.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Invoicing — `finance.invoicing`

Build: `/flowflex:start finance.invoicing` · Done: `/flowflex:done finance.invoicing` · Spec: [[../../domains/finance/invoicing/_module|hub]] · Hard deps: finance.ledger, core.billing, core.rbac, core.settings, foundation.queues

- [ ] **Feature — Invoice Lifecycle (State Machine)** ([[../../domains/finance/invoicing/features/invoice-lifecycle|spec]]) — hand-check: open `InvoiceResource` — list + edit (`/finance/invoices`). Header actions: Send, Record payment, Void.; Send → confirm modal → assigns number + queues PDF/mail (optimistic badge flip); Void → confirm + reason; post-`paid` rows are rea
- [ ] **Feature — Payments** ([[../../domains/finance/invoicing/features/payments|spec]]) — hand-check: open record-payment slide-over launched from `InvoiceResource` (+ payments relation-manager) — `/finance/invoices/{; enter amount ≤ open balance → submit → state transition + journal post; relation-manager lists prior payments inline
- [ ] **Feature — Recurring Invoices** ([[../../domains/finance/invoicing/features/recurring-invoices|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

## hr

### Employee Profiles — `hr.employee-profiles`

Build: `/flowflex:start hr.employee-profiles` · Done: `/flowflex:done hr.employee-profiles` · Spec: [[../../domains/hr/employee-profiles/_module|hub]] · Hard deps: core.billing, core.rbac, core.files

- [ ] **Document Storage** ([[../../domains/hr/employee-profiles/features/document-storage|spec]]) — hand-check: open "Documents" tab on the Employee view (`/hr/employees/{id}` → Documents tab); upload a document, preview or download an existing one.
- [ ] **Employee Record** ([[../../domains/hr/employee-profiles/features/employee-record|spec]]) — hand-check: open "Employees" (`/hr/employees`); browse/search/filter the roster; create or edit an employee; open a row to the view page.
- [ ] **Employment Lifecycle (State Machine)** ([[../../domains/hr/employee-profiles/features/employment-lifecycle|spec]]) — hand-check: open "Employee Record" (`/hr/employees/{id}`); trigger a legal transition via a modal action.
- [ ] **Manager Hierarchy** ([[../../domains/hr/employee-profiles/features/manager-hierarchy|spec]]) — hand-check: open Employee form (`/hr/employees/{id}/edit`); pick or change an employee's manager.
- [ ] **Offboarding** ([[../../domains/hr/employee-profiles/features/offboarding|spec]]) — hand-check: open Offboard action on the Employee view (`/hr/employees/{id}`); submit the termination form to offboard the employee.
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Onboarding — `hr.onboarding`

Build: `/flowflex:start hr.onboarding` · Done: `/flowflex:done hr.onboarding` · Spec: [[../../domains/hr/onboarding/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac, core.notifications

- [ ] **Document Collection** ([[../../domains/hr/onboarding/features/document-collection|spec]]) — hand-check: open "Document Collection" (`/hr/onboarding/{plan}/documents`); upload a document or mark it collected.
- [ ] **Equipment Requests** ([[../../domains/hr/onboarding/features/equipment-requests|spec]]) — hand-check: open "Equipment Requests" (`/hr/onboarding/equipment`); create/view an equipment request against a plan.
- [ ] **Milestone Check-ins** ([[../../domains/hr/onboarding/features/milestone-checkins|spec]]) — hand-check: open "Milestone Check-ins" (`/hr/onboarding/milestones`); review upcoming/overdue check-ins; drill into a check-in.
- [ ] **Onboarding Templates** ([[../../domains/hr/onboarding/features/onboarding-templates|spec]]) — hand-check: open "Onboarding Templates" (`/hr/onboarding-templates`); create/edit templates; add/reorder tasks in the repeater with an `assigned_role` per task.
- [ ] **Plan Generation on Hire** ([[../../domains/hr/onboarding/features/plan-generation-on-hire|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Progress Dashboard** ([[../../domains/hr/onboarding/features/progress-dashboard|spec]]) — hand-check: open "Onboarding Dashboard" (`/hr/onboarding`); scan active onboardings; drill into a plan.
- [ ] **Task Checklists** ([[../../domains/hr/onboarding/features/task-checklists|spec]]) — hand-check: open "Onboarding Plan" (`/hr/onboarding/{plan}`); HR completes or skips a task; employee-role tasks completed via self-service when active.
- [ ] **Welcome Email** ([[../../domains/hr/onboarding/features/welcome-email|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean

### Leave Management — `hr.leave-management`

Build: `/flowflex:start hr.leave-management` · Done: `/flowflex:done hr.leave-management` · Spec: [[../../domains/hr/leave-management/_module|hub]] · Hard deps: hr.profiles, core.billing, hr.leave, core.rbac, core.notifications

- [ ] **Feature — Accrual & Carry-Over Jobs** ([[../../domains/hr/leave-management/features/accrual-jobs|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec
- [ ] **Feature — Leave Balances** ([[../../domains/hr/leave-management/features/leave-balances|spec]]) — hand-check: open "Leave Balances" (`/hr/leave-balances`); filter and inspect balances; drill into an employee's per-type ledger; managers view team balances.
- [ ] **Feature — Leave Request Workflow & Approval** ([[../../domains/hr/leave-management/features/leave-request-workflow|spec]]) — hand-check: open "Leave Requests" (`/hr/leave-requests`); submit a request (draft → submitted); approver approves or rejects (with reason) from the Pending tab; cancel own request.
- [ ] **Feature — Leave Types** ([[../../domains/hr/leave-management/features/leave-types|spec]]) — hand-check: open "Leave Types" (`/hr/leave-types`); create/edit a leave type; set accrual days, carry-over cap, `requires_approval` toggle, and display color.
- [ ] **Feature — Team Calendar & Overlap Detection** ([[../../domains/hr/leave-management/features/team-calendar|spec]]) — hand-check: open "Team Calendar" (`/hr/leave-calendar`); switch month/week; filter by team; hover an event for detail; overlap warning surfaces when a new request overlaps existing approv
- [ ] Gates: Pint + PHPStan + Pest green, spec Test Checklist covered, `/flowflex:verify` clean
