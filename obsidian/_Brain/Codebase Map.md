---
tags: [brain, codebase, map]
last_updated: 2026-05-07
---

# Codebase Map

Where everything lives in `app/`. Use this to navigate quickly.

---

## App Directory Structure

```
app/
├── Concerns/
│   └── BelongsToCompany.php            — global scope trait, auto-sets company_id
│
├── Enums/
│   ├── Hr/                             — EmploymentStatus, EmploymentType, LeaveRequestStatus,
│   │                                     PayElementType, PayFrequency, PayRunStatus,
│   │                                     OnboardingFlowStatus, OnboardingTaskStatus, OnboardingTaskType,
│   │                                     LeaveAccrualType, CustomFieldType
│   ├── Finance/                        — InvoiceStatus, ExpenseStatus
│   └── Crm/                            — ContactType, DealStatus, TicketStatus, TicketPriority
│
├── Events/
│   ├── Hr/                             — 16 events
│   ├── Projects/                       — 10 events
│   ├── Finance/                        — 8 events
│   └── Crm/                            — 3 events
│
├── Filament/
│   ├── Admin/
│   │   ├── Resources/                  — CompanyResource, TenantResource, UserResource, ModuleResource,
│   │   │                                 RoleResource, Marketing/* (8 resources)
│   │   ├── Pages/Auth/Login.php
│   │   └── Widgets/                    — AdminStatsOverviewWidget, RecentCompaniesWidget, MarketingStatsWidget
│   ├── Workspace/
│   │   └── Pages/Settings/             — ManageCompany, ManageTeam, ManageApiKeys, ManageNotificationPreferences
│   ├── Hr/
│   │   ├── Enums/NavigationGroup.php   — People, Onboarding, Leave, Payroll
│   │   └── Resources/                  — DepartmentResource, EmployeeResource, OnboardingTemplateResource,
│   │                                     OnboardingFlowResource, LeaveTypeResource, LeaveRequestResource,
│   │                                     PayrollEntityResource, PayElementResource, PayRunResource,
│   │                                     SalaryRecordResource, PayslipResource, ContractorPaymentResource,
│   │                                     DeductionResource, PublicHolidayResource
│   ├── Projects/
│   │   ├── Enums/NavigationGroup.php   — Tasks, TimeTracking, Documents
│   │   └── Resources/                  — TaskResource, TaskLabelResource, TimeEntryResource,
│   │                                     TimesheetResource, DocumentFolderResource, DocumentResource
│   ├── Finance/
│   │   ├── Enums/NavigationGroup.php   — Invoices, Expenses, Reports
│   │   └── Resources/                  — InvoiceResource, CreditNoteResource, ExpenseResource,
│   │                                     ExpenseCategoryResource, MileageRateResource,
│   │                                     RecurringInvoiceResource, ExpenseReportResource
│   └── Crm/
│       ├── Enums/NavigationGroup.php   — Contacts, Sales, Support
│       └── Resources/                  — CrmContactResource (+ CrmContactCustomFieldsRelationManager,
│                                         CrmActivitiesRelationManager),
│                                         CrmCompanyResource (+ CrmActivitiesRelationManager),
│                                         DealResource (+ DealNotesRelationManager),
│                                         PipelineResource, DealStageResource, TicketResource,
│                                         CannedResponseResource, TicketSlaRuleResource,
│                                         ChatbotRuleResource
│
├── Http/
│   ├── Controllers/Api/V1/             — ApiController, Hr/*, Projects/*, Finance/*, Crm/*
│   └── Middleware/                     — AuthenticateApiKey, AuthenticateTenant, SetLocaleFromCompany
│
├── Jobs/Hr/
│   └── GeneratePayslipPdf.php          — queued, stub (needs PDF package)
│
├── Listeners/
│   ├── Hr/                             — 8 real + 7 stubs
│   ├── Projects/                       — 1 real + 9 stubs
│   ├── Finance/                        — 4 real + 4 stubs
│   └── Crm/                            — 1 real + 2 stubs
│
├── Models/
│   ├── (root)                          — Company, Tenant, User, ApiKey, Module, SubModule,
│   │                                     Role, Permission, File, NotificationPreference, Address
│   ├── Hr/                             — 26 models
│   ├── Projects/                       — 12 models (Task now has parent_id + parent()/children() relationships)
│   ├── Finance/                        — 10 models
│   ├── Crm/                            — 19 models (CrmContact, CrmCompany, Deal, Pipeline, DealStage,
│   │                                     Ticket, TicketMessage, CannedResponse, TicketSlaRule,
│   │                                     TicketSlaBreach, CsatSurvey, CsatResponse, ChatbotRule,
│   │                                     CrmContactCustomField, CrmContactCustomFieldValue,
│   │                                     CrmActivity, DealNote, SharedInbox, InboxEmail)
│   ├── Marketing/                      — 12 models
│   └── Pivots/CompanyModule.php
│
├── Notifications/
│   ├── FlowFlexNotification.php        — abstract base: toMail() + toArray() + toDatabase()
│   ├── Hr/                             — LeaveApproved/Rejected/Requested, OnboardingStarted,
│   │                                     PayslipGenerated, LeaveBalanceLow
│   ├── Projects/                       — TaskAssigned
│   ├── Finance/                        — InvoiceOverdue, ExpenseSubmitted/Approved/Rejected
│   └── Crm/                            — TicketResolved
│
├── Policies/
│   ├── (root)                          — Company, Tenant, User, ApiKey, Module, File, Role, Permission
│   ├── Hr/                             — 14 policies
│   ├── Projects/                       — 7 policies
│   ├── Finance/                        — 7 policies
│   └── Crm/                            — 9 policies (+ TicketSlaRulePolicy, ChatbotRulePolicy)
│
├── Providers/
│   ├── AppServiceProvider.php          — policy registrations, health checks, file storage singleton
│   ├── EventServiceProvider.php        — 37 event→listener pairs
│   └── Filament/                       — AdminPanelProvider, WorkspacePanelProvider, HrPanelProvider,
│                                         ProjectsPanelProvider, FinancePanelProvider, CrmPanelProvider,
│                                         + 8 future-phase stubs
│
└── Services/
    └── FileStorageService.php          — S3/local abstraction, signed temporary URLs
```

