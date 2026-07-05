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

- [x] **Duplicate Detection** ([[../../domains/crm/contacts/features/duplicate-detection|spec]]) — hand-check: open `ContactResource` create/edit form and list at `/crm/contacts`; merge is a row action on the list/view.; inline validation on `email` blur/save; merge action modal (pick keep vs merge record) → confirm; CSV import shows a per-row dedup *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Lifecycle Stages** ([[../../domains/crm/contacts/features/lifecycle-stages|spec]]) — hand-check: open `ContactResource` list/edit at `/crm/contacts`.; inline stage change (optimistic select) → `ContactService::moveLifecycleStage`; tab switch re-filters the table. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] Gates: Pint + PHPStan + Pest green (212 tests), page sweep 27×200 *(AI ✓ 2026-07-05)*

### Pipeline Board — `crm.pipeline`

Build: `/flowflex:start crm.pipeline` · Done: `/flowflex:done crm.pipeline` · Spec: [[../../domains/crm/pipeline/_module|hub]] · Hard deps: crm.deals, core.billing, core.rbac

- [x] **Kanban Board** ([[../../domains/crm/pipeline/features/kanban-board|spec]]) — hand-check: open `PipelineBoardPage` at `/crm/pipeline` (custom Filament page + Livewire `PipelineBoard`).; drag card between columns → `DealService::moveToStage` → broadcast `DealStageChanged`; quick-add deal from column header; filter b *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Realtime Board Sync** ([[../../domains/crm/pipeline/features/realtime-sync|spec]]) — hand-check: open `PipelineBoardPage` at `/crm/pipeline` — Livewire listens on the per-company Reverb channel.; optimistic local move + `DealStageChanged` broadcast → remote boards patch the card into its new column without refresh. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] Gates: Pint + PHPStan + Pest green (212 tests), page sweep 27×200 *(AI ✓ 2026-07-05)*

### Activities — `crm.activities`

Build: `/flowflex:start crm.activities` · Done: `/flowflex:done crm.activities` · Spec: [[../../domains/crm/activities/_module|hub]] · Hard deps: crm.contacts, core.billing, core.rbac, core.notifications

- [x] **Task Reminders** ([[../../domains/crm/activities/features/task-reminders|spec]]) — hand-check: background — trigger it (`TaskReminderCommand` (scheduled) scans due/overdue activities; results surface as Core No), then check the visible result named in the spec *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] Gates: Pint + PHPStan + Pest green (212 tests), page sweep 27×200 *(AI ✓ 2026-07-05)*

### Deals — `crm.deals`

Build: `/flowflex:start crm.deals` · Done: `/flowflex:done crm.deals` · Spec: [[../../domains/crm/deals/_module|hub]] · Hard deps: crm.contacts, crm.pipeline, core.billing, core.rbac

- [x] **Invoice Creation from Won Deal** ([[../../domains/crm/deals/features/invoice-creation|spec]]) — hand-check: open `CreateInvoiceAction` on the Deal view page at `/crm/deals`.; manual action → confirm modal → Finance creates a draft invoice (deep-link to `/finance/invoices` on success). Also fires automati *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Won/Lost Flow** ([[../../domains/crm/deals/features/won-lost-flow|spec]]) — hand-check: open `CloseDealAction` modal on the `DealResource` view/edit page at `/crm/deals`.; modal action → outcome select → conditional fields → confirm; on won, `CreateInvoiceAction` becomes visible on the view page. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] Gates: Pint + PHPStan + Pest green (212 tests), page sweep 27×200 *(AI ✓ 2026-07-05)*

## finance

### General Ledger — `finance.ledger`

Build: `/flowflex:start finance.ledger` · Done: `/flowflex:done finance.ledger` · Spec: [[../../domains/finance/general-ledger/_module|hub]] · Hard deps: core.billing, core.rbac, core.settings

- [x] **Feature — Fiscal Period Lock** ([[../../domains/finance/general-ledger/features/fiscal-period-lock|spec]]) — hand-check: open `FiscalPeriodResource` — `/finance/ledger/periods`; close a period (locks it); reopen a closed period (owner-level, audited). *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Feature — Trial Balance** ([[../../domains/finance/general-ledger/features/trial-balance|spec]]) — hand-check: open `TrialBalancePage` — `/finance/ledger/trial-balance`; pick a from/to range; click an account row to drill down to its `fin_journal_lines`. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] Gates: Pint + PHPStan + Pest green (212 tests), page sweep 27×200 *(AI ✓ 2026-07-05)*

