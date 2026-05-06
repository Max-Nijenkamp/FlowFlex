---
tags: [flowflex, architecture, modular-monolith, events, phase/1]
domain: Platform
status: built
last_updated: 2026-05-06
---

# Architecture

FlowFlex is built as a **modular monolith** — a single Laravel application where modules are fully isolated internally and communicate only through events. This is the backbone of everything.

## Modular Monolith Structure

```
app/
  Modules/
    Core/           ← always loaded, foundation for everything
    HR/
    Finance/
    Projects/
    CRM/
    Marketing/
    Operations/
    Analytics/
    IT/
    Legal/
    Ecommerce/
    Communications/
    Learning/
```

### Module Internal Structure

Every module follows this exact folder layout:

```
Modules/HR/
  Models/
  Filament/
    Pages/
    Resources/
    Widgets/
  Services/
  Events/
  Listeners/
  Policies/
  Providers/
    HRServiceProvider.php
  database/
    migrations/
    seeders/
  config/
    hr.php
  routes/
    api.php
```

## The Core Module (Always Active)

The Core module is **never optional**. It provides the foundation that all other modules inherit:

- Multi-tenant workspace management (every other module inherits tenant scoping automatically)
- Authentication & identity (email, OAuth, SAML, 2FA, magic link)
- RBAC via Spatie Permission (roles, permissions, module-level access, record-level policies)
- Module registry (which modules are active per tenant)
- Module billing engine (Stripe metering, plan management, usage tracking)
- Notification hub (in-app, email, SMS, webhook, Slack/Teams)
- API gateway (REST API per module, Webhook management, API key management)
- Audit log (all activity, immutable, filterable)
- File storage abstraction (S3/R2 behind a unified `Storage` facade)
- Event bus (cross-module communication via Laravel Events)

## Cross-Module Communication Rules

**Rule: Modules NEVER import each other's internal classes directly.**

Modules communicate only via:
1. **Laravel Events** — a module fires an event; other modules listen
2. **The Core data layer** — shared models like `User`, `Tenant`, `File` live in Core
3. **Service contracts** — if Module A needs data from Module B, it calls a registered interface, not a concrete class

### Example of Correct Cross-Module Flow

```php
// In TimeTracking module — fires event when entry approved
event(new TimeEntryApproved($entry));

// In Payroll module — listens and reacts
class AddTimeEntryToPayRun implements ShouldQueue {
    public function handle(TimeEntryApproved $event): void { ... }
}

// In ClientBilling module — also listens independently
class MarkTimeAsBillable implements ShouldQueue {
    public function handle(TimeEntryApproved $event): void { ... }
}
```

Both [[Payroll]] and [[Client Billing & Retainers]] listen to `TimeEntryApproved` completely independently — neither knows about the other.

## Module Registry & Billing

- A `modules` table tracks which modules each tenant has active
- A `module_usage_events` table records metered events per tenant
- Stripe usage records are synced from this table via a scheduled job
- When a tenant toggles a module off, its Filament panel resources are hidden; data is retained
- Module pricing is defined in config, not hardcoded in UI

## Module Dependency Map

```
Core Platform (always active)
├── All modules inherit: Auth, RBAC, Tenancy, Notifications, Files, API

HR Domain
├── Employee Profiles (standalone)
├── Onboarding → requires: Employee Profiles
│   └── benefits from: LMS (training delivery), IT (access provisioning)
├── Offboarding → requires: Employee Profiles
│   └── benefits from: IT (access revoke), Asset Management (asset recall)
├── Leave Management → requires: Employee Profiles
│   └── benefits from: Payroll (deductions), Scheduling (rota updates)
├── Payroll → requires: Employee Profiles
│   └── benefits from: Time Tracking (auto-fill hours), Leave (deductions), Expenses
├── Performance → requires: Employee Profiles
│   └── benefits from: LMS (development plans link to courses)
├── Recruitment → standalone, but on hire integrates with Employee Profiles + Onboarding
├── Scheduling → requires: Employee Profiles
│   └── benefits from: Time Tracking (clock-in creates time entry), POS (clock-in integration)
├── Benefits → requires: Employee Profiles, Payroll (for deductions)
├── Feedback & Engagement → requires: Employee Profiles
└── HR Compliance → requires: Employee Profiles
    └── benefits from: LMS (training delivery), Legal/Policy (acknowledgement)

Finance Domain
├── Invoicing → standalone
├── Expenses → benefits from: Employee Profiles, Payroll (reimbursement)
├── AP/AR → standalone
├── Bank Reconciliation → requires: Invoicing (to match against)
├── Budgeting → benefits from: AP/AR (actual spend), Payroll (salary costs)
├── Financial Reporting → benefits from: all Finance modules (data sources)
├── Client Billing → requires: Invoicing, Time Tracking, CRM (client records)
├── Tax/VAT → benefits from: Invoicing, AP/AR
├── Fixed Assets → benefits from: Operations/Asset Management
└── MRR Tracking → requires: Invoicing, CRM

Projects Domain
├── Task Management → standalone
├── Project Planning → benefits from: Task Management
├── Time Tracking → benefits from: Task Management, Projects
├── Document Management → standalone
├── Document Approvals → requires: Document Management
├── Knowledge Base → standalone
├── Collaboration → benefits from: Task Management
├── Resource Planning → requires: Employee Profiles, Task Management
└── Agile/Sprint → requires: Task Management
```

## Why Modular Monolith

The monolith approach means:
- **Easy to develop** — no distributed systems complexity during early phase
- **Easy to debug** — everything in one process, one log stream, one deployment
- **Can split later** — each module is already isolated enough to become a microservice when scale demands it
- **Shared infrastructure** — one database, one cache, one queue by default

## Related

- [[Tech Stack]]
- [[Multi-Tenancy]]
- [[Module Development Checklist]]
- [[Naming Conventions]]
- [[Security Rules]]
- [[Performance Rules]]
