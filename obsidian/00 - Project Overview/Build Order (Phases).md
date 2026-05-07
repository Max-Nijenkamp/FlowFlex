---
tags: [flowflex, phases, build-order, roadmap]
domain: Platform
status: phases-1-3-complete
last_updated: 2026-05-07
---

# Build Order (Phases)

The FlowFlex build order. Each phase delivers two complete, fully functional domains. A panel is only marked complete when every module in that domain is built — no features deferred to a later phase.

> **Implementation detail** (what was built, bugs fixed, current state) → see [[Brain Index]] in the `_Brain/` folder.

---

## Guiding Principle

**One phase = two complete domains = two complete panels.**  
Every module, every feature, every table listed under a domain is built in that domain's phase. No partial panels, no deferred features across phases. When a phase ships, users can rely on both panels end-to-end.

---

## Phase 1 — Foundation ✅ Complete

**Goal:** Core infrastructure live. Auth works, tenants are admin-created, files are stored, notifications fire, and the REST API is available. Everything else is built on this.

**Domains:** Core Platform

### Modules

| Module | Status | Description |
|---|---|---|
| [[Authentication & Identity]] | ✅ | Email login, sessions, impersonation. OAuth + SAML deferred to Phase 8. |
| [[Roles & Permissions (RBAC)]] | ✅ | Spatie permission system, role builder, per-module permission layers |
| [[Notifications & Alerts]] | ✅ | In-app bell, email, user preferences; `FlowFlexNotification` base class |
| [[API & Integrations Layer]] | ✅ | REST API v1, API key auth, `/me` + `/modules` endpoints, throttling |
| [[Multi-Tenancy & Workspace]] | ✅ | Workspace settings, branding, team management, module activation |
| [[File Storage]] | ✅ | `FileStorageService`, signed URLs, S3/local abstraction |
| [[Module Billing Engine]] | ⏳ | Deferred to Phase 8 — full Stripe per-module billing |

**Panels delivered:** `admin` (super-admin) · `workspace` (company settings)

**Phase 1 delivers:** A deployable multi-tenant platform. A FlowFlex admin can create companies and onboard their first users. Nothing domain-specific works yet.

---

## Phase 1.5 — Marketing Site (Public Website) ✅ Complete

**Goal:** FlowFlex has a public-facing website for customer acquisition. Built inside the same Laravel application as the platform — no separate frontend framework.

**Domain:** Marketing Site · `14 - Marketing Site/`

> This is the FlowFlex **product website** (flowflex.com) — not a customer feature. Built in parallel with Phase 2, using the admin panel CMS (Phase 1) to manage content.

### Pages & Sections

| Page | Route | Status |
|---|---|---|
| Homepage | `/` | ✅ |
| Pricing | `/pricing` | ✅ |
| Features overview | `/features` | ✅ |
| Module pages | `/modules/{slug}` | ✅ |
| About | `/about` | ✅ |
| Blog | `/blog` | ✅ |
| Blog post | `/blog/{slug}` | ✅ |
| Request demo | `/demo` | ✅ |
| Help centre | `/help` | ✅ |
| Changelog | `/changelog` | ✅ |
| Careers | `/careers` | ✅ |
| Contact | `/contact` | ✅ |
| Status page | `/status` | ✅ |
| Privacy Policy | `/legal/privacy` | ✅ |
| Terms of Service | `/legal/terms` | ✅ |
| Cookie Policy | `/legal/cookies` | ✅ |
| Data Processing Agreement | `/legal/dpa` | ✅ |
| Acceptable Use Policy | `/legal/aup` | ✅ |
| Security | `/security` | ✅ |

### Models (managed via Admin Panel CMS)

All marketing models live under `app/Models/Marketing/`:

| Model | Table | Purpose |
|---|---|---|
| `BlogPost` | `blog_posts` | Blog articles |
| `BlogCategory` | `blog_categories` | Blog categorisation |
| `ChangelogEntry` | `changelog_entries` | Release notes |
| `ContactSubmission` | `contact_submissions` | Contact form leads |
| `DemoRequest` | `demo_requests` | Demo pipeline |
| `FaqEntry` | `faq_entries` | FAQ content |
| `HelpArticle` | `help_articles` | Help centre articles |
| `HelpCategory` | `help_categories` | Help centre categorisation |
| `NewsletterSubscriber` | `newsletter_subscribers` | Email list |
| `OpenRole` | `open_roles` | Careers page jobs |
| `TeamMember` | `team_members` | About page team |
| `Testimonial` | `testimonials` | Social proof |