### Bank Accounts — `finance.bank`

Build: `/flowflex:start finance.bank` · Done: `/flowflex:done finance.bank` · Spec: [[../../domains/finance/bank-accounts/_module|hub]] · Hard deps: finance.ledger, core.billing, core.rbac, core.files

- [x] **Feature — CSV Statement Import** ([[../../domains/finance/bank-accounts/features/csv-import|spec]]) — hand-check: open "Import statement" under `/finance/bank/{account}/import`; file upload (max 10MB, `text/csv`), column mapping, date-format pick, submit → queued job; malformed rows land in an error report, *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Feature — Reconciliation** ([[../../domains/finance/bank-accounts/features/reconciliation|spec]]) — hand-check: open "Reconciliation" under `/finance/bank/{account}/reconcile`; `suggestMatches` exact-amount within a ±5-day window *(assumed)*, click to link a txn to a journal line, unreconcile, `balanceComp *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] Gates: Pint + PHPStan + Pest green (212 tests), page sweep 27×200 *(AI ✓ 2026-07-05)*

### Expenses — `finance.expenses`

Build: `/flowflex:start finance.expenses` · Done: `/flowflex:done finance.expenses` · Spec: [[../../domains/finance/expenses/_module|hub]] · Hard deps: finance.ledger, core.billing, core.rbac, core.files, core.notifications

- [x] **Feature — Approval Workflow (State Machine)** ([[../../domains/finance/expenses/features/approval-workflow|spec]]) — hand-check: open `ExpenseResource` — `/finance/expenses`; submit (draft→submitted); approve/reject (submitted→approved/rejected, reason required on reject); reimburse (approved→reimbursed) *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Feature — Expense Policy** ([[../../domains/finance/expenses/features/expense-policy|spec]]) — hand-check: open `fin_expense_categories` CRUD — `/finance/expenses/categories`; create/edit a category, set its transaction limit and GL posting target. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Feature — Expense Reports** ([[../../domains/finance/expenses/features/expense-reports|spec]]) — hand-check: open `ExpenseReportResource` — `/finance/expenses/reports`; create a report, attach expenses, bulk-submit (cascades submit to member drafts), export CSV. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] Gates: Pint + PHPStan + Pest green (212 tests), page sweep 27×200 *(AI ✓ 2026-07-05)*

### Invoicing — `finance.invoicing`

Build: `/flowflex:start finance.invoicing` · Done: `/flowflex:done finance.invoicing` · Spec: [[../../domains/finance/invoicing/_module|hub]] · Hard deps: finance.ledger, core.billing, core.rbac, core.settings, foundation.queues

- [x] **Feature — Invoice Lifecycle (State Machine)** ([[../../domains/finance/invoicing/features/invoice-lifecycle|spec]]) — hand-check: open `InvoiceResource` — list + edit (`/finance/invoices`). Header actions: Send, Record payment, Void.; Send → confirm modal → assigns number + queues PDF/mail (optimistic badge flip); Void → confirm + reason; post-`paid` rows are rea *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Feature — Payments** ([[../../domains/finance/invoicing/features/payments|spec]]) — hand-check: open record-payment slide-over launched from `InvoiceResource` (+ payments relation-manager) — `/finance/invoices/{; enter amount ≤ open balance → submit → state transition + journal post; relation-manager lists prior payments inline *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Feature — Recurring Invoices** ([[../../domains/finance/invoicing/features/recurring-invoices|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] Gates: Pint + PHPStan + Pest green (212 tests), page sweep 27×200 *(AI ✓ 2026-07-05)*

## hr

### Employee Profiles — `hr.employee-profiles`

Build: `/flowflex:start hr.employee-profiles` · Done: `/flowflex:done hr.employee-profiles` · Spec: [[../../domains/hr/employee-profiles/_module|hub]] · Hard deps: core.billing, core.rbac, core.files

- [x] **Document Storage** ([[../../domains/hr/employee-profiles/features/document-storage|spec]]) — hand-check: open "Documents" tab on the Employee view (`/hr/employees/{id}` → Documents tab); upload a document, preview or download an existing one. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Employee Record** ([[../../domains/hr/employee-profiles/features/employee-record|spec]]) — hand-check: open "Employees" (`/hr/employees`); browse/search/filter the roster; create or edit an employee; open a row to the view page. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Employment Lifecycle (State Machine)** ([[../../domains/hr/employee-profiles/features/employment-lifecycle|spec]]) — hand-check: open "Employee Record" (`/hr/employees/{id}`); trigger a legal transition via a modal action. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Manager Hierarchy** ([[../../domains/hr/employee-profiles/features/manager-hierarchy|spec]]) — hand-check: open Employee form (`/hr/employees/{id}/edit`); pick or change an employee's manager. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Offboarding** ([[../../domains/hr/employee-profiles/features/offboarding|spec]]) — hand-check: open Offboard action on the Employee view (`/hr/employees/{id}`); submit the termination form to offboard the employee. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] Gates: Pint + PHPStan + Pest green (212 tests), page sweep 27×200 *(AI ✓ 2026-07-05)*

### Onboarding — `hr.onboarding`

Build: `/flowflex:start hr.onboarding` · Done: `/flowflex:done hr.onboarding` · Spec: [[../../domains/hr/onboarding/_module|hub]] · Hard deps: hr.profiles, core.billing, core.rbac, core.notifications

- [x] **Document Collection** ([[../../domains/hr/onboarding/features/document-collection|spec]]) — hand-check: open "Document Collection" (`/hr/onboarding/{plan}/documents`); upload a document or mark it collected. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Equipment Requests** ([[../../domains/hr/onboarding/features/equipment-requests|spec]]) — hand-check: open "Equipment Requests" (`/hr/onboarding/equipment`); create/view an equipment request against a plan. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Milestone Check-ins** ([[../../domains/hr/onboarding/features/milestone-checkins|spec]]) — hand-check: open "Milestone Check-ins" (`/hr/onboarding/milestones`); review upcoming/overdue check-ins; drill into a check-in. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Onboarding Templates** ([[../../domains/hr/onboarding/features/onboarding-templates|spec]]) — hand-check: open "Onboarding Templates" (`/hr/onboarding-templates`); create/edit templates; add/reorder tasks in the repeater with an `assigned_role` per task. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Plan Generation on Hire** ([[../../domains/hr/onboarding/features/plan-generation-on-hire|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Progress Dashboard** ([[../../domains/hr/onboarding/features/progress-dashboard|spec]]) — hand-check: open "Onboarding Dashboard" (`/hr/onboarding`); scan active onboardings; drill into a plan. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Task Checklists** ([[../../domains/hr/onboarding/features/task-checklists|spec]]) — hand-check: open "Onboarding Plan" (`/hr/onboarding/{plan}`); HR completes or skips a task; employee-role tasks completed via self-service when active. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Welcome Email** ([[../../domains/hr/onboarding/features/welcome-email|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] Gates: Pint + PHPStan + Pest green (212 tests), page sweep 27×200 *(AI ✓ 2026-07-05)*

### Leave Management — `hr.leave-management`

Build: `/flowflex:start hr.leave-management` · Done: `/flowflex:done hr.leave-management` · Spec: [[../../domains/hr/leave-management/_module|hub]] · Hard deps: hr.profiles, core.billing, hr.leave, core.rbac, core.notifications

- [x] **Feature — Accrual & Carry-Over Jobs** ([[../../domains/hr/leave-management/features/accrual-jobs|spec]]) — hand-check: background — trigger it (runs in the background), then check the visible result named in the spec *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Feature — Leave Balances** ([[../../domains/hr/leave-management/features/leave-balances|spec]]) — hand-check: open "Leave Balances" (`/hr/leave-balances`); filter and inspect balances; drill into an employee's per-type ledger; managers view team balances. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Feature — Leave Request Workflow & Approval** ([[../../domains/hr/leave-management/features/leave-request-workflow|spec]]) — hand-check: open "Leave Requests" (`/hr/leave-requests`); submit a request (draft → submitted); approver approves or rejects (with reason) from the Pending tab; cancel own request. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Feature — Leave Types** ([[../../domains/hr/leave-management/features/leave-types|spec]]) — hand-check: open "Leave Types" (`/hr/leave-types`); create/edit a leave type; set accrual days, carry-over cap, `requires_approval` toggle, and display color. *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] **Feature — Team Calendar & Overlap Detection** ([[../../domains/hr/leave-management/features/team-calendar|spec]]) — hand-check: open "Team Calendar" (`/hr/leave-calendar`); switch month/week; filter by team; hover an event for detail; overlap warning surfaces when a new request overlaps existing approv *(AI ✓ 2026-07-05 — hand-check pending)*
- [x] Gates: Pint + PHPStan + Pest green (212 tests), page sweep 27×200 *(AI ✓ 2026-07-05)*
