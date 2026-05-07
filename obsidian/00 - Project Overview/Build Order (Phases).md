---
tags: [flowflex, phases, build-order, roadmap, phase/1]
domain: Platform
status: phase-1-2-complete
last_updated: 2026-05-07
---

# Build Order (Phases)

The FlowFlex MVP build order. Each phase is deployable and sellable on its own.

## Phase 1 — Foundation (Month 1) ✅ In Progress

**Goal:** Core Platform complete. Authentication works, tenants are admin-created, notifications work, files can be stored. Nothing else is built yet but the entire infrastructure is ready.

**Modules:**
- ✅ [[Authentication & Identity]] — email login, sessions, impersonation (OAuth + SAML deferred post-launch)
- ✅ [[Roles & Permissions (RBAC)]] — Spatie permission system, role builder, permission layers
- ⏳ [[Module Billing Engine]] — deferred to Phase 6 (billing setup after MVP testing)
- ✅ [[Notifications & Alerts]] — in-app bell, email, user preferences; `FlowFlexNotification` base class
- ✅ [[API & Integrations Layer]] — REST API v1, API key auth, `/me` + `/modules` endpoints
- ✅ [[Multi-Tenancy & Workspace]] — workspace settings pages, branding, team management
- ✅ [[File Storage]] — `FileStorageService`, `File` model, signed URLs, S3/local abstraction

**What was built in code:**
- `admin` Filament panel (FlowFlex super-admin)
- `workspace` Filament panel (per-tenant settings) with Settings nav group
- Company, Tenant, Module, Role, Permission, ApiKey, File, NotificationPreference models
- Migrations for all Phase 1 tables
- `AuthenticateApiKey` middleware + `routes/api.php`
- Workspace settings pages: ManageCompany, ManageTeam, ManageNotificationPreferences, ManageApiKeys
- `FileStorageService` singleton
- `FlowFlexNotification` abstract base + `ModuleToggledNotification`
- No self-registration — first account created by super-admin, workspace owner adds members

**Phase 1 delivers:** Working multi-tenant platform with auth, settings, notifications, file storage, and a REST API. Foundation solid for Phase 2 module development.

---

## Phase 2 — First Module Cluster (Month 2) ✅ Complete

**Goal:** HR and Projects modules live. A company can manage their people and their work inside FlowFlex.

**HR & People modules:**
- ✅ [[Employee Profiles]] — DepartmentResource, EmployeeResource (with DocumentsRelationManager)
- ✅ [[Onboarding]] — OnboardingTemplateResource (with TemplateTasksRelationManager), OnboardingFlowResource
- ✅ [[Leave Management]] — LeaveTypeResource, LeaveRequestResource
- ✅ [[Payroll]] — PayElementResource, PayRunResource, SalaryRecordResource

**Projects & Work modules:**
- ✅ [[Task Management]] — TaskResource, TaskLabelResource (existing)
- ✅ [[Time Tracking]] — TimeEntryResource (completed pages), TimesheetResource
- ✅ [[Document Management]] — DocumentFolderResource, DocumentResource

**What was built in code:**
- 9 HR Filament resources across People / Onboarding / Leave / Payroll navigation groups
- 4 Projects Filament resources across Tasks / Time Tracking / Documents navigation groups
- All granular permissions added to RolesAndPermissionsSeeder
- `hr-manager` role covers all `hr.*` permissions via wildcard
- `employee` role gets leave-requests and timesheet view/create
- `EventServiceProvider` — 8 event→listener pairs wired
- 6 Notifications: `LeaveRequested`, `LeaveApproved`, `LeaveRejected`, `PayslipGenerated`, `OnboardingStarted`, `TaskAssigned`
- 8 Listeners (all `ShouldQueue`): notify manager/employee on leave events, dispatch payslip jobs, notify on onboarding/task assigned
- `GeneratePayslipPdf` queued job — creates Payslip record, fires `PayslipGenerated` (PDF render stub — needs PDF package)
- 16 Phase 2 policies registered in AppServiceProvider (9 HR + 7 Projects)
- REST API Phase 2 endpoints: `GET /api/v1/hr/employees`, `/hr/leave-requests`, `/projects/tasks`, `/projects/time-entries` (+ `/{id}` variants)

**What Phase 2 delivers:** A usable HR system and a task/project management system. A small business can replace BambooHR + Jira/Trello with FlowFlex after Phase 2.

---

## Phase 3 — Finance & CRM (Month 3)

**Goal:** Finance and CRM live. A business can invoice clients, track expenses, and manage their customer relationships.

**Finance modules:**
- [[Invoicing]] — invoice builder, auto-generate from time, recurring
- [[Expense Management]] — mobile receipt, OCR, approval, payroll reimbursement
- [[Financial Reporting]] — P&L, balance sheet, cash flow, custom reports