### Tech

- **Blade + Livewire** — all public pages; server-rendered for SEO
- **Tailwind CSS** — same design tokens as the app
- **Alpine.js** — lightweight interactivity (accordions, mobile menu, pricing toggle)
- Views: `resources/views/marketing/`
- Routes: `routes/web.php` under public prefix group
- Controller: `app/Http/Controllers/Marketing/MarketingController.php`
- Admin CMS: `/admin` — see [[Admin Panel CMS]]

**Migration range:** `600000–699999` (Marketing Site models — separate from in-platform Marketing domain at `400000–449999`)

**Phase 1.5 delivers:** FlowFlex has a public presence. Prospects can learn about the product, read pricing, request a demo, and read the blog. All content is manageable by the FlowFlex team from the admin panel.

---

## Phase 2 — HR & Projects ✅ Complete

**Goal:** A company can manage their people and their work inside FlowFlex. First complete business domain.

**Domains:** HR & People · Projects & Work

### HR Modules

| Module | Status | Description |
|---|---|---|
| [[Employee Profiles]] | ✅ | Employee records, departments, documents, custom fields |
| [[Onboarding]] | ✅ | Onboarding templates, flows, task completion, check-ins |
| [[Leave Management]] | ✅ | Leave types, policies, requests, balances, approval workflow |
| [[Payroll]] | ✅ | Pay elements, pay runs, salary records, payslips, contractor payments, deductions |

### Projects Modules

| Module | Status | Description |
|---|---|---|
| [[Task Management]] | ✅ | Tasks, labels, dependencies, automations, automation logs |
| [[Time Tracking]] | ✅ | Time entries, timesheets, timesheet approvals |
| [[Document Management]] | ✅ | Folders, documents, versioning, sharing, starred docs |

**Panels delivered:** `hr` (purple `#7C3AED`) · `projects`

**Phase 2 delivers:** Replaces BambooHR + Jira/Trello for a small business. HR team can run payroll, approve leave, and onboard staff. Project teams can track tasks, log time, and share documents.

---

## Phase 3 — Finance & CRM ✅ Complete

**Goal:** A business can invoice clients, track expenses, manage customer relationships, and run a sales pipeline.

**Domains:** Finance · CRM & Sales

### Finance Modules

| Module | Status | Description |
|---|---|---|
| [[Invoicing]] | ✅ | Invoice builder, credit notes, recurring invoices, email events, payments |
| [[Expense Management]] | ✅ | Expense submission, categories, mileage rates, approval workflow |
| [[Financial Reporting]] | ✅ | P&L, revenue, expenses, outstanding — FinancialSummaryWidget + page |

### CRM Modules

| Module | Status | Description |
|---|---|---|
| [[Contact & Company Management]] | ✅ | 360° contact and company records, activity timeline |
| [[Sales Pipeline]] | ✅ | Deal pipeline, custom stages, deal stages, mark won/lost |
| [[Customer Support & Helpdesk]] | ✅ | Ticket queue, messages, canned responses, resolve action |
| [[Shared Inbox & Email]] | ✅ | Models and events wired; full UI built in Phase 8 (CRM Extension) |

**Panels delivered:** `finance` (emerald `#059669`) · `crm` (blue `#2563EB`)

**Phase 3 delivers:** Replaces Xero/QuickBooks (basic) + Salesforce/HubSpot (basic). Finance team can issue invoices, approve expenses, and see P&L. Sales team has a full deal pipeline and support ticketing.

---

## Phase 4 — Operations & Ecommerce

**Goal:** Physical operations fully managed and products sold online. An operations manager can track stock, assets, purchasing, field jobs, POS, and safety — all in one panel. An ecommerce team can manage products, process orders, and sell via marketplace channels, subscriptions, and digital downloads.

**Domains:** Operations & Field Service · E-commerce & Sales Channels

### Operations Modules

| Module | Description |
|---|---|
| [[Inventory Management]] | Products, stock locations, stock levels, stock movements, adjustments, batch/serial, reorder rules, cycle counts |
| [[Asset Management]] | Asset register, categories, assignments, check-in/out, lifecycle, QR labels |
| [[Purchasing & Procurement]] | Suppliers, purchase orders, PO lines, goods receipts, 3-way matching, approval thresholds |
| [[Equipment Maintenance]] | Maintenance schedules (preventive/reactive), work orders, parts usage, maintenance logs |
| [[Field Service Management]] | Job dispatch, technician assignment, GPS tracking, mobile sign-off, checklists, photos |
| [[Point of Sale]] | POS terminals, sessions, transactions, transaction lines, payments, inventory sync |
| [[Quality Control & Inspections]] | Inspection templates, inspection records, responses, non-conformance reports (NCR) |
| [[Supply Chain Visibility]] | Shipments, shipment events, carrier tracking, supplier performance scores |
| [[HSE]] | Incidents, risk assessments (safety), safety observations, incident investigations |

