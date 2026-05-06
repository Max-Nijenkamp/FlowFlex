---
tags: [flowflex, events, architecture, cross-module, event-bus]
domain: Platform
status: built
last_updated: 2026-05-06
---

# Cross-Module Event Map

Every cross-domain event in FlowFlex and which modules react to it. When both source and consuming modules are active for a tenant, these flows happen automatically via the [[Architecture|event bus]].

**Rule:** Modules NEVER call each other's internal classes. All cross-module communication goes through Laravel Events on the queue.

---

## Master Event Table

| Event | Source Module | Consuming Modules | What Happens |
|---|---|---|---|
| `EmployeeHired` | [[Recruitment & ATS]] | [[Onboarding]], [[Payroll]], [[Scheduling & Shifts]], [[Course Builder & LMS\|LMS]] | Starts onboarding flow, adds to payroll, adds to rota, assigns induction course |
| `OnboardingCompleted` | [[Onboarding]] | [[HR Compliance]], [[Course Builder & LMS\|LMS]] | Marks induction complete, triggers first compliance cert assignments |
| `EmployeeOffboarded` | [[Offboarding]] | [[Access & Permissions Audit\|IT/Access]], [[Payroll]], [[Asset Management]] | Revokes all access, runs final payroll, recalls assets |
| `CandidateHired` | [[Recruitment & ATS]] | [[Employee Profiles]], [[Onboarding]] | Creates employee record, starts onboarding flow |
| `TimeEntryApproved` | [[Time Tracking]] | [[Payroll]], [[Client Billing & Retainers]] | Adds to pay run, marks hours as billable |
| `LeaveApproved` | [[Leave Management]] | [[Payroll]], [[Scheduling & Shifts]] | Deducts from pay if unpaid, removes from rota |
| `ProjectMilestoneReached` | [[Project Planning]] | [[Invoicing]], [[Contact & Company Management\|CRM]] | Triggers milestone invoice, updates deal status |
| `TaskCompleted` | [[Task Management]] | [[Project Planning]], [[Invoicing]] | Updates project progress, triggers invoice if milestone-linked |
| `InvoiceOverdue` | [[Invoicing]] | [[Contact & Company Management\|CRM]], [[Notifications & Alerts\|Notifications]] | Creates follow-up task in CRM, alerts account manager |
| `InvoicePaid` | [[Invoicing]] | [[Bank Reconciliation]], [[Subscription & MRR Tracking]] | Auto-matches to bank transaction, updates MRR |
| `StockBelowReorderPoint` | [[Inventory Management]] | [[Purchasing & Procurement]] | Creates draft purchase order |
| `PurchaseOrderApproved` | [[Purchasing & Procurement]] | [[Accounts Payable & Receivable]] | Creates bill record, updates committed spend |
| `FieldJobCompleted` | [[Field Service Management]] | [[Invoicing]], [[Inventory Management]], [[Customer Support & Helpdesk\|CRM]] | Creates invoice, deducts parts used, closes support ticket |
| `TicketResolved` | [[Customer Support & Helpdesk]] | [[Email Marketing]], [[Contact & Company Management\|CRM]] | Sends CSAT survey, updates contact timeline |
| `OrderPlaced` | [[Order Management\|E-commerce]] | [[Inventory Management]], [[Invoicing\|Finance]], [[Contact & Company Management\|CRM]] | Deducts stock, records revenue, updates customer record |
| `CourseCompleted` | [[Course Builder & LMS]] | [[HR Compliance]], [[Performance & Reviews]] | Fulfils certification requirement, logs development activity |
| `CertificationExpired` | [[HR Compliance]] | [[Course Builder & LMS\|LMS]], [[Notifications & Alerts\|Notifications]] | Triggers renewal course assignment, notifies employee and manager |
| `ContractExpiring` | [[Contract Management]] | [[Contact & Company Management\|CRM]], [[Notifications & Alerts\|Notifications]] | Creates renewal task in CRM, alerts account manager |
| `RiskFlagRaised` | [[Risk Register]] | [[Legal Overview\|Legal]], [[Notifications & Alerts\|Notifications]] | Notifies risk owner, creates mitigation task |
| `BurnoutSignalDetected` | [[Employee Feedback]] | [[HR Overview\|HR]], [[Notifications & Alerts\|Notifications]] | Alerts HR manager and direct manager |
| `SaaSLicenceExpiring` | [[SaaS Spend Management]] | [[Invoicing\|Finance]], [[Notifications & Alerts\|Notifications]] | Alerts finance team, creates renewal task |
| `ShiftClockOut` | [[Scheduling & Shifts]] | [[Time Tracking]] | Creates time entry from clock-out |
| `ExpenseApproved` | [[Expense Management]] | [[Payroll]] | Adds reimbursement to next pay run |
| `PayRunProcessed` | [[Payroll]] | [[Notifications & Alerts\|Notifications]] | Notifies employees, triggers payslip generation |
| `ModuleActivated` | [[Module Billing Engine]] | All module panels | Shows panel to tenant users |
| `ModuleDeactivated` | [[Module Billing Engine]] | All module panels | Hides panel, retains data |

---

## Events by Phase

| Phase | Key Events Enabled |
|---|---|
| Phase 1 | `UserLoggedIn`, `ModuleActivated`, `ModuleDeactivated`, `TenantCreated` |
| Phase 2 | `EmployeeHired`, `TimeEntryApproved`, `LeaveApproved`, `TaskCompleted` |
| Phase 3 | `InvoiceOverdue`, `InvoicePaid`, `TicketResolved`, `CandidateHired` |
| Phase 4 | `StockBelowReorderPoint`, `OrderPlaced`, `FieldJobCompleted` |
| Phase 5 | `CourseCompleted`, `ContractExpiring`, `BurnoutSignalDetected`, `SaaSLicenceExpiring` |

---

## Event Implementation Pattern

```php
// Firing module — always queued
event(new TimeEntryApproved($entry));

// Consuming module A — queued listener
class AddTimeEntryToPayRun implements ShouldQueue
{
    public function handle(TimeEntryApproved $event): void
    {
        // Payroll logic here
    }
}

// Consuming module B — completely independent
class MarkTimeAsBillable implements ShouldQueue
{
    public function handle(TimeEntryApproved $event): void
    {
        // Client billing logic here
    }
}
```

Register all listeners in the module's `ServiceProvider`:

```php
// In HRServiceProvider
protected $listen = [
    TimeEntryApproved::class => [
        AddTimeEntryToPayRun::class,
    ],
    CandidateHired::class => [
        CreateEmployeeProfile::class,
        StartOnboardingFlow::class,
    ],
];
```

---

## Event Naming Convention

- **Past tense** — `EmployeeHired`, not `HireEmployee`
- **Descriptive** — `TimeEntryApproved`, not `TimeApproved`
- **Namespace** — `App\Modules\HR\Events\EmployeeHired`

---

## Related

- [[Architecture]]
- [[Module Development Checklist]]
- [[Build Order (Phases)]]
- [[Notifications & Alerts]]
