---
type: architecture
category: filament
pattern-key: panels
status: stable
last-reviewed: 2026-06-10
color: "#A78BFA"
---

# Domain Panel Reference

> [!warning] Authoritative infra source moved
> Verified infrastructure facts now live in [[../infrastructure/_moc|Infrastructure]] — only **2 panels** exist today (Admin+App); this 19/21-panel map is the aspirational target. Details on this page may predate the 2026-06-20 rebuild — trust the linked note on any conflict.

> [!note] Artifact source of truth moved (2026-07-03)
> The current per-module artifact map is the generated [[../_meta/artifact-registry|artifact registry]] — one row per Filament artifact scraped from every module's `## Filament Artifacts` section, with kind, blueprint/tweaks, and permission. Keep using this page as a navigation overview only; on any conflict, the registry (and each module's `architecture.md`) wins.


Per-domain breakdown: panel styling, navigation structure, Filament resource type (CRUD vs custom page), key permissions, and caching notes. Read alongside [[architecture/filament-patterns]].

---

## Panel Styling Quick Reference

**21 Filament panels total**: `/admin` + `/app` + 19 domain panels. Two domains share a host panel (see [[build/decisions/decision-2026-06-01-panel-consolidation]]):
- **Procurement** hosted in the **Operations** panel (`/operations`)
- **Customer Success** hosted in the **CRM** panel (`/crm`)

| Domain | Panel ID | Path | Filament Color | Tailwind Approximate |
|---|---|---|---|---|
| Admin (staff) | `admin` | `/admin` | Gray | `#6B7280` |
| App (workspace) | `app` | `/app` | Slate | `#64748B` |
| Foundation | — | (scaffold) | — | — |
| HR & People | `hr` | `/hr` | Violet | `#7C3AED` |
| Finance & Accounting | `finance` | `/finance` | Emerald | `#059669` |
| CRM & Sales (+ Customer Success) | `crm` | `/crm` | Rose | `#E11D48` |
| Projects & Work | `projects` | `/projects` | Indigo | `#4338CA` |
| Communications | `comms` | `/comms` | Blue | `#2563EB` |
| Support & Help Desk | `support` | `/support` | Orange | `#EA580C` |
| Document Management | `dms` | `/dms` | Slate | `#475569` |
| Marketing | `marketing` | `/marketing` | Pink | `#DB2777` |
| Operations (+ Procurement) | `operations` | `/operations` | Orange | `#C2410C` |
| Analytics & BI | `analytics` | `/analytics` | Sky | `#0284C7` |
| IT & Security | `it` | `/it` | Cyan | `#0891B2` |
| Legal & Compliance | `legal` | `/legal` | Amber | `#D97706` |
| E-commerce | `ecommerce` | `/ecommerce` | Teal | `#0D9488` |
| Learning & Dev | `lms` | `/lms` | Green | `#16A34A` |
| AI & Automation | `ai` | `/ai` | Indigo | `#6366F1` |
| Workplace | `workplace` | `/workplace` | Lime | `#65A30D` |
| Events | `events` | `/events` | Rose | `#BE185D` |

Panel primary color defined in `PanelProvider` via `->colors(['primary' => Color::hex('#7C3AED')])`.

**Merged domains** (no standalone panel — hosted in the parent):
- Procurement → `/operations` (nav groups: Requisitions, Sourcing, Approvals)
- Customer Success → `/crm` (nav group: Customer Success)

---

## Foundation — No Panel (Scaffold)

Foundation is infrastructure — no Filament resources for company users.

**Admin panel resources** (FlowFlex staff only):
- `CompanyResource` — CRUD — create/manage tenant companies
- `AdminUserResource` — CRUD — manage FlowFlex staff accounts
- `ImpersonationPage` — custom page — impersonate company users

---

## Core Platform (`/app`)

### Custom Pages (not standard CRUD)
| Page | Type | Why Custom |
|---|---|---|
| `SetupWizardPage` | Multi-step wizard | Non-linear flow with progress state |
| `ModuleMarketplacePage` | Grid layout | Card-based activation UI, not a table |
| `CompanySettingsPage` | Tabbed form | Multiple setting groups in one screen |
| `DataExportPage` | Action-only | Triggers background job, shows download link |
| `DsarRequestResource` (view) | Custom view page | Timeline of DSAR steps |