### Ecommerce Modules

| Module | Description |
|---|---|
| [[Product Catalogue]] | Products, variants, categories, brands, images, pricing rules, tax codes |
| [[Order Management]] | Orders, order lines, fulfillments, shipments, returns, refunds, channel refs |
| [[Storefront & Checkout]] | Storefronts, pages, carts, cart items, checkout sessions, Stripe/PayPal |
| [[Marketplace Channel Sync]] | Amazon/eBay/Etsy/Shopify connections, channel listings, sync logs |
| [[Subscription Products]] | Plans, subscriptions, subscription invoices, dunning attempts |
| [[Digital Products & Downloads]] | Digital products, download links, licence keys, streaming access |

### Key Events — Phase 4

| Event | Source | Consumed By |
|---|---|---|
| `StockBelowReorderPoint` | Inventory Management | Purchasing (create draft PO) |
| `PurchaseOrderApproved` | Purchasing | Finance — AP/AR (create bill) |
| `PurchaseOrderReceived` | Purchasing | Inventory (update stock levels) |
| `WorkOrderCompleted` | Equipment Maintenance | Asset (update maintenance record) |
| `FieldJobCompleted` | Field Service | Invoice (create invoice), Inventory (deduct parts), CRM (close ticket) |
| `POSTransactionCompleted` | Point of Sale | Inventory (deduct stock), Finance (record sale) |
| `OrderPlaced` | Order Management | Inventory (deduct stock), Invoice (record revenue), CRM (update customer) |
| `CheckoutCompleted` | Storefront | Order Management (create order) |
| `SubscriptionRenewed` | Subscription Products | Finance (create invoice) |
| `PaymentFailed` | Subscription Products | Dunning flow triggered |

**Panels delivered:** `operations` (amber `#D97706`) · `ecommerce` (teal `#0D9488`)

**Migration range:** `300000–399999` (Operations) · `500000–599999` (Ecommerce)

**Phase 4 delivers:** Replaces DEAR Inventory + Shopify + Cin7 + Bigcommerce. Operations teams manage physical assets, stock, purchasing, field jobs, POS and quality. Ecommerce teams manage products, orders, and multiple sales channels.

---

## Phase 5 — Marketing & Communications

**Goal:** Marketing teams can run campaigns, build content, and capture leads. Internal communications are centralised — messaging, announcements, intranet, meetings, and bookings.

**Domains:** Marketing & Content · Communications & Internal Comms

### Marketing Modules

| Module | Description |
|---|---|
| [[CMS & Website Builder]] | Content pages, content blocks, media library, redirect rules, templates, scheduled publishing |
| [[Email Marketing]] | Campaigns, campaign recipients, email sequences, sequence steps, A/B testing, deliverability |
| [[Forms & Lead Capture]] | Form builders, form fields (all types, conditional logic), form submissions, CRM auto-create |
| [[Social Media Management]] | Social accounts, posts, scheduling, content calendar, post analytics |
| [[SEO & Analytics]] | SEO audits, keyword rankings, GA4 snapshots, redirect manager, technical audit |
| [[Ad Campaign Management]] | Ad accounts (Google/Meta/LinkedIn/TikTok), campaigns, performance snapshots, ROAS dashboard |
| [[Events & Webinars]] | Events, registrations, attendees, sessions, waitlist, QR check-in |
| [[Affiliate & Partner Management]] | Affiliates, referral links, commissions, payouts, affiliate portal |

### Communications Modules

| Module | Description |
|---|---|
| [[Internal Messaging & Chat]] | Channels (public/private/direct), messages, attachments, threading, reactions |
| [[Company Announcements]] | Announcements, read receipts, acknowledgement tracking |
| [[Meeting & Video Integration]] | Meeting scheduling, Google Meet/Zoom/Teams links, notes, action items |
| [[Company Intranet]] | Intranet pages, news feed, org chart, pinned content |
| [[Booking & Appointment Scheduling]] | Booking pages, availability, appointments, confirmation emails |

### Key Events — Phase 5

