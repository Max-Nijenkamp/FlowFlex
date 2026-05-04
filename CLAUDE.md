# CLAUDE.md — FlowFlex Platform

> This file is the single source of truth for Claude when working on the FlowFlex codebase.
> Read this entire file before writing any code, suggesting any architecture, or making any decisions.
> When in doubt, refer back here first.

---

## 1. What is FlowFlex?

FlowFlex is a **modular, multi-tenant SaaS platform** that replaces the fragmented mess of tools businesses currently use (Jira, Salesforce, Xero, BambooHR, Monday, Slack, Docusign, etc.) with a single unified workspace.

The name says it all:
- **Flow** — like the flow of the sea. Smooth, continuous, everything moves together naturally.
- **Flex** — flexible and scalable by the customer's needs. You pay for what you use, nothing more.

The core promise to customers: **one login, one data layer, one bill — every tool your business needs, only the ones you actually want.**

FlowFlex is not another all-in-one bloatware suite. It is a **module-based platform** where customers activate only the modules they need and pay per module (or bundle). Every module shares the same authentication, permissions system, data layer, notification engine, and API surface.

---

## 2. The Problem FlowFlex Solves

Businesses today:
- Use 8–15 disconnected SaaS tools that don't talk to each other
- Pay for features they never use in bloated enterprise software (Salesforce, SAP, HubSpot)
- Spend money and time gluing tools together manually (Excel → Payroll → HR → CRM)
- Suffer from data fragmentation: the same customer exists in 3 different tools as 3 different records
- Pay per-tool pricing that scales painfully as headcount grows

FlowFlex fixes this by being the **single system of record** for the entire business operation — HR, finance, projects, customers, operations, marketing, IT, legal, communications, learning, and e-commerce — all under one roof, all interconnected, all optional.

---

## 3. Tech Stack

### Backend
- **Laravel 11** (PHP) — primary framework
- **Filament 3** — admin panel framework (TALL stack: Tailwind, Alpine.js, Livewire, Laravel)
- **PostgreSQL** — primary relational database (one schema per tenant or row-level tenancy via `tenant_id`)
- **Redis** — caching, queues, sessions, real-time pub/sub
- **Laravel Queues** — background jobs (payroll runs, report generation, email sends, event processing)
- **Laravel Events + Listeners** — cross-module event bus (the backbone of module interconnection)
- **Laravel Sanctum** — API token authentication
- **Spatie Laravel Permission** — RBAC (roles and permissions)
- **Spatie Laravel Multitenancy** — multi-tenant workspace isolation
- **Spatie Laravel Activity Log** — immutable audit trail across all modules

### Frontend
- **Livewire 3** — reactive components within Filament panels
- **Alpine.js** — lightweight interactivity
- **Tailwind CSS** — utility-first styling
- **Filament Panels** — separate panel per domain (admin, HR, finance, operations, etc.)

### Infrastructure & Services
- **Laravel Horizon** — queue monitoring
- **Laravel Telescope** — local debugging
- **Stripe** — subscription billing, module metering, usage-based charges
- **Twilio** — SMS notifications
- **Resend / Mailgun** — transactional email
- **AWS S3 / Cloudflare R2** — file storage (documents, assets, media)
- **Pusher / Soketi** — real-time WebSocket events (notifications, live updates)

### Architecture Pattern
- **Modular Monolith** — single Laravel application, but modules are fully isolated internally
- Each module has its own: `Models/`, `Filament/`, `Services/`, `Listeners/`, `Policies/`, `database/migrations/`
- Modules communicate only via **Events** — never by directly calling another module's internal classes
- The monolith can later be split into microservices per module when scale demands it, without rewriting

---

## 4. Architecture Principles

### 4.1 Modular Monolith Structure

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

Each module folder follows this internal structure:

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

### 4.2 The Core Module (Always Active)

The Core module is **never optional**. It provides:
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

### 4.3 Cross-Module Communication Rules

**Rule: Modules NEVER import each other's internal classes directly.**

Modules communicate only via:
1. **Laravel Events** — a module fires an event; other modules listen
2. **The Core data layer** — shared models like `User`, `Tenant`, `File` live in Core
3. **Service contracts** — if Module A needs data from Module B, it calls a registered interface, not a concrete class

Example of correct cross-module flow:
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

### 4.4 Multi-Tenancy

- Every database table that belongs to a module has a `tenant_id` column
- Every Eloquent query is automatically scoped to the current tenant via a global scope
- Tenants are fully isolated — no data leaks between workspaces
- Each tenant has: custom subdomain, optional custom domain (CNAME), branding config, locale, timezone
- Use `spatie/laravel-multitenancy` for tenant resolution and context switching

### 4.5 Module Registry & Billing