### Standard CRUD Resources
`AuditLogResource`, `NotificationResource`, `WebhookEndpointResource`, `ApiClientResource`, `DataImportResource`, `RoleResource`, `UserResource`

### Key Permissions
```
core.audit.view-any
core.audit.view
core.settings.view
core.settings.update
core.billing.view
core.billing.manage
core.marketplace.activate
core.marketplace.deactivate
core.rbac.manage-roles
core.rbac.manage-users
core.webhooks.manage
core.api.manage
core.privacy.manage-dsars
access.app-panel
```

### Caching
- `company:{id}:modules` — active module list (5 min TTL)
- Settings: cached by `spatie/laravel-settings`

---

## HR & People (`/hr`, Violet)

### Custom Pages
| Page | Type | Why Custom |
|---|---|---|
| `OrgChartPage` | D3.js tree in Blade | Hierarchical tree requires custom rendering |
| `LeaveCalendarPage` | `saade/filament-fullcalendar` | Calendar layout — not a table |
| `ShiftSchedulePage` | Calendar + drag-and-drop | Weekly schedule grid with drag assignment |
| `HrAnalyticsDashboard` | Dashboard with charts | `leandrocfe/filament-apex-charts` widgets |
| `SelfServiceDashboardPage` | Custom overview | Employee sees own data only — restricted view |

### Standard CRUD Resources
`EmployeeResource`, `LeaveRequestResource`, `LeaveBalanceResource`, `LeaveTypeResource`, `OnboardingResource`, `OnboardingTemplateResource`, `PayrollRunResource`, `PayslipResource`, `JobRequisitionResource`, `ApplicantResource`, `ReviewCycleResource`, `CompensationBandResource`, `BenefitResource`

### Resource-Specific Notes
- `EmployeeResource` view page: tabs (Personal, Employment, Documents, History, Leave Balance)
- `ApplicantResource`: list has Kanban sub-view (custom page within resource context)
- `PayslipResource`: read-only, no create/edit — payslips generated by payroll run job
- `SelfServiceDashboardPage`: `canAccess()` returns true for all authenticated HR panel users, but all queries are scoped to `Auth::user()->employee`

### Key Permissions
```
hr.employees.view-any, hr.employees.view, hr.employees.create, hr.employees.update, hr.employees.delete
hr.leave.view-any, hr.leave.view, hr.leave.create, hr.leave.approve, hr.leave.reject
hr.payroll.view-any, hr.payroll.run, hr.payroll.approve
hr.onboarding.manage
hr.org.view
hr.self-service.view          ← default for every employee
hr.recruitment.view-any, hr.recruitment.create, hr.recruitment.manage
hr.performance.manage
hr.analytics.view
access.hr-panel
```

### Caching
- `company:{id}:hr:headcount` — 15 min TTL, invalidate on hire/termination

---

## Finance & Accounting (`/finance`, Emerald)

### Custom Pages
| Page | Type | Why Custom |
|---|---|---|
| `TrialBalancePage` | Date range + table | Not a resource — aggregates GL |
| `FinanceDashboardPage` | Dashboard with charts | Revenue chart, outstanding AR widget, expense breakdown |
| `BankReconciliationPage` | Two-panel matcher | Side-by-side bank transactions vs GL entries |
| `ProfitLossPage` | Report page | Structured financial statement layout |
| `BalanceSheetPage` | Report page | Asset/liability/equity structured view |

### Standard CRUD Resources
`InvoiceResource`, `ExpenseResource`, `ExpenseCategoryResource`, `ExpenseReportResource`, `ChartOfAccountsResource`, `JournalEntryResource`, `BankAccountResource`, `BankTransactionResource`, `BudgetResource`

### Resource-Specific Notes
- `InvoiceResource` view page: invoice PDF preview panel + payment history + action buttons (Send, Record Payment, Mark Void, Create Credit Note)
- `InvoiceResource` status column uses `spatie/laravel-model-states` — status badge with colour per state
- `JournalEntryResource`: auto-posted entries (from invoice/payroll) are read-only; only manual entries are editable
- All amounts displayed in company currency from Company Settings