---

## Database

**Migrations:** Numbered by domain group:
- `100000–199999` — Core (companies, tenants, users, modules, api_keys, files…)
- `200000–299999` — HR
- `250000–269999` — Projects
- `600000–699999` — Marketing CMS
- `700000–799999` — Finance + CRM
- `800000+` — Hotfix migrations (renames, indexes, soft-deletes additions)

**Seeders:**
- `RolesAndPermissionsSeeder` — all permissions + roles (super-admin, workspace-admin, hr-manager, finance-manager, sales-rep, employee)
- `DatabaseSeeder` — calls RolesAndPermissionsSeeder

---

## Factories

```
database/factories/
├── CompanyFactory.php
├── TenantFactory.php
├── UserFactory.php
├── Hr/
│   ├── DepartmentFactory.php
│   ├── EmployeeFactory.php          — states: active(), terminated(), forCompany()
│   ├── LeaveTypeFactory.php         — states: unpaid()
│   ├── LeaveRequestFactory.php      — states: approved(), rejected()
│   ├── PayrollEntityFactory.php     — states: default()
│   ├── PayElementFactory.php
│   ├── PayRunFactory.php            — states: approved(), processed()
│   ├── SalaryRecordFactory.php
│   ├── OnboardingTemplateFactory.php
│   ├── OnboardingFlowFactory.php    — states: completed()
│   ├── DeductionFactory.php
│   └── ContractorPaymentFactory.php — states: processed()
├── Projects/
│   ├── TaskFactory.php              — states: done(), inProgress(), forCompany()
│   ├── TaskLabelFactory.php
│   ├── TimesheetFactory.php         — states: submitted(), approved()
│   ├── TimeEntryFactory.php         — states: approved()
│   ├── DocumentFolderFactory.php
│   └── DocumentFactory.php         — states: starred()
├── Finance/
│   ├── InvoiceFactory.php          — states: draft(), sent(), paid()
│   ├── ExpenseFactory.php          — states: approved(), rejected()
│   ├── ExpenseCategoryFactory.php  — states: inactive()
│   ├── CreditNoteFactory.php       — states: forInvoice()
│   └── MileageRateFactory.php      — states: inactive()
└── Crm/
    ├── CrmContactFactory.php       — states: lead(), customer()
    ├── CrmCompanyFactory.php
    ├── PipelineFactory.php         — states: default()
    ├── DealFactory.php             — states: won(), lost()
    ├── TicketFactory.php           — states: resolved(), high()
    ├── CannedResponseFactory.php   — states: private()
    ├── TicketSlaRuleFactory.php    — states: inactive()
    ├── TicketSlaBreachFactory.php
    ├── CsatSurveyFactory.php       — states: sent()
    ├── CsatResponseFactory.php
    ├── ChatbotRuleFactory.php      — states: inactive()
    ├── CrmContactCustomFieldFactory.php — states: dropdown()
    ├── CrmContactCustomFieldValueFactory.php
    ├── CrmActivityFactory.php
    ├── DealNoteFactory.php
    ├── SharedInboxFactory.php      — states: inactive()
    └── InboxEmailFactory.php       — states: read(), archived()
```