- A `modules` table tracks which modules each tenant has active
- A `module_usage_events` table records metered events per tenant
- Stripe usage records are synced from this table via a scheduled job
- When a tenant toggles a module off, its Filament panel resources are hidden; data is retained
- Module pricing is defined in config, not hardcoded in UI

---

## 5. Filament Panel Architecture

FlowFlex uses **multiple Filament panels** — one per domain cluster. This means each user only sees the panels they have access to.

### Panel Map

| Panel ID | Domain | URL path | Access |
|---|---|---|---|
| `admin` | Platform super-admin | `/admin` | FlowFlex staff only |
| `workspace` | Workspace settings, billing, module management | `/app/settings` | Workspace admins |
| `hr` | HR & People | `/app/hr` | HR team, managers |
| `projects` | Projects & Work | `/app/projects` | All employees |
| `finance` | Finance & Accounting | `/app/finance` | Finance team |
| `crm` | CRM & Sales | `/app/crm` | Sales, support |
| `marketing` | Marketing & Content | `/app/marketing` | Marketing team |
| `operations` | Operations & Field Service | `/app/ops` | Ops, field teams |
| `it` | IT & Security | `/app/it` | IT team |
| `legal` | Legal & Compliance | `/app/legal` | Legal, compliance |
| `ecommerce` | E-commerce | `/app/store` | Ecommerce team |
| `lms` | Learning & Development | `/app/learn` | All employees |

### Panel Access Rules
- A panel is only visible to a user if:
  1. The module is active for their tenant
  2. The user has at least one permission within that panel
- Filament's `canAccess()` method on each panel checks both conditions
- Super-admins see all panels regardless

---

## 6. Module Catalogue

Below is the complete list of all modules, their sub-modules, and what each one does.
Every module listed here is a buildable Filament resource or page group.

---

### CORE PLATFORM (Always Active)

#### Authentication & Identity
Multi-method login: email/password, Google OAuth, Microsoft OAuth, GitHub OAuth, SAML SSO, magic link, 2FA (TOTP app or SMS). Session management, device tracking, and admin impersonation.

#### Roles & Permissions (RBAC)
Spatie-powered role system. Custom role builder per workspace. Module-level access (can the user see this panel?), resource-level access (can they CRUD this record?), field-level visibility (can they see salary fields?). Temporary access grants, time-based windows, IP allowlisting.

#### Module Billing Engine
Stripe Billing integration. Tenants toggle modules on/off from the workspace settings panel. Metered usage events feed Stripe. Plan tiers: Starter, Pro, Enterprise. Per-seat or per-module pricing. Trial management. Invoice history per tenant.

#### Notifications & Alerts
Central hub. Every module dispatches notification events here. Users control: in-app bell, email digest (immediate / hourly / daily), Slack push, Teams push, SMS (Twilio), webhook. Quiet hours per user. Escalation rules.

#### API & Integrations Layer
REST API exposed per active module. GraphQL gateway (optional, enterprise tier). Inbound and outbound webhooks. API key management. Rate limiting. Native connectors: Google Workspace, Microsoft 365, QuickBooks, Xero, Shopify, WooCommerce, Stripe, Twilio, Zapier, Make.

#### Multi-Tenancy & Workspace
Workspace creation and setup wizard. Custom subdomain (`companyname.flowflex.com`). White-label branding (logo, colours, custom email sender). Custom domain (CNAME). Locale and timezone per workspace. Full data isolation. Per-tenant backup and restore.

---

### HR & PEOPLE MANAGEMENT

#### Employee Profiles & Directory
Central record for every person. All HR modules reference this. Personal info, employment details, department, org chart position, contract storage, custom fields, emergency contacts.

#### Onboarding
**Sub-modules:**
- Pre-boarding portal (tasks before start date, visible to new hire before day 1)
- Onboarding templates (clone per role/department)
- Task checklists (auto-assign on hire)
- Document collection (contracts, right-to-work, tax forms)
- Equipment requests (linked to asset management module if active)
- IT provisioning checklist (linked to IT module if active)
- Buddy/mentor assignment
- 30/60/90-day check-in triggers
- Progress tracker (manager visibility)

#### Offboarding
Exit checklist automation. Access revocation (fires event → IT module revokes access). Asset return tracking. Exit interview form. Final payroll trigger (fires event → Payroll module). Knowledge handover tasks.

#### Leave & Absence Management
**Sub-modules:**
- Leave request and approval workflow
- Balance tracking (accrual rules, carry-over policies)
- Company holiday calendar
- Policy rules per leave type (annual, sick, TOIL, maternity, paternity, unpaid)
- Multi-country leave law presets
- Blackout period rules (no leave during peak periods)
- Manager team visibility calendar