### Key Permissions
```
finance.invoices.view-any, finance.invoices.view, finance.invoices.create, finance.invoices.update, finance.invoices.send, finance.invoices.void
finance.invoices.approve                    ← for high-value invoice approval workflow
finance.expenses.view-any, finance.expenses.view, finance.expenses.create, finance.expenses.approve
finance.ledger.view, finance.ledger.post-manual
finance.bank.view, finance.bank.reconcile
finance.budgets.view, finance.budgets.manage
finance.reporting.view
finance.payroll-journal.post               ← cross-domain: allows Payroll to post to GL
access.finance-panel
```

### Caching
- `company:{id}:finance:pl:{year-month}` — 1 hr TTL (historical only)
- `company:{id}:finance:ar-aging` — 30 min TTL

---

## CRM & Sales (`/crm`, Rose)

### Custom Pages
| Page | Type | Why Custom |
|---|---|---|
| `PipelineBoardPage` | Kanban board | Drag-and-drop deal cards via Livewire + Alpine.js |
| `CrmDashboardPage` | Dashboard with charts | Pipeline value by stage, activities due, deals closing |
| `ActivityTimelinePage` | Timeline view | Chronological activity feed per contact/deal |
| `ContactMapPage` | (Phase 3) | Geographic contact map if territory management active |

### Standard CRUD Resources
`ContactResource`, `AccountResource`, `DealResource`, `QuoteResource`, `ActivityResource`, `EmailIntegrationResource` (settings page), `SequenceResource`, `SegmentResource`

### Resource-Specific Notes
- `ContactResource` + `AccountResource` view pages: activity timeline embedded as relation manager
- `DealResource` view page: products tab, quote history tab, activity timeline — action buttons: Create Quote, Create Invoice (if won), Move to Stage
- `PipelineBoardPage`: uses Livewire component for drag-and-drop; each card links to `DealResource` view page
- `QuoteResource` view page: quote PDF preview + accept/decline action

### Key Permissions
```
crm.contacts.view-any, crm.contacts.view, crm.contacts.create, crm.contacts.update, crm.contacts.delete
crm.accounts.view-any, crm.accounts.view, crm.accounts.create, crm.accounts.update
crm.deals.view-any, crm.deals.view, crm.deals.create, crm.deals.update
crm.deals.view-all             ← view deals owned by others (managers only)
crm.pipeline.view
crm.quotes.create, crm.quotes.send
crm.activities.view-any, crm.activities.create
crm.sequences.manage
crm.forecasting.view
access.crm-panel
```

### Caching
- `company:{id}:crm:pipeline-value` — 15 min TTL, invalidate on deal update

---

## Projects & Work (`/projects`, Indigo) — Phase 2

### Custom Pages (planned)
| Page | Type | Why Custom |
|---|---|---|
| `KanbanBoardPage` | Kanban board | Drag-and-drop task cards |
| `GanttChartPage` | Gantt chart | Timeline with dependency arrows (library TBD) |
| `WorkloadPage` | Heat map grid | Team × day grid showing task load |
| `SprintBoardPage` | Sprint kanban | Sprint-specific kanban with backlog |

### Standard CRUD
`ProjectResource`, `TaskResource`, `SprintResource`, `MilestoneResource`, `TimeEntryResource`

### Key Permissions (planned)
```
projects.projects.view-any, projects.projects.create, projects.projects.manage
projects.tasks.view-any, projects.tasks.create, projects.tasks.update
projects.time.log, projects.time.approve
projects.sprints.manage
access.projects-panel
```

---

## Communications (`/comms`, Blue) — Phase 2

### Custom Pages (planned)
| Page | Type | Why Custom |
|---|---|---|
| `SharedInboxPage` | Email-client-style UI | Three-panel layout: channels / thread list / message view |
| `ComposeMessagePage` | Rich text compose | Channel selector, recipient, WhatsApp template picker |
| `InboxAnalyticsPage` | Dashboard | Response time, volume by channel |

### Standard CRUD
`ChannelResource` (config), `BroadcastResource` (create + send), `AutomationRuleResource`

### Key Permissions (planned)
```
comms.inbox.view, comms.inbox.reply
comms.broadcast.create, comms.broadcast.send
comms.whatsapp.send
comms.automations.manage
access.comms-panel
```

---

## Support & Help Desk (`/support`, Orange) — Phase 2