| Event | Source | Consumed By |
|---|---|---|
| `FormSubmissionReceived` | Forms | CRM (auto-create contact), Email (trigger sequence) |
| `CheckoutCompleted` | Ecommerce (Phase 4) | Email (trigger post-purchase sequence) |
| `CartAbandoned` | Ecommerce (Phase 4) | Email (trigger abandoned cart sequence) |
| `TicketResolved` | CRM (Phase 3) | Email (send CSAT survey) |
| `EventRegistrationReceived` | Events | Email (confirmation), CRM (create contact) |
| `AppointmentBooked` | Booking | Email (confirmation + calendar invite) |
| `AnnouncementPublished` | Announcements | Push notification to all tenants |
| `MeetingCompleted` | Meeting | Tasks (auto-create action items) |

**Panels delivered:** `marketing` (pink `#DB2777`) · `communications` (sky `#0284C7`)

**Migration range:** `400000–449999` (Marketing) · `450000–499999` (Communications)

**Phase 5 delivers:** Replaces Mailchimp + Webflow + Typeform + HootSuite + Calendly + Slack (internal) + Confluence (intranet). Marketing team runs full campaigns. Entire company communicates internally without leaving FlowFlex.

---

## Phase 6 — Analytics & IT Security

**Goal:** Every metric is visible without writing SQL. IT and security are centralised — assets, licences, helpdesk, access, compliance, and uptime monitoring.

**Domains:** Analytics & BI · IT & Security Management

### Analytics Modules

| Module | Description |
|---|---|
| [[Custom Dashboards]] | Drag-and-drop dashboard builder, cross-module widgets (metric/chart/table/funnel) |
| [[Report Builder]] | Self-serve report builder, no SQL, any domain, scheduled delivery, export |
| [[KPI & Goal Tracking]] | Company KPIs, check-ins, goal cascade to teams and individuals |
| [[Team Velocity & Ops Metrics]] | Cycle time, throughput, burnout signals, ops metrics snapshots |
| [[Audit Log & Activity Trail]] | Immutable activity log viewer, filtering, export for compliance |
| [[Data Warehouse & Export]] | BigQuery/Snowflake/S3 export jobs (Enterprise-tier feature) |

### IT & Security Modules

| Module | Description |
|---|---|
| [[IT Asset Management]] | Hardware and software lifecycle, licence compliance, seat tracking |
| [[Internal IT Helpdesk]] | Employee IT tickets, SLA policies, categories, internal notes |
| [[SaaS Spend Management]] | SaaS discovery, spend tracking, shadow IT, renewal alerts |
| [[Access & Permissions Audit]] | Cross-system access snapshots, overprovision alerts |
| [[Security & Compliance]] | Compliance frameworks (GDPR/ISO27001/SOC2), controls, evidence tracking |
| [[Uptime & Status Monitoring]] | Service monitoring, status checks, incidents, public status page |

### Key Events — Phase 6

| Event | Source | Consumed By |
|---|---|---|
| `KPIOffTrack` | KPI & Goals | Notifications (notifies owner) |
| `BurnoutSignalDetected` | Team Velocity | HR (notify HR manager) |
| `ReportGenerated` | Report Builder | Email (deliver to recipients) |
| `SoftwareLicenceExpiring` | IT Asset Management | Finance (create renewal task) |
| `ITTicketSLABreached` | IT Helpdesk | Notifications (escalate to IT manager) |
| `SaaSLicenceExpiring` | SaaS Spend | Finance + IT notifications |
| `OverprovisionAlertRaised` | Access Audit | IT security notifications |
| `ComplianceControlFailed` | Security | Notifications (notify compliance officer) |
| `ServiceDown` | Uptime Monitor | Immediate alert to IT team |
| `EmployeeHired` | HR (Phase 2) | IT Helpdesk (create access provisioning ticket) |
| `OffboardingCompleted` | HR (Phase 2) | IT (revoke all access, trigger access audit) |

**Panels delivered:** `analytics` (purple `#9333EA`) · `it` (slate `#475569`)

**Migration range:** `800000–849999` (Analytics) · `850000–899999` (IT)

**Phase 6 delivers:** Replaces Looker/Metabase (basic) + Jira Service Management (IT) + Zylo (SaaS spend) + Okta Access Reviews. Business leaders see all KPIs in one place. IT team manages the entire company's tech stack from one panel.

---

## Phase 7 — Legal & Learning

**Goal:** Legal and compliance are tracked, not chased. Every employee has a structured learning path, skills are mapped, and succession is planned.