#### Payroll
**Sub-modules:**
- Salary and hourly payroll runs
- Auto-pull from time tracking (fires → payroll when time approved)
- Tax calculation engine (configurable per country)
- Deductions (pension, benefits, salary sacrifice)
- Bonus and commission payments
- Payslip generation (PDF, emailed to employee)
- Bank transfer output (BACS file UK, ACH US)
- Multi-currency support
- Hourly worker payroll
- Contractor / freelancer payments
- Payroll reporting and audit trail

#### Performance & Reviews
Goal setting (OKR and KPI frameworks). Review cycles (annual, quarterly, 360). Self-assessment forms. Manager review forms. Calibration sessions. Development plans linked to L&D module.

#### Recruitment & ATS
**Sub-modules:**
- Job posting builder
- Careers page embed (iframe or subdomain)
- Multi-channel job publishing (Indeed, LinkedIn via API)
- Applicant pipeline (kanban by stage)
- Interview scheduling (links to calendar)
- Scorecards per interview stage
- Offer letter generator (e-signature)
- Background check integration (third-party)
- Referral programme tracking
- Candidate email sequences
- On-hire: auto-creates Employee Profile record

#### Scheduling & Shifts
Drag-and-drop shift builder. Availability capture per employee. Shift swap requests and manager approval. Overtime tracking. POS integration (if Operations/POS module active). Clock-in/clock-out (mobile and tablet). Rota publishing and employee view.

#### Benefits & Perks
Benefits catalogue (health, pension, perks). Employee enrolment portal. Eligibility rules by role, tenure, or location. Costs auto-sync to payroll deductions.

#### Employee Feedback & Engagement
Pulse surveys (scheduled). eNPS tracking over time. Anonymous feedback channels. Sentiment dashboard. Burnout signal detection (commit patterns, overtime, leave usage). Recognition and kudos system.

#### HR Compliance & Certifications
Mandatory training completion tracking (linked to LMS module if active). Certification expiry alerts. Policy acknowledgement sign-off (linked to Legal module if active). Right-to-work check storage. GDPR employee data handling. Audit trail per employee.

---

### PROJECTS & WORK MANAGEMENT

#### Task Management
**Sub-modules:**
- Kanban board (custom columns/statuses)
- List view
- Calendar view
- Timeline view (Gantt-lite)
- Subtasks (unlimited nesting)
- Task dependencies
- Priority levels (custom or P1–P4)
- Recurring tasks
- Task templates
- Automations (if X then Y, e.g. "when status = Done, notify assignee's manager")
- My Work view (personal task inbox)
- Bulk actions

#### Project Planning
Full Gantt chart. Milestones. Dependencies (Finish-to-Start, Start-to-Start, Finish-to-Finish). Critical path detection. Resource allocation per project. Baseline vs actual tracking. Project templates. Project health status (on track / at risk / delayed).

#### Time Tracking
One-click timer or manual entry. Tag to project, client, task. Billable vs non-billable flag. Weekly timesheet view. Approval workflow (employee submits → manager approves). Overtime alerts. Feeds automatically to Payroll and Client Billing modules via events.

#### Document Management
**Sub-modules:**
- File upload and storage (S3/R2)
- Folder structure (project/department/client scoped)
- Version history (retain all versions)
- File permissions (view / edit / download per user or role)
- Preview (PDF, images, video, Office files)
- Full-text search (including OCR on scanned PDFs)
- External sharing links (time-limited, password-optional)
- Google Drive sync
- OneDrive sync
- Document expiry and archiving

#### Document Approvals & E-Sign
Approval workflow builder (sequential or parallel chains). Multi-step sign-off. E-signature (legally binding, DocuSign-style built in). Rejection and revision cycle. Audit trail (who saw, who signed, when). Deadline reminders. Template library.

#### Knowledge Base & Wiki
Block-based rich text editor. Categories and tags. Full-text search. Page change history and contributor tracking. Comments and suggestions. Public-facing or internal-only per article. Useful as employee handbook, SOPs, runbooks.

#### Team Collaboration
Task comments and threads. @mentions. Activity feed per project. File attachments in comments. Reactions. Project announcements (pinned updates).

#### Resource & Capacity Planning
Workload heatmap per team member. Capacity by role or individual. Availability calendar. Project demand forecasting. Prevents double-booking. Flags over-allocation before it's a problem.

#### Agile & Sprint Management
Backlog management. Sprint creation with date range. Story point estimation. Burndown chart. Velocity tracking across sprints. Sprint retrospective notes. Works alongside Project Planning — both can be active simultaneously.

---

### FINANCE & ACCOUNTING