### Custom Pages (planned)
| Page | Type | Why Custom |
|---|---|---|
| `TicketInboxPage` | Email-client-style | Similar to comms inbox — focused on tickets |
| `SupportDashboardPage` | Dashboard | CSAT, resolution time, volume trends |
| `SlaMonitorPage` | Real-time view | Tickets approaching SLA breach — live updates via Reverb |

### Standard CRUD
`TicketResource`, `KnowledgeBaseResource` (articles), `CannedResponseResource`, `AutomationRuleResource`, `SlaResource` (policy config)

---

## Document Management (`/dms`, Slate) — Phase 2

### Custom Pages (planned)
| Page | Type | Why Custom |
|---|---|---|
| `DocumentLibraryPage` | File browser | Folder tree + file grid (not a table) |
| `DocumentViewerPage` | PDF/doc preview | In-browser document preview |
| `WikiPage` | Rich text view | Wiki page renderer with table of contents |

### Standard CRUD
`DocumentApprovalResource`, `RetentionPolicyResource`, `DocumentTemplateResource`

---

## Phase 3 Domains — Panel Notes

| Domain | Notable Custom Pages |
|---|---|
| Marketing | Campaign builder (multi-step wizard), Email template designer (visual editor) |
| Analytics | Dashboard builder (drag-and-drop widget grid), Report builder |
| E-commerce | Product catalogue grid, Order fulfilment board, Storefront preview |
| LMS | Course builder (step-by-step lesson editor), Learning path visual builder |
| AI | Workflow builder (node-based flow editor), Copilot chat interface |
| Events | Event calendar (fullcalendar), Attendee check-in view |
| IT | Asset lifecycle board, Helpdesk ticket queue |
| Customer Success | Account health scorecard, Playbook step tracker |

---

## Common Panel Provider Template

Every domain panel follows this exact structure:

```php
class {Domain}PanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('{panel-id}')
            ->path('{panel-path}')
            ->colors(['primary' => Color::hex('{hex-color}')])
            ->brandName('FlowFlex — {Domain Name}')
            ->brandLogo(asset('images/logo/flowflex-logo-light.svg')) // light — sidebar is ink in both modes
            ->font('Instrument Sans') // Switchboard+ body face (brand.md)
            ->darkMode(Feature::Enabled)
            ->sidebarCollapsibleOnDesktop()
            ->authGuard('web')
            ->authModel(User::class)
            ->auth(fn (User $user) => $user->can('access.{panel-id}-panel'))
            ->discoverResources(
                in: app_path('Filament/{DomainPascal}/Resources'),
                for: 'App\\Filament\\{DomainPascal}\\Resources',
            )
            ->discoverPages(
                in: app_path('Filament/{DomainPascal}/Pages'),
                for: 'App\\Filament\\{DomainPascal}\\Pages',
            )
            ->discoverWidgets(
                in: app_path('Filament/{DomainPascal}/Widgets'),
                for: 'App\\Filament\\{DomainPascal}\\Widgets',
            )
            ->middleware(['web', SetLocale::class])
            ->authMiddleware([Authenticate::class, SetCompanyContext::class])
            ->viteTheme('resources/css/filament/{panel-id}/theme.css');
    }
}
```

`access.{panel-id}-panel` permission is seeded for every role that has at least one permission in that domain. A user with only Finance permissions cannot land on the HR panel URL.

---

## Vite Theme Registration

One theme CSS file per panel. All registered in `vite.config.js`:

```js
input: [
    'resources/css/app.css',
    'resources/css/filament/app/theme.css',
    'resources/css/filament/hr/theme.css',
    'resources/css/filament/finance/theme.css',
    'resources/css/filament/crm/theme.css',
    'resources/css/filament/projects/theme.css',
    'resources/css/filament/comms/theme.css',
    'resources/css/filament/support/theme.css',
    'resources/css/filament/dms/theme.css',
    // ... one per active panel
],
```

Each `theme.css` imports the Filament preset and overrides the primary color:

```css
@import '/vendor/filament/filament/resources/css/theme.css';

:root {
    --c-primary-50: ...;
    --c-primary-500: #7C3AED;  /* domain primary */
    --c-primary-900: ...;
}
```

Generate tailwind config for each panel theme: `php artisan filament:install --panels`.