**CRM & Sales modules:**
- [[Contact & Company Management]] — 360° contact records, activity timeline
- [[Sales Pipeline]] — deal pipeline, forecasting, win/loss tracking
- [[Shared Inbox & Email]] — shared team inbox, email sequences
- [[Customer Support & Helpdesk]] — ticket management, SLAs, live chat

**What Phase 3 delivers:** A business can replace Xero/QuickBooks (basic) + Salesforce/HubSpot (basic) with FlowFlex. Strong first paying customer proposition.

---

## Phase 4 — Operations & Marketing (Month 4)

**Goal:** Operations and Marketing modules live.

**Operations modules:**
- [[Inventory Management]] — stock levels, reorder alerts, barcode scanning
- [[Asset Management]] — physical asset tracking, check-in/out, lifecycle
- [[Purchasing & Procurement]] — POs, supplier approval, 3-way matching

**Marketing modules:**
- [[CMS & Website Builder]] — block-based CMS, blog, SEO fields
- [[Email Marketing]] — campaign builder, automation flows, A/B testing
- [[Forms & Lead Capture]] — drag-and-drop form builder, CRM integration

**What Phase 4 delivers:** Operations and retail businesses can manage stock, assets, and purchasing. Marketing teams can run campaigns. Replaces Shopify inventory + Mailchimp + Typeform.

---

## Phase 5 — Extended Modules (Month 5–6)

**Goal:** Complete the platform. All remaining domains built.

**Analytics & BI:**
- [[Custom Dashboards]], [[Report Builder]], [[KPI & Goal Tracking]]
- [[Audit Log & Activity Trail]], [[Team Velocity & Ops Metrics]]

**IT & Security:**
- [[IT Asset Management]], [[Internal IT Helpdesk]], [[SaaS Spend Management]]
- [[Access & Permissions Audit]], [[Security & Compliance]], [[Uptime & Status Monitoring]]

**Legal & Compliance:**
- [[Contract Management]], [[Policy Management]], [[Risk Register]]
- [[Data Privacy]], [[Insurance & Licence Tracking]]

**E-commerce:**
- [[Product Catalogue]], [[Order Management]], [[Storefront & Checkout]]
- [[Marketplace Channel Sync]], [[Subscription Products]]

**Learning & Development:**
- [[Course Builder & LMS]], [[Skills Matrix & Gap Analysis]], [[Succession Planning]]
- [[Mentoring & Coaching]], [[External Training Requests]]

**Communications:**
- [[Internal Messaging & Chat]], [[Company Announcements]], [[Meeting & Video Integration]]
- [[Company Intranet]], [[Booking & Appointment Scheduling]]

**HR Remaining:**
- [[Recruitment & ATS]], [[Performance & Reviews]], [[Scheduling & Shifts]]
- [[Benefits & Perks]], [[Employee Feedback]], [[HR Compliance]]

**Projects Remaining:**
- [[Project Planning]] (full Gantt), [[Document Approvals & E-Sign]]
- [[Knowledge Base & Wiki]], [[Resource & Capacity Planning]], [[Agile & Sprint Management]]

**Finance Remaining:**
- [[Accounts Payable & Receivable]], [[Bank Reconciliation]], [[Budgeting & Forecasting]]
- [[Client Billing & Retainers]], [[Tax & VAT Compliance]], [[Fixed Asset & Depreciation]]
- [[Subscription & MRR Tracking]]

**CRM Remaining:**
- [[Customer Data Platform]], [[Client Portal]], [[Quotes & Proposals]]
- [[Loyalty & Retention]]

---

## Phase 6 — Enterprise & Scale

**Goal:** Enterprise features, compliance certifications, API marketplace.

**Deliverables:**
- [[Data Warehouse & Export]] — BigQuery sync, Snowflake, S3 export, ETL jobs
- SOC 2 Type II compliance tooling
- Advanced multi-region deployment support
- API marketplace — let third-party developers build FlowFlex modules
- SCIM provisioning for enterprise SSO
- Custom SLA contracts
- Dedicated account management tooling

---

## Cross-Module Event Coverage by Phase

| Phase | Key Events Enabled |
|---|---|
| Phase 1 | `UserLoggedIn`, `ModuleActivated`, `TenantCreated` |
| Phase 2 | `EmployeeHired`, `TimeEntryApproved`, `LeaveApproved` |
| Phase 3 | `InvoiceOverdue`, `InvoicePaid`, `TicketResolved` |
| Phase 4 | `StockBelowReorderPoint`, `OrderPlaced` |
| Phase 5 | `CourseCompleted`, `ContractExpiring`, `FieldJobCompleted` |

## Related

- [[FlowFlex Overview]]
- [[Architecture]]
- [[Module Development Checklist]]
- [[Panel Map]]