#### Invoicing
Invoice builder from scratch or auto-generated from approved time entries or project milestones. Recurring invoices. Partial payments. Late fee automation. Multi-currency. Credit notes. PDF generation and email delivery. Payment status tracking.

#### Expense Management
Employee expense submission via mobile (receipt photo). OCR receipt scanning. Category rules. Approval workflow. Reimbursement via payroll (fires event → Payroll). Mileage tracking. Per diem rates.

#### Accounts Payable & Receivable
Supplier bill management. Payment runs. Aged debtor and aged creditor reports. Purchase order matching. Payment reminder sequences.

#### Bank Reconciliation
Open Banking connection (Plaid / TrueLayer). Auto-matching rules (match bank transaction to invoice or bill). Manual reconciliation for unmatched items. Multi-account support. Bank feed import.

#### Budgeting & Forecasting
Department and project budgets. Actual vs budget variance tracking. Rolling forecasts. Over-budget alerts before month end. Scenario planning (best/worst/base case).

#### Financial Reporting
P&L statement. Balance sheet. Cash flow statement. Custom management accounts report builder. Scheduled delivery (email as PDF on cadence). Export to Excel and PDF.

#### Client Billing & Retainers
Time-to-invoice conversion (pulls from time tracking). Retainer management (monthly cap, rollover rules). Project-based milestone billing. Client payment portal (branded). Unbilled time alerts.

#### Tax & VAT Compliance
VAT / GST / sales tax calculation on invoices. Multi-jurisdiction tax rule engine. MTD-compatible submission (UK). Audit-ready transaction records.

#### Fixed Asset & Depreciation
Asset register (financial view). Depreciation schedule auto-calculation (straight-line, reducing balance). Disposal recording. NBV reporting. Integrates with Operations/Asset Management module.

#### Subscription & MRR Tracking
For SaaS/subscription businesses using FlowFlex. MRR / ARR dashboard. Churn tracking. Expansion revenue. Revenue recognition (ASC 606 / IFRS 15 rules). Cohort analysis.

---

### CRM & SALES

#### Contact & Company Management
360° contact and company records. Full activity timeline (emails, calls, meetings, deals). Custom fields. Tags and segmentation. Data enrichment (LinkedIn / Clearbit). Duplicate detection and merge.

#### Sales Pipeline
Visual deal pipeline with custom stages. Probability weighting. Revenue forecasting. Win/loss tracking with reason codes. Multiple pipelines (by product, region, team). Deal rotation rules. Stale deal alerts.

#### Quotes & Proposals
Quote builder from product catalogue. Discount rules. Convert quote to invoice in one click. View tracking (know when client opens it). E-signature on acceptance. Auto-creates deal in pipeline.

#### Shared Inbox & Email
Shared team inbox (sales@, support@). Email sequences (drip campaigns for sales). Templates. Open and click tracking. Auto-log all emails to contact timeline. Inbox assignment rules.

#### Customer Data Platform (CDP)
Unified customer profile across all modules. Dynamic segmentation. Behaviour event tracking. Source attribution. Identity resolution (same person in multiple modules). Syncs to Marketing module for targeting.

#### Customer Support & Helpdesk
**Sub-modules:**
- Ticket management (email, form, chat)
- SLA rules (response and resolution targets)
- Escalation paths
- Canned responses library
- CSAT surveys (auto-send post-resolution)
- Multi-channel (email, live chat widget, form)
- Live chat widget (embeddable on website)
- Rule-based chatbot
- Customer self-service portal
- Slack-native ticketing option

#### Client Portal
Branded self-service portal your clients log into. Project status view. Invoice payment. Document sharing. Support ticket raising. Approval request signing. No FlowFlex branding — fully white-labelled.

#### Loyalty & Retention
Points / rewards programme. Referral tracking. Churn prediction score (ML-based risk flag). Win-back campaign triggers (fires to Marketing module). Tier management.

---

### MARKETING & CONTENT

#### CMS & Website Builder
Block-based page builder. Blog / news. SEO meta fields. Media library. Multi-language (i18n). Scheduled publishing. Redirect manager. Template library. A/B test pages. Headless CMS API for decoupled frontends.

#### Email Marketing
Campaign builder. Drag-and-drop email editor. Automation flows (welcome series, abandoned cart, re-engagement). A/B testing (subject line, content). Open / click analytics. Unsubscribe and bounce management. Deliverability tools (DKIM, SPF, DMARC status).

#### Social Media Management
Content calendar. Multi-channel publishing (LinkedIn, Instagram, Facebook, X, TikTok). Approval workflow for posts. Best-time suggestions (based on engagement data). Performance analytics per channel.

#### Forms & Lead Capture
Drag-and-drop form builder. Conditional logic. File upload fields. Embed via iframe or JS snippet. On submit: auto-create CRM contact, trigger workflow, fire notification. Spam protection (CAPTCHA). Multi-step forms.