**Domains:** Legal & Compliance · Learning & Development

### Legal Modules

| Module | Description |
|---|---|
| [[Contract Management]] | Contracts, parties, versions, e-signature, renewal alerts, auto-renewal rules |
| [[Policy Management]] | Policies, versions, employee acknowledgements, review schedules |
| [[Risk Register]] | Risks, risk scoring, mitigations, periodic reviews |
| [[Data Privacy]] | DSRs (access/erasure/rectification), processing activities, consent records, DPIAs |
| [[Insurance & Licence Tracking]] | Insurance policies, regulatory licences, expiry reminders |

### Learning & Development Modules

| Module | Description |
|---|---|
| [[Course Builder & LMS]] | Courses, modules, lessons (video/quiz/SCORM/PDF), enrollments, certificates |
| [[Skills Matrix & Gap Analysis]] | Skills taxonomy, employee skill levels, role requirements, gap analysis |
| [[Succession Planning]] | Key roles, 9-box grid, succession candidates, readiness levels |
| [[Mentoring & Coaching]] | Mentor profiles, mentor-mentee matching, session tracking |
| [[External Training Requests]] | Training requests, manager approval, completion tracking, certificates |

### Key Events — Phase 7

| Event | Source | Consumed By |
|---|---|---|
| `ContractExpiring` | Contracts | CRM (renewal task), Legal notifications |
| `ContractSigned` | Contracts | Notifications (confirm to all parties) |
| `RiskFlagRaised` | Risk Register | Legal team notifications |
| `PolicyPublished` | Policy Management | Notifications (all relevant tenants) |
| `PolicyAcknowledgementOverdue` | Policy Management | Notifications (remind tenant) |
| `CourseCompleted` | LMS | HR Compliance (mark cert), Performance (log dev) |
| `CertificateExpiring` | LMS | Reminder notification to employee + manager |
| `SkillGapIdentified` | Skills Matrix | LMS (recommend course) |
| `EmployeeHired` | HR (Phase 2) | LMS (assign induction course) |
| `OnboardingCompleted` | HR (Phase 2) | LMS (assign first compliance certs) |

**Panels delivered:** `legal` (red `#DC2626`) · `lms` (orange `#EA580C`)

**Migration range:** `900000–949999` (Legal) · `950000–999999` (LMS)

**Phase 7 delivers:** Replaces DocuSign/Ironclad (contracts) + OneTrust (privacy) + Litmos/TalentLMS (LMS). Legal team tracks every contract, policy, and risk. HR/L&D team runs the full learning lifecycle from induction to succession planning.

---

## Phase 8 — Platform Extensions & Enterprise

**Goal:** Complete the platform. Every remaining module from existing domains is built. Enterprise infrastructure enables scale, compliance certification, and an API marketplace.

### Domain Extensions (remaining modules not built in Phases 1–7)

**HR Extensions:**

| Module | Description |
|---|---|
| [[Recruitment & ATS]] | Job postings, applicants, pipeline stages, offers, onboarding handoff |
| [[Performance & Reviews]] | Review cycles, 360 feedback, OKRs, performance ratings |
| [[Scheduling & Shifts]] | Shift builder, team schedules, absence coverage, payroll integration |
| [[Benefits & Perks]] | Benefits catalogue, enrollment, flex benefits, total comp statements |
| [[Employee Feedback]] | Pulse surveys, anonymous feedback, sentiment tracking |
| [[HR Compliance]] | Certification tracking, compliance deadlines, document expiry |
| [[Offboarding]] | Offboarding checklists, asset recall, exit interviews, access revocation |

**Finance Extensions:**

| Module | Description |
|---|---|
| [[Accounts Payable & Receivable]] | Bills, bill payments, AR aging reports, creditor/debtor management |
| [[Bank Reconciliation]] | Bank feed import, transaction matching, reconciliation status |
| [[Budgeting & Forecasting]] | Budget templates, actuals vs budget, rolling forecasts |
| [[Client Billing & Retainers]] | Retainer agreements, drawdown tracking, milestone billing |
| [[Tax & VAT Compliance]] | Tax rates, VAT returns, MTD integration (UK), filing status |
| [[Fixed Asset & Depreciation]] | Asset register, depreciation schedules (SL/DB), disposal |
| [[Subscription & MRR Tracking]] | MRR/ARR reporting, churn tracking, expansion revenue |

**Projects Extensions:**