---

## Marketing Site Pages (Inertia + Vue)

```
resources/js/Pages/
├── Welcome.vue                     — Homepage (/)
└── Marketing/
    ├── About.vue                   — /about
    ├── Careers.vue                 — /careers
    ├── Changelog.vue               — /changelog
    ├── Contact.vue                 — /contact
    ├── Demo.vue                    — /demo
    ├── Features.vue                — /features
    ├── Help.vue                    — /help
    ├── HelpArticle.vue             — /help/{slug}  (if added)
    ├── Module.vue                  — /modules/{key}
    ├── Pricing.vue                 — /pricing
    ├── Security.vue                — /security
    ├── Status.vue                  — /status
    ├── Blog/
    │   ├── Index.vue               — /blog
    │   └── Post.vue                — /blog/{slug}
    └── Legal/
        ├── Aup.vue                 — /legal/aup
        ├── Cookies.vue             — /legal/cookies
        ├── Dpa.vue                 — /legal/dpa
        ├── Privacy.vue             — /legal/privacy
        └── Terms.vue               — /legal/terms
```

---

## Routes

```
routes/
├── web.php         — marketing site routes (all public pages)
├── api.php         — /api/v1/* (see Current State for full list)
└── console.php     — scheduled commands
```

---

## Translations

```
lang/
├── en/     — auth.php, messages.php, pagination.php, passwords.php, validation.php
├── nl/     — auth.php, messages.php, pagination.php, passwords.php, validation.php
└── de/     — auth.php, messages.php, pagination.php, passwords.php, validation.php
```

Filament vendor translations for `nl` and `de` live in `vendor/filament/*/resources/lang/{nl,de}/` and are loaded automatically when `App::setLocale()` is called.

Flag SVGs: `public/flags/{gb,nl,de}.svg` — used by `bezhansalleh/filament-language-switch`.

i18n (Vue): `resources/js/i18n/` — English-only frontend. `locale.value` stays `'en'` permanently. No language toggle in the marketing site nav.

---

## Config

Key config files touched:
- `config/filament.php` — default panel
- `config/permission.php` — Spatie guard config
- `config/queue.php` — Redis queue
- `bootstrap/providers.php` — all service providers including all panel providers