#### SEO & Analytics
Keyword rank tracking. Technical SEO audit. Backlink monitoring. Google Search Console integration. GA4 integration. Competitor tracking.

#### Ad Campaign Management
Google Ads account connection. Meta Ads connection. Spend tracking. ROAS dashboard. Conversion attribution. Unified spend vs organic dashboard.

#### Events & Webinars
Event registration page builder. Attendee management. Reminder email/SMS sequences. QR code check-in (mobile). Post-event follow-up automation. Webinar platform integration (Zoom, Teams).

#### Affiliate & Partner Management
Affiliate portal (branded). Referral link generation per affiliate. Commission rules (percentage, flat, tiered). Payout management (via Finance module). Performance reports.

---

### OPERATIONS & FIELD SERVICE

#### Inventory Management
**Sub-modules:**
- Real-time stock levels
- Multi-location warehouse support
- Reorder point alerts (auto-create PO if active)
- Barcode / QR scanning (mobile app)
- Batch and serial number management
- Stock adjustments (write-off, correction)
- Supplier tracking
- Stock forecasting
- FIFO / LIFO / FEFO costing methods
- Returns and RMA handling
- Stocktake / cycle count workflows

#### Purchasing & Procurement
Purchase order builder. Supplier approval lists. Goods receipt notes. 3-way matching (PO → receipt → invoice). Approval thresholds by amount. Supplier portal (external login for suppliers to confirm orders).

#### Asset Management
Track every physical asset. Assignment to employee or location. QR code asset labels. Lifecycle stages (in use / in storage / disposed). Check-out / check-in workflow. IT asset tracking (linked to IT module). Disposal recording.

#### Equipment Maintenance (CMMS)
Preventive maintenance schedules. Reactive work orders. Fault reporting (mobile-friendly). Technician dispatch (linked to Field Service if active). Parts usage tracking. Full maintenance history per asset. Downtime tracking.

#### Field Service Management
Job dispatch to technicians. Live map of field team locations (GPS). Route optimisation. Mobile job app (offline-capable). Customer arrival notification (SMS). Digital job completion sign-off (customer signature on mobile). Parts used on-site (deducts from inventory).

#### Supply Chain Visibility
Order tracking from supplier to warehouse to customer. Supplier scorecards. Lead time tracking. Delay alerts. Landed cost calculation.

#### Point of Sale (POS)
Tablet / web-based POS for retail and hospitality. Product catalogue (from Ecommerce module if active). Cash and card payment processing. Email or print receipts. Real-time inventory sync on every sale. End-of-day Z-reports. Split payments.

#### Quality Control & Inspections
Digital inspection checklists. Pass/fail scoring with weighted criteria. Photo evidence capture (mobile). Non-conformance report (NCR) raising. Corrective action tracking. ISO audit readiness package.

#### Health, Safety & Environment (HSE)
Incident reporting (mobile and desktop). Risk assessments. Hazmat / COSHH register. Toolbox talk logging with attendance. Safety induction completion tracking. RIDDOR report generation (UK). Near-miss reporting.

---

### ANALYTICS, BI & REPORTING

#### Custom Dashboards
Drag-and-drop widget builder. Data sources from any active module. Role-based dashboard views (each team sees their metrics). Auto-refresh intervals. Share or embed dashboard (external link).

#### Report Builder
Self-serve report builder — no SQL needed. Filter, group, sort, pivot. Scheduled report delivery (email as PDF or Excel). Saved report library. Cross-module data joins.

#### KPI & Goal Tracking
Define company KPIs and cascade to departments and individuals. Traffic-light status (green/amber/red). Trend charts over time. Threshold alerts. Benchmark comparisons.

#### Data Warehouse & Export
For enterprise customers. BigQuery sync. Snowflake connector. S3 / Azure Blob export. Scheduled ETL jobs. Data retention policy management.

#### Audit Log & Activity Trail
Immutable log of every action. Who changed what, when, from which IP. Before and after values. Filter by user / module / action type. Export for compliance.

#### Team Velocity & Operations Metrics
Cycle time tracking. Throughput charts. Bottleneck detection. Predictive delivery estimates. Cross-team comparison. Burnout signal flags.

---

### IT & SECURITY MANAGEMENT

#### IT Asset Management (ITAM)
Full hardware and software lifecycle tracking. Licence compliance. Renewal alerts. Warranty management. Cost per asset. Compliance dashboard. Integrates with Onboarding/Offboarding flows.

#### Internal IT Helpdesk
Employee-facing portal for IT issues. Hardware fault reporting. Software request. Access request management. SLA tracking. Knowledge base for self-service resolution (linked to KB module).