| Module | Description |
|---|---|
| [[Project Planning]] | Full Gantt chart, milestones, dependencies, critical path |
| [[Document Approvals & E-Sign]] | Approval workflows on documents, e-signature requests |
| [[Knowledge Base & Wiki]] | Company wiki, article editor, search, version history |
| [[Team Collaboration]] | Comment threads on any record, @mentions, reactions, activity feeds, watching |
| [[Resource & Capacity Planning]] | Team capacity calendar, resource allocation, utilisation |
| [[Agile & Sprint Management]] | Sprint boards, backlog, velocity charts, burndown |

**CRM Extensions:**

| Module | Description |
|---|---|
| [[Customer Data Platform]] | Unified customer profiles across all touchpoints |
| [[Client Portal]] | Self-service portal for clients to view invoices, tickets, documents |
| [[Quotes & Proposals]] | Quote builder, product lines, e-signature, convert to invoice |
| [[Loyalty & Retention]] | Points system, rewards, churn scoring, win-back campaigns |
| [[Shared Inbox & Email]] | Full shared inbox UI (models wired in Phase 3, UI built here) |

### Enterprise Infrastructure

| Feature | Description |
|---|---|
| [[Module Billing Engine]] | Full Stripe per-module subscription billing, usage-based pricing |
| [[Data Warehouse & Export]] | Enterprise tier unlock — module built in Phase 6, activated here for Enterprise companies |
| White-label | Custom domain, custom branding, removable FlowFlex attribution |
| API Marketplace | Third-party developer APIs, webhook subscriptions, OAuth apps |
| SCIM Provisioning | Enterprise SSO auto-provisioning (Okta, Azure AD) |
| Multi-region Deployment | EU/US/APAC data residency, GDPR-compliant data location |
| SOC 2 Type II Tooling | Automated evidence collection, audit trail export, compliance dashboard |
| Custom SLA Contracts | Enterprise SLA terms, uptime guarantees, dedicated support |

**Phase 8 delivers:** FlowFlex is complete. Every module across all 13 domains is live. Enterprise customers with compliance requirements can certify against SOC 2 and GDPR. Third-party developers can extend the platform.

---

## Cross-Module Event Coverage by Phase

| Phase | Key Events Enabled |
|---|---|
| Phase 1 | `TenantCreated`, `ModuleActivated`, `ApiKeyCreated` |
| Phase 2 | `EmployeeHired`, `LeaveApproved`, `PayslipGenerated`, `TaskAssigned`, `TimeEntryApproved` |
| Phase 3 | `InvoicePaid`, `InvoiceOverdue`, `ExpenseApproved`, `TicketResolved`, `DealWon` |
| Phase 4 | `StockBelowReorderPoint`, `OrderPlaced`, `FieldJobCompleted`, `POSTransactionCompleted` |
| Phase 5 | `FormSubmissionReceived`, `EmailCampaignSent`, `AppointmentBooked`, `AnnouncementPublished` |
| Phase 6 | `KPIOffTrack`, `ServiceDown`, `ComplianceControlFailed`, `OverprovisionAlertRaised` |
| Phase 7 | `ContractExpiring`, `CourseCompleted`, `RiskFlagRaised`, `PolicyPublished` |
| Phase 8 | `SubscriptionChurned`, `RecruitmentOfferAccepted`, `BudgetOverrun`, `CommentPosted`, `ContractSigned` |

---

## Panel Summary

| Panel | Domain | Color | Phase |
|---|---|---|---|
| `admin` | Platform admin | `#2199C8` | 1 ✅ |
| `workspace` | Company settings | — | 1 ✅ |
| `hr` | HR & People | `#7C3AED` | 2 ✅ |
| `projects` | Projects & Work | — | 2 ✅ |
| `finance` | Finance | `#059669` | 3 ✅ |
| `crm` | CRM & Sales | `#2563EB` | 3 ✅ |
| `operations` | Operations & Field Service | `#D97706` | 4 |
| `ecommerce` | E-commerce | `#0D9488` | 4 |
| `marketing` | Marketing & Content | `#DB2777` | 5 |
| `communications` | Internal Comms | `#0284C7` | 5 |
| `analytics` | Analytics & BI | `#9333EA` | 6 |
| `it` | IT & Security | `#475569` | 6 |
| `legal` | Legal & Compliance | `#DC2626` | 7 |
| `lms` | Learning & Development | `#EA580C` | 7 |

## Related

- [[FlowFlex Overview]]
- [[Architecture]]
- [[Module Development Checklist]]
- [[Panel Map]]
- [[Brain Index]]