#### SaaS Spend Management
SaaS discovery (detects all tools in use, including shadow IT). Spend tracking. Licence optimisation recommendations. Renewal calendar. Usage analytics per tool. Alerts for duplicate tools or unused licences.

#### Access & Permissions Audit
Cross-system access map (who has access to what). Overprovision alerts. Periodic access review cycles. Auto-revoke on offboarding (fires event from HR Offboarding). Principle of least privilege enforcement.

#### Security & Compliance
GDPR / ISO 27001 / SOC 2 readiness tooling. Data processing register. DPIA templates. Breach reporting workflow. Evidence collection and packaging for auditors. ISO 27001 controls tracker.

#### Uptime & Status Monitoring
Monitor internal or client-facing services. Public status page (hosted on FlowFlex subdomain). Incident management workflow. Alert rules (email, Slack, SMS). Postmortem templates.

---

### LEGAL & COMPLIANCE

#### Contract Management (CLM)
Full contract lifecycle. Template library. Redlining and version control. Approval chains. E-signature. Auto-renewal alerts. Contract repository with full-text search. Expiry tracking and renewal pipeline.

#### Policy Management
Company policy publishing with version control. Employee acknowledgement (must-read). Compliance tracking (who has signed, who hasn't). Scheduled review reminders per policy.

#### Risk Register
Risk identification and categorisation. Likelihood × impact matrix scoring. Risk ownership assignment. Mitigation action tracking. Board-level risk report export.

#### Data Privacy (GDPR / CCPA)
Data subject request (DSR) handling workflow. Right to erasure automation (deletes PII across all modules). Consent management. Data processing register (Article 30). Cookie consent banner (embeddable). Privacy policy generator.

#### Insurance & Licence Tracking
Business insurance policy register. Regulatory licence register (trade licences, professional certifications). Expiry alerts. Document vault per policy. Renewal reminders to responsible owner.

---

### E-COMMERCE & SALES CHANNELS

#### Product Catalogue
Centralised product database. Variants and attributes. Pricing rules and tiers. Tax codes. Image gallery. Bulk import/export. Pushes to storefront, POS, and quoting in one update.

#### Order Management
**Sub-modules:**
- Multi-channel order import (website, POS, marketplaces)
- Order status workflow (custom stages)
- Fulfillment tracking
- Partial and split shipping
- Returns and refunds (linked to Finance)
- Packing slip generation
- Dropshipping supplier routing
- 3PL integration (ShipBob, ShipStation)
- Shipping label printing
- Customs documentation

#### Storefront & Checkout
Customisable branded storefront. Product pages. Cart and checkout. Payment methods: Stripe, PayPal, Klarna, Apple Pay, Google Pay. Guest checkout. Discount and coupon codes.

#### Marketplace Channel Sync
Sync listings and orders from Amazon, eBay, Etsy. Centralised stock control (prevents overselling across channels). Listing management from one place.

#### Subscription Products
Recurring billing cadences (weekly, monthly, annual). Customer pause and cancel flows (self-serve). Dunning management for failed payments. Box and bundle builder. Delivery schedule management.

#### Digital Products & Downloads
File delivery automation post-purchase. Download limits. Licence key generation. Access expiry. Bundle pricing.

---

### COMMUNICATIONS & INTERNAL COMMS

#### Internal Messaging & Chat
Real-time channels, direct messages, and threads. File sharing. Task linking (reference a task from a message). Search history. Reactions. Acts as a Slack alternative for teams that want everything in one place.

#### Company Announcements
Internal broadcast tool. Target audience (all staff / specific team / location). Read receipt tracking. Acknowledgement required flag. Announcement archive.

#### Meeting & Video Integration
Schedule and join Google Meet / Zoom from within the platform. Auto-create meeting notes. Action item capture. Recording link stored against project or task.

#### Company Intranet & Noticeboard
Homepage for the organisation. Company news feed. Quick links. Org chart view. Events calendar. Team spotlights. Customisable homepage widgets per role.

#### Booking & Appointment Scheduling
Booking page builder (Calendly-style). Availability rules per person or team. Calendar sync (Google Calendar, Outlook). Buffer times. Group bookings. Confirmation and reminder emails/SMS. Round-robin team booking.

---

### LEARNING & DEVELOPMENT (LMS)

#### Course Builder & LMS
**Sub-modules:**
- Course builder (video, document, quiz, embed)
- Course assignment by role or department
- Completion tracking with progress %
- Certificate generation (PDF, branded)
- Leaderboards and gamification
- External customer training portal option
- SCORM import (for existing e-learning content)
- AI-assisted quiz generation from course content

#### Skills Matrix & Gap Analysis
Skills library (taxonomy). Role skill requirements definition. Employee skill self-assessment. Gap analysis dashboard. Training recommendations based on gaps (linked to course catalogue).

#### Succession Planning
9-box talent grid (performance vs potential). Succession readiness scoring per role. Key role risk flags (single-person dependency). Development path builder.

#### Mentoring & Coaching
Mentor matching (skills and goals-based). Session scheduling. Progress tracking. Programme management (cohorts). Internal coaching network directory.

#### External Training Requests
Employee training request form (conference, course, certification). Manager approval workflow. L&D budget tracking. Post-training notes and knowledge sharing back to KB.

---

## 7. Data Interconnection Map

This shows which modules automatically share data via events when both are active:

| Event source | Event fired | Modules that react |
|---|---|---|
| HR / Onboarding | `EmployeeHired` | IT (provision access), Payroll (add to run), Scheduling (add to rota), LMS (assign onboarding course) |
| HR / Offboarding | `EmployeeOffboarded` | IT (revoke access), Payroll (final run), Asset Management (recall assets) |
| Time Tracking | `TimeEntryApproved` | Payroll (add to pay run), Finance/Client Billing (mark billable) |
| Projects | `ProjectMilestoneCompleted` | Finance (trigger milestone invoice), CRM (update deal status) |
| Finance / Invoicing | `InvoiceOverdue` | CRM (create follow-up task), Notifications (alert account manager) |
| Inventory | `StockBelowReorderPoint` | Purchasing (create draft PO) |
| Field Service | `JobCompleted` | Finance (create invoice), Inventory (deduct parts used), CRM (update ticket) |
| CRM / Support | `TicketResolved` | Marketing (trigger CSAT survey), CRM (update contact timeline) |
| Recruitment | `CandidateHired` | HR (create employee profile), Onboarding (start onboarding flow) |
| E-commerce | `OrderPlaced` | Inventory (deduct stock), Finance (record revenue), CRM (update customer record) |
| LMS | `CourseCompleted` | HR Compliance (mark certification fulfilled), HR Performance (log development activity) |

---

## 8. Naming Conventions

### Files & Classes
- Models: `PascalCase` singular — `Employee`, `PayRun`, `SalesOpportunity`
- Controllers: `PascalCase` + `Controller` — only for API routes
- Filament Resources: `PascalCase` + `Resource` — `EmployeeResource`
- Filament Pages: `PascalCase` — `ManagePayRoll`, `ViewDashboard`
- Events: past tense, descriptive — `EmployeeHired`, `TimeEntryApproved`, `InvoiceOverdue`
- Listeners: imperative action — `CreateOnboardingTasks`, `RevokeTenantAccess`
- Services: noun + Service — `PayrollCalculationService`, `StripeWebhookService`
- Jobs: imperative verb — `GeneratePayslipPDF`, `SyncInventoryToMarketplace`

### Database
- Tables: `snake_case` plural — `employees`, `pay_runs`, `time_entries`
- All tables include: `id` (ULID preferred), `tenant_id`, `created_at`, `updated_at`
- Soft deletes (`deleted_at`) on any table where data should be recoverable
- Foreign keys: `{model}_id` — `employee_id`, `project_id`
- Pivot tables: alphabetical order — `employee_role`, `module_tenant`
- Indexes: all foreign keys, all commonly filtered columns (`status`, `tenant_id`, `created_at`)

### Routes
- API routes: `/api/v1/{module}/{resource}` — `/api/v1/hr/employees`
- Filament panel routes: managed by Filament automatically
- Webhook routes: `/webhooks/{provider}` — `/webhooks/stripe`

---

## 9. Module Development Checklist

When building a new module, follow this checklist in order:

- [ ] Create `Modules/{ModuleName}/Providers/{ModuleName}ServiceProvider.php` and register in `config/app.php`
- [ ] Create `config/{module_name}.php` with module metadata (name, version, dependencies)
- [ ] Add module to `modules` registry table via seeder
- [ ] Create all database migrations under `Modules/{ModuleName}/database/migrations/`
- [ ] Every table must have `tenant_id`, timestamps, and soft deletes
- [ ] Create Eloquent models with `BelongsToTenant` trait applied
- [ ] Register all Spatie permissions for this module: `{module}.{resource}.{action}`
- [ ] Create Filament Panel (or add resources to existing panel)
- [ ] Create Filament Resources with proper auth policies
- [ ] Register all Events and Listeners in the ServiceProvider
- [ ] Expose API routes in `Modules/{ModuleName}/routes/api.php`
- [ ] Write Feature tests for all key flows
- [ ] Document all events fired and all events listened to in `Modules/{ModuleName}/README.md`

---

## 10. Security Rules

- **Never trust user input.** All form data goes through Laravel Form Requests with explicit validation rules.
- **All API endpoints require authentication.** No public endpoints unless explicitly intended (e.g. booking page, storefront).
- **All queries are tenant-scoped.** A global scope applies `WHERE tenant_id = :current_tenant_id` to every query. Never bypass this.
- **Sensitive fields are encrypted at rest.** Bank details, national insurance numbers, salary data, API keys. Use Laravel's `encrypted` cast.
- **Audit every write.** Use Spatie Activity Log on all models where data changes matter. This is non-optional.
- **File access is signed and expiring.** Never expose raw S3 URLs. Always use signed temporary URLs with expiry.
- **Rate limit all APIs.** Default: 60 req/min per API key. Configurable per tenant tier.
- **RBAC on every Filament resource.** Every `Resource` must implement `canViewAny()`, `canCreate()`, `canEdit()`, `canDelete()` and check the authenticated user's permissions.

---

## 11. Performance Rules

- **Never load N+1 queries.** Always eager-load relationships. Use `with()` and Filament's `->with()` on tables.
- **Queue all slow jobs.** PDF generation, payslip creation, report building, email sends, API sync jobs — all queued. Never block the HTTP request.
- **Cache expensive reads.** Module registry (is module X active for tenant Y?), permission checks, dashboard aggregates. Use Redis with explicit cache keys per tenant. Bust on change.
- **Paginate all table views.** Default 25 rows. No `->get()` on large tables.
- **Use database indexes.** Every `tenant_id`, every status column, every foreign key. Check query plans in development.

---

## 12. Environment Setup

```bash
# Clone and install
composer install
npm install

# Environment
cp .env.example .env
php artisan key:generate

# Database
php artisan migrate --seed

# Build assets
npm run dev

# Queue worker (required for background jobs)
php artisan queue:work --queue=high,default,low

# Start development server
php artisan serve
```

Key `.env` variables:

```dotenv
APP_NAME=FlowFlex
APP_URL=https://app.flowflex.com

# Multi-tenancy
CENTRAL_DOMAIN=flowflex.com

# Database
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=flowflex

# Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Stripe
STRIPE_KEY=
STRIPE_SECRET=
STRIPE_WEBHOOK_SECRET=

# Storage
FILESYSTEM_DISK=s3
AWS_BUCKET=
AWS_DEFAULT_REGION=

# Mail
MAIL_MAILER=resend
RESEND_API_KEY=

# SMS
TWILIO_SID=
TWILIO_AUTH_TOKEN=
TWILIO_FROM=

# Real-time
PUSHER_APP_ID=
PUSHER_APP_KEY=
PUSHER_APP_SECRET=
```

---

## 13. MVP Build Order

Build in this sequence. Each phase should be deployable and sellable on its own.

### Phase 1 — Foundation (Month 1)
Core Platform: authentication, RBAC, multi-tenancy, workspace setup, module billing engine, notifications, file storage.

### Phase 2 — First Module Cluster (Month 2)
HR & People: employee profiles, onboarding, leave management, payroll (basic).
Projects & Work: task management (kanban + list), time tracking, document management.

### Phase 3 — Finance & CRM (Month 3)
Finance: invoicing, expense management, basic financial reporting.
CRM: contacts, sales pipeline, shared inbox, customer support helpdesk.

### Phase 4 — Operations & Marketing (Month 4)
Operations: inventory management, asset management, purchasing.
Marketing: CMS, email marketing, forms and lead capture.

### Phase 5 — Extended Modules (Month 5–6)
Analytics & BI, IT Management, Legal & Compliance, E-commerce, Learning & Development, Communications.

### Phase 6 — Enterprise & Scale
Data warehouse exports, SOC 2 compliance tooling, advanced multi-region support, API marketplace (let third-party developers build FlowFlex modules).

---

## 14. Brand & Voice

**Name:** FlowFlex  
**Tagline:** *Your business, your tools — in flow.*  
**Tone:** Direct, calm, confident. Not startup-hype, not enterprise-stiff.  
**Values:**  
- Simplicity over features — we don't add a feature if it adds confusion  
- Modularity is a feature — never force a customer to use what they don't need  
- Data integrity is sacred — if two modules share data, it must always be consistent  
- Speed matters — pages load fast, actions respond immediately, reports don't hang  

**What FlowFlex is not:**
- Not another Salesforce (we don't charge €300/seat for features you'll never touch)
- Not a feature factory (we build deep, not wide)
- Not opinionated about your industry (the module system handles vertical differences)

---

*Last updated: May 2026*  
*Maintained by: Max (Founder)*  
*Stack: Laravel 12 · Filament 5 · PostgreSQL · Redis · Stripe · AWS S3*
