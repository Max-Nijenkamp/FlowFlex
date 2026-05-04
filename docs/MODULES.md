# MODULES.md — FlowFlex Platform Module Architecture

> This file is the definitive reference for every domain, module, sub-module, and sub-sub-module in the FlowFlex platform.
> Every feature described here maps to a Filament resource, page, or widget in the codebase.
> Use this file when planning sprints, scoping modules, writing migrations, or understanding how the platform fits together.

---

## How to Read This File

Each section follows this structure:

```
## Domain Name
Domain description and purpose.

### Module Name
Module description — what it does, who uses it, what problem it solves.

**Who uses it:** Role/persona
**Filament Panel:** panel-id
**Depends on:** Other modules (if any)
**Events fired:** EventName — description
**Events consumed:** EventName (from Module) — what this module does with it

#### Sub-module Name
Description of the sub-module.

**Features:**
- Feature name — description
- Feature name — description

##### Sub-sub-module Name (where scope demands it)
Description and features.
```

---

## Domain Map Overview

| # | Domain | Panel | Modules | Status |
|---|---|---|---|---|
| 0 | Core Platform | `admin`, `workspace` | 6 | Always active |
| 1 | HR & People | `hr` | 11 | Optional |
| 2 | Projects & Work | `projects` | 9 | Optional |
| 3 | Finance & Accounting | `finance` | 10 | Optional |
| 4 | CRM & Sales | `crm` | 8 | Optional |
| 5 | Marketing & Content | `marketing` | 8 | Optional |
| 6 | Operations & Field Service | `operations` | 9 | Optional |
| 7 | Analytics, BI & Reporting | `analytics` | 6 | Optional |
| 8 | IT & Security Management | `it` | 6 | Optional |
| 9 | Legal & Compliance | `legal` | 5 | Optional |
| 10 | E-commerce & Sales Channels | `ecommerce` | 6 | Optional |
| 11 | Communications & Internal Comms | `communications` | 5 | Optional |
| 12 | Learning & Development (LMS) | `learning` | 5 | Optional |

**Total: 99 modules and sub-modules across 13 domains.**

---

---

# DOMAIN 0 — Core Platform

**Always active. Cannot be disabled. Powers every other domain.**

The Core Platform is the invisible foundation under every module. It handles identity, access, tenancy, billing, notifications, file storage, and the event bus that connects modules together. No module exists without it.

**Filament Panels:** `admin` (FlowFlex super-admin), `workspace` (per-tenant settings)

---

### Module: Authentication & Identity

Every user across every tenant authenticates through this module. It is the single identity layer for the entire platform — one login, every panel.

**Who uses it:** All users, admins, FlowFlex staff
**Depends on:** Nothing (foundational)

**Events fired:**
- `UserLoggedIn` — triggers audit log entry
- `UserLoggedOut` — ends session record
- `UserPasswordChanged` — security notification dispatched
- `TwoFactorEnabled` — security confirmation notification
- `SuspiciousLoginDetected` — alert to workspace admin

#### Sub-module: Login Methods
The methods by which users authenticate into FlowFlex.

**Features:**
- Email and password — standard credential login with bcrypt hashing
- Google OAuth 2.0 — one-click login via Google account
- Microsoft OAuth — login via Microsoft / Azure AD account
- GitHub OAuth — login via GitHub account (useful for developer-heavy teams)
- SAML SSO — enterprise single sign-on for large organisations using identity providers (Okta, Azure AD, Auth0)
- Magic link — passwordless email link login (expires in 15 minutes)
- Passkey / WebAuthn — biometric and hardware key authentication

#### Sub-module: Two-Factor Authentication (2FA)
Additional verification layer on top of any login method.

**Features:**
- TOTP authenticator app (Google Authenticator, Authy, 1Password)
- SMS OTP via Twilio (fallback option)
- Backup recovery codes (10 single-use codes generated on 2FA setup)
- Workspace admin can enforce 2FA as mandatory for all users
- Grace period setting (require 2FA within N days of account creation)

#### Sub-module: Session Management
**Features:**
- Active session list per user (device, IP, location, last active)
- Revoke individual sessions remotely
- Revoke all other sessions ("sign out everywhere")
- Session timeout configuration per workspace (15min / 1hr / 8hr / never)
- Device fingerprinting for suspicious login detection
- Concurrent session limits (enterprise tier)

#### Sub-module: Admin Impersonation
**Features:**
- FlowFlex super-admins can impersonate any tenant user for support purposes
- Workspace admins can impersonate any user within their tenant
- All impersonation sessions are logged with reason field
- A visible banner appears when impersonating ("You are viewing as Jane Smith")
- Impersonated sessions cannot change passwords or billing settings

---

### Module: Roles & Permissions (RBAC)

Granular access control built on Spatie Laravel Permission. Every panel, resource, and action is gated through this module.

**Who uses it:** Workspace admins, team managers
**Depends on:** Authentication & Identity

**Events fired:**
- `RoleCreated` — audit log
- `PermissionGranted` — audit log with user + permission + granter
- `PermissionRevoked` — audit log

#### Sub-module: Role Builder
**Features:**
- Create custom roles with any combination of permissions
- Role naming and description
- Role inheritance (a Senior Manager role extends Manager role)
- Assign roles to users individually or in bulk
- Clone existing roles as starting point
- System roles (Owner, Admin, Member, Read-only) that cannot be deleted

#### Sub-module: Permission Layers

Permissions operate at three levels:

**Panel-level:** Can the user access this Filament panel at all?
Example: `hr.panel.access`, `finance.panel.access`

**Resource-level:** Can the user perform CRUD on a resource within a panel?
Example: `hr.employees.view`, `hr.employees.create`, `hr.employees.edit`, `hr.employees.delete`

**Field-level:** Can the user see or edit a specific field on a record?
Example: `hr.employees.salary.view`, `hr.employees.salary.edit`

**Features:**
- Permission matrix UI (grid of roles vs permissions, toggle cells)
- Permission search and filter
- "Effective permissions" view (shows what a specific user can actually do, including inherited)

#### Sub-module: Scoping & Restrictions
**Features:**
- Department scoping (a manager sees only their department's records)
- Team scoping (a team lead sees only their team)
- Location scoping (a regional manager sees only their region)
- IP allowlist per role (certain roles can only log in from specific IPs)
- Time-based access windows (contractors can only access Mon-Fri 09:00-18:00)
- Temporary access grants with automatic expiry date

---

### Module: Module Billing Engine

The commercial engine of FlowFlex. Tracks which modules each tenant has active, meters usage, and bills via Stripe.

**Who uses it:** Workspace owners/admins, FlowFlex billing system
**Depends on:** Authentication & Identity, Multi-tenancy

**Events fired:**
- `ModuleActivated` — tenant turned on a module
- `ModuleDeactivated` — tenant turned off a module
- `SubscriptionUpgraded` — plan tier changed
- `SubscriptionDowngraded` — plan tier reduced
- `PaymentFailed` — triggers grace period and notifications
- `TrialExpired` — tenant trial ended without converting

#### Sub-module: Module Toggle
**Features:**
- Module marketplace — grid of all available modules with descriptions and pricing
- Toggle modules on/off per tenant (data is never deleted on deactivation, only hidden)
- Module dependencies shown ("Finance requires Core Invoicing")
- Preview mode — explore a module's UI without activating it (read-only demo data)
- Module activation wizard (guided setup for complex modules like Payroll)

#### Sub-module: Plan Management
Plan tiers control both access to modules and usage limits.

**Tier: Starter**
- Up to 10 users
- Up to 5 modules active
- 5GB file storage
- Standard support

**Tier: Pro**
- Up to 100 users
- Unlimited modules
- 100GB file storage
- Priority support
- API access

**Tier: Enterprise**
- Unlimited users
- Unlimited modules
- Unlimited file storage
- Custom SLA
- Dedicated account manager
- SSO + SCIM provisioning
- Custom contracts

**Features:**
- Plan comparison and upgrade flow (self-serve)
- Stripe Billing integration (subscriptions, proration, invoices)
- Annual discount toggle (2 months free on annual)
- Seat-based and module-based pricing combined
- Overage handling (soft limits with notifications)

#### Sub-module: Usage Metering
**Features:**
- Event-based metering (each module emits usage events)
- Usage dashboard per tenant (see what you're consuming)
- Billing period usage summary
- Alerts when approaching plan limits (at 80% and 100%)
- Historical usage graphs

#### Sub-module: Trial Management
**Features:**
- 14-day free trial (configurable per module)
- Trial includes all Pro features
- Trial countdown banner in the workspace
- Conversion flow at trial expiry
- FlowFlex sales team notification when high-value trial starts

---

### Module: Notifications & Alerts

The central notification hub. Every module dispatches notifications here. Users control how and where they receive them.

**Who uses it:** All users
**Depends on:** Authentication & Identity

#### Sub-module: Notification Channels
**Features:**
- In-app bell notification centre (with unread count badge)
- Email notifications (Resend/Mailgun, templated, branded per workspace)
- Slack push (webhook-based, workspace-level Slack connection)
- Microsoft Teams push (webhook-based)
- SMS via Twilio (for urgent alerts only)
- Browser push notifications (web push API)
- Webhook dispatch (POST to a URL the workspace configures)

#### Sub-module: User Preferences
**Features:**
- Per-user notification preferences per notification type
- Digest options: immediate, hourly, daily, weekly
- Quiet hours (e.g. 22:00–08:00 no notifications)
- Channel priority per notification type (e.g. "Invoice overdue → email + Slack, not SMS")
- Notification mute per record (mute notifications for specific project, ticket, etc.)

#### Sub-module: Escalation Rules
**Features:**
- Escalation chains (if not acknowledged in N minutes, escalate to manager)
- Priority levels on notifications (Low / Normal / High / Critical)
- Critical notifications bypass quiet hours
- Workspace-level defaults (HR admin sets what's default for all new users)

---

### Module: API & Integrations Layer

Every active module exposes its data through this layer. Third-party apps and automation tools connect here.

**Who uses it:** Developers, integration specialists, automation workflows
**Depends on:** Authentication & Identity, RBAC

#### Sub-module: REST API
**Features:**
- Versioned REST API (`/api/v1/`)
- Endpoints per active module (inactive modules return 404)
- Full CRUD where permissions allow
- Consistent response envelope: `{ data, meta, errors }`
- OpenAPI / Swagger documentation auto-generated
- Postman collection export
- API playground in the workspace dashboard

#### Sub-module: API Key Management
**Features:**
- Generate named API keys per workspace
- Scoped keys (read-only, specific modules only)
- Key expiry dates
- Last used timestamp
- Revoke keys instantly
- Key rotation reminder alerts

#### Sub-module: Webhooks
**Features:**
- Outbound webhook: subscribe to events and POST to any URL
- Event selection per webhook (choose which events trigger it)
- Secret signing (HMAC-SHA256) for webhook verification
- Delivery log (see payload, response code, retry history)
- Manual retry on failed deliveries
- Inbound webhook URLs for integrations that push data in

#### Sub-module: Native Connectors
Pre-built integrations that require minimal setup:

**Productivity:**
- Google Workspace (Calendar sync, Drive file picker, Gmail logging)
- Microsoft 365 (Outlook calendar sync, OneDrive, Teams notifications)

**Accounting:**
- QuickBooks Online (bidirectional sync: invoices, expenses, contacts)
- Xero (bidirectional sync: invoices, expenses, bank feeds)

**E-commerce:**
- Shopify (products, orders, customers)
- WooCommerce (products, orders, customers)

**Payments:**
- Stripe (customer data, payment events)

**Communication:**
- Twilio (SMS sending, phone number management)
- SendGrid / Resend (transactional email fallback)

**Automation:**
- Zapier (bi-directional: trigger and action zaps)
- Make / Integromat (same as Zapier)

---

### Module: Multi-Tenancy & Workspace Management

Manages the complete isolation and configuration of each tenant workspace.

**Who uses it:** Workspace owners, FlowFlex super-admins
**Depends on:** Nothing (foundational alongside Core Auth)

#### Sub-module: Workspace Setup
**Features:**
- Workspace creation wizard (name, industry, size, primary currency, timezone, locale)
- Subdomain assignment (`yourcompany.flowflex.com`)
- Custom domain (CNAME record setup with DNS verification)
- Workspace avatar / logo upload
- Primary owner designation
- Initial module selection

#### Sub-module: Branding & White-label
**Features:**
- Logo upload (light and dark versions)
- Primary brand colour (used in emails and client-facing portals)
- Email sender name and from-address (e.g. "Acme Corp via FlowFlex")
- Client portal custom branding (completely white-labelled, no FlowFlex mention)
- Custom login page background image

#### Sub-module: Locale & Regionalisation
**Features:**
- Workspace timezone (all timestamps displayed in this tz)
- Date format preference (DD/MM/YYYY vs MM/DD/YYYY)
- Primary currency (for invoices, budgets, financial reports)
- Language selection (UI language — en, nl, de, fr, es supported)
- Number format (1,000.00 vs 1.000,00)
- First day of week (Monday vs Sunday)

#### Sub-module: Backup & Data Management
**Features:**
- Scheduled workspace data exports (CSV + JSON per module)
- On-demand full export ("Download everything")
- Data retention policies per module (auto-delete old records after N years)
- Workspace deletion with 30-day recovery window
- GDPR data erasure request (deletes all PII across all modules)

---

---

# DOMAIN 1 — HR & People Management

**Filament Panel:** `hr`
**Domain Colour:** Violet `#7C3AED`
**Domain Icon:** `users`

The HR domain is the people layer of FlowFlex. It manages every stage of the employee lifecycle — from the first job posting to the final offboarding — and serves as the source of truth for every other domain that needs to know who works at the company.

---

### Module: Employee Profiles & Directory

The central record for every person in the organisation. All other HR modules reference and extend this record. When a candidate is hired in Recruitment, a profile is automatically created here.

**Who uses it:** HR team, managers, all employees (self-service view)
**Filament Panel:** `hr`
**Events fired:**
- `EmployeeProfileCreated`
- `EmployeeProfileUpdated`
- `EmployeeDepartmentChanged`
- `EmployeeRoleChanged`

**Features:**
- Personal information (name, DOB, national ID, contact details)
- Employment details (start date, employment type, job title, department, location)
- Org chart position (reports to, direct reports)
- Contract storage (upload and version contracts)
- Custom fields (workspace-defined fields per company)
- Emergency contact details
- Profile photo
- Employee number (auto-generated or manual)
- Status (active / on leave / terminated)
- Self-service portal (employees update their own contact info, photo, emergency contacts)

---

### Module: Onboarding

Structured, templated journeys for every new hire. The goal is a consistent, professional day-one experience without HR manually coordinating every step.

**Who uses it:** HR team, hiring managers, new employees
**Filament Panel:** `hr`
**Depends on:** Employee Profiles
**Events fired:**
- `OnboardingStarted`
- `OnboardingTaskCompleted`
- `OnboardingCompleted`
**Events consumed:**
- `CandidateHired` (from Recruitment) → auto-starts onboarding flow

#### Sub-module: Pre-boarding Portal
The new hire experience before their first day. They receive an invite link to a branded portal where they can complete setup tasks without needing a full platform account yet.

**Features:**
- Branded welcome page (company name, their manager's name, start date)
- Document collection (upload ID, right-to-work documents)
- Contract e-signature (pull from approval module)
- Personal details form (so HR doesn't have to chase this on day one)
- "What to expect on day one" content block
- Access to company handbook (link to Knowledge Base if active)

#### Sub-module: Onboarding Templates
**Features:**
- Template builder (define a sequence of tasks by role/department)
- Task types: document upload, form fill, training course (links to LMS), read and acknowledge, external link
- Default assignees per task (HR, IT, hiring manager)
- Due dates relative to start date (e.g. "Day 1", "Week 1", "Day 30")
- Template cloning and versioning
- Multiple templates (one per job family: Engineering, Sales, Operations, etc.)

#### Sub-module: Onboarding Task Management
**Features:**
- Task checklist view for HR team (all active onboardings in one view)
- New hire's personal task list (what they need to do)
- Hiring manager task list (what the manager needs to do for their new hire)
- Task completion notifications (to HR when new hire completes a task)
- Progress bar per new hire (% complete)
- Overdue task alerts

#### Sub-module: 30/60/90-Day Check-ins
**Features:**
- Automated check-in form sent to new hire and manager at 30, 60, 90 days
- Configurable questions per check-in
- Manager response and review
- Flag for HR attention if check-in scores below threshold
- Check-in history stored on employee profile

---

### Module: Offboarding

Controlled, consistent exit process that protects the company and respects the departing employee.

**Who uses it:** HR team, IT team, managers
**Filament Panel:** `hr`
**Depends on:** Employee Profiles
**Events fired:**
- `OffboardingStarted`
- `OffboardingCompleted` → consumed by IT (revoke access), Payroll (final run), Asset Management (recall assets)

**Features:**
- Offboarding trigger (resignation, termination, redundancy, retirement — each has different checklist templates)
- Last day configuration
- Exit interview form (built-in or link to external)
- Knowledge handover tasks (document critical knowledge, reassign responsibilities)
- Asset return checklist (linked to asset register if Operations module active)
- Final payroll trigger (sends event to Payroll module)
- Access revocation checklist (sends event to IT module)
- Reference letter generation template
- Employment end confirmation letter

---

### Module: Leave & Absence Management

End-to-end leave request, approval, and tracking. Policy rules are fully configurable per company, per leave type, per employee category.

**Who uses it:** All employees, managers, HR team
**Filament Panel:** `hr`
**Depends on:** Employee Profiles, Scheduling (if active)
**Events fired:**
- `LeaveRequested`
- `LeaveApproved` → consumed by Payroll, Scheduling
- `LeaveRejected`
- `LeaveBalanceLow` → notification to employee

#### Sub-module: Leave Types & Policies
**Features:**
- Leave type configuration (Annual, Sick, Maternity, Paternity, TOIL, Unpaid, Compassionate, Study)
- Accrual rules (monthly, per pay period, anniversary-based)
- Carry-over rules (how much unused leave can roll into next year)
- Maximum balance cap
- Negative balance allowance (can employee go into leave debt?)
- Minimum notice period per leave type
- Probation period restrictions (no annual leave in first 3 months)
- Multi-country leave law presets (UK, Netherlands, Germany, France, US)

#### Sub-module: Leave Requests & Approval
**Features:**
- Employee submits leave request (type, dates, notes)
- Manager receives notification and approves or rejects with reason
- Multi-level approval (for long leave or above a threshold)
- Team calendar overlap check (warns if too many people off simultaneously)
- Blackout periods (HR blocks certain dates — no leave during Q4 peak)
- Cancellation flow (employee can cancel if approved but not yet taken)

#### Sub-module: Leave Balances & Calendar
**Features:**
- Employee self-service balance view (days taken, days remaining, accrued)
- Manager team leave calendar (who is off when)
- Public holiday calendar (per country/region, auto-populated)
- Year-end balance reporting
- Leave report export (by employee, department, leave type)

---

### Module: Payroll

Full payroll calculation and processing engine. Pulls from Time Tracking, Leave, and Expenses automatically — no manual reconciliation.

**Who uses it:** Finance/HR team, employees (payslip view)
**Filament Panel:** `hr` (payroll runs), `finance` (payroll costs view)
**Depends on:** Employee Profiles, Time Tracking (if active), Leave (if active), Expenses (if active)
**Events fired:**
- `PayRunCreated`
- `PayRunApproved`
- `PayRunProcessed` → employees notified, payslips generated
- `PayslipGenerated`

#### Sub-module: Payroll Configuration
**Features:**
- Pay frequency (weekly, bi-weekly, monthly, 4-weekly)
- Pay date configuration
- Tax year settings (per country)
- Pay elements setup (base salary, overtime rate, bonus types, deduction types)
- Employer NI / pension contribution settings (UK)
- Multiple payroll entities (if company has multiple legal entities)

#### Sub-module: Pay Run Processing
**Features:**
- Pay run creation (system pulls all eligible employees and pre-populates)
- Auto-pull from approved time entries (adds hours × rate)
- Auto-pull from approved expenses (adds reimbursements)
- Auto-pull from leave (deducts unpaid leave)
- Manual adjustments (one-off bonus, correction, deduction)
- Tax calculation engine (configurable per country — UK PAYE, Dutch wage tax, etc.)
- Employer cost calculation (NI, pension contributions)
- Pay run review and approval workflow
- BACS file export (UK bank payments)
- ACH file export (US bank payments)
- SEPA file export (EU bank payments)

#### Sub-module: Payslips & Reporting
**Features:**
- PDF payslip generation (branded, compliant layout)
- Automatic email to employee on payslip generation
- Employee payslip history portal (self-service download)
- Payroll summary report (total payroll cost, by department, by pay element)
- Year-to-date reports per employee
- RTI / FPS submission (UK HMRC real-time information)
- P60 / P45 generation (UK)

#### Sub-module: Contractor & Hourly Worker Payroll
**Features:**
- Contractor payment runs (separate from employee payroll)
- Hourly worker support (clocked hours from Scheduling module flow in)
- IR35 flag on contractor records (UK)
- Self-billing invoice generation for contractors

---

### Module: Performance & Reviews

Structured performance management. OKRs, review cycles, 360 feedback, and development planning — all in one place.

**Who uses it:** All employees, managers, HR team
**Filament Panel:** `hr`
**Depends on:** Employee Profiles

**Features:**
- Goal setting framework (OKR: Objective + Key Results, or simple KPI targets)
- Goal hierarchy (company goals → department goals → individual goals)
- Goal check-in updates (weekly / monthly progress updates)
- Review cycle builder (define: who reviews whom, what form, what cadence)
- Self-assessment form (employee rates themselves before manager review)
- Manager review form (structured rating + free text)
- 360-degree feedback (peer nominations, anonymous optional)
- Calibration session workspace (managers align ratings across team before sharing)
- Development plan (actions, training recommendations, target roles)
- Review history per employee (all past reviews stored on profile)

---

### Module: Recruitment & ATS

Full applicant tracking system. From job requisition to signed offer letter, with automatic hand-off to onboarding on hire.

**Who uses it:** HR team, hiring managers, recruiters
**Filament Panel:** `hr`
**Events fired:**
- `JobPostingPublished`
- `ApplicationReceived`
- `CandidateAdvanced` (moved to next pipeline stage)
- `OfferMade`
- `CandidateHired` → consumed by Employee Profiles (create record), Onboarding (start flow)
- `CandidateRejected`

#### Sub-module: Job Requisitions & Postings
**Features:**
- Job requisition form (department, role, salary band, start date, justification)
- Approval workflow for new headcount (manager → finance → HR director)
- Job posting builder (title, description, requirements, salary range optional)
- Careers page embed (iframe snippet or hosted `/careers` subdomain)
- Multi-channel job publishing (Indeed, LinkedIn, Glassdoor via API integrations)
- Internal job board (post internally first, before external)

#### Sub-module: Application Pipeline
**Features:**
- Kanban pipeline per job (custom stages per role: Applied, Screening, Interview 1, Interview 2, Offer, Hired)
- Bulk actions (move multiple candidates to next stage, reject with template email)
- Application source tracking (where did the candidate come from?)
- Resume/CV storage and parsing (auto-fill candidate details from CV upload)
- Candidate profile (full history, all applications to the company)
- Duplicate detection (same candidate applying to multiple roles)
- GDPR-compliant candidate data retention and deletion

#### Sub-module: Interviews & Scorecards
**Features:**
- Interview scheduling (suggest times from interviewer's calendar, send invite)
- Scorecard builder (define rating criteria per role, per interview stage)
- Interviewer scorecard submission
- Panel interview coordination (multiple interviewers, aggregate scores)
- Interview feedback compilation view (hiring manager sees all scores)
- Candidate interview confirmation and reminder emails

#### Sub-module: Offers & Closing
**Features:**
- Offer letter generator (template with merge fields: name, role, salary, start date)
- Offer approval workflow (HR director sign-off on comp above threshold)
- E-signature on offer letter (candidate signs digitally)
- Offer status tracking (sent, viewed, signed, declined)
- Counter-offer handling notes
- Rejection templates (multiple templates for different rejection reasons)

#### Sub-module: Referral Programme
**Features:**
- Employee referral submission form
- Referral tracking (who referred whom, for which role)
- Referral bonus configuration (amount, trigger: interview, hire, 3-month anniversary)
- Referral dashboard (top referrers, referral conversion rate)
- Automated payout trigger to Payroll module on bonus eligibility

---

### Module: Scheduling & Shifts

Shift planning for hourly workers, retail, hospitality, and any team with rotas. Connected directly to payroll and time tracking.

**Who uses it:** Shift managers, operations managers, employees
**Filament Panel:** `hr` or `operations` (configurable)
**Depends on:** Employee Profiles
**Events fired:**
- `ShiftPublished`
- `ShiftSwapRequested`
- `ShiftSwapApproved`
- `ClockIn`
- `ClockOut` → consumed by Time Tracking (creates time entry)

**Features:**
- Drag-and-drop shift builder (week/fortnight view)
- Employee availability capture (employees submit their availability windows)
- Shift templates (recurring weekly rota saved as template)
- Skill-based scheduling (only show employees qualified for a shift type)
- Minimum rest time enforcement (e.g. 11 hours between shifts)
- Maximum hours per week warning (overtime threshold alerts)
- Shift swap requests (employee requests swap → manager approves)
- Rota publishing (employees notified when their schedule is published)
- Open shift posting (post an uncovered shift for eligible employees to claim)
- Clock-in / clock-out (mobile app with GPS location verification option)
- POS integration for clock-in (if Operations/POS module active)
- Attendance reporting (who was scheduled vs who actually showed up)

---

### Module: Benefits & Perks

Benefits catalogue that employees can browse and enrol in. Eligibility rules keep complexity manageable. Costs flow automatically to payroll deductions.

**Who uses it:** HR team, all employees
**Filament Panel:** `hr`
**Depends on:** Employee Profiles, Payroll (for deductions)

**Features:**
- Benefits catalogue builder (define each benefit: name, description, cost, provider)
- Benefit types: health insurance, dental, vision, pension/401k, life insurance, cycle-to-work, gym membership, childcare vouchers, private travel insurance
- Eligibility rules per benefit (by role, employment type, tenure, location)
- Open enrolment periods (annual window when employees can change selections)
- Life event triggers (marriage, new child — allows mid-year changes)
- Employee enrolment portal (browse available benefits, select, see cost impact on pay)
- Employer cost dashboard (total benefits spend by type and headcount)
- Pension contribution tracking (employee vs employer contribution, per country rules)
- Provider contact and documentation storage per benefit

---

### Module: Employee Feedback & Engagement

Ongoing pulse on how employees are feeling. Goes beyond annual surveys — captures real-time sentiment and flags burnout risk before it becomes an attrition event.

**Who uses it:** HR team, leadership, managers
**Filament Panel:** `hr`
**Depends on:** Employee Profiles

**Features:**
- Pulse survey builder (create short 3–5 question surveys)
- Scheduled pulse delivery (weekly, fortnightly, monthly — random timing option to prevent gaming)
- eNPS question (built-in: "How likely are you to recommend working here?")
- eNPS trend tracking over time (by department, by tenure cohort)
- Anonymous feedback toggle (employees answer without their name attached)
- Sentiment analysis dashboard (aggregate scores, trend lines, department breakdowns)
- Burnout signal detection algorithm:
  - Overtime frequency rising
  - Leave not being taken
  - Increasing after-hours activity
  - Declining pulse survey scores
  - Increased sick leave frequency
- Manager alert when direct report's burnout score crosses threshold
- Recognition and kudos system (peer-to-peer, manager-to-employee public recognition)
- Recognition feed (public wall of kudos visible to all)
- Birthday and work anniversary alerts for managers

---

### Module: HR Compliance & Certifications

Tracks all mandatory training, certification requirements, policy acknowledgements, and regulatory deadlines. Feeds into audit readiness.

**Who uses it:** HR team, compliance managers, all employees (their own records)
**Filament Panel:** `hr`
**Depends on:** Employee Profiles, LMS (if active for training delivery), Legal/Policy Management (if active)
**Events fired:**
- `CertificationExpired`
- `TrainingOverdue`
- `PolicyNotAcknowledged`

**Features:**
- Mandatory training tracker (assign required trainings per role, track completion)
- Certification register per employee (type, issuing body, issue date, expiry date)
- Expiry alerts (60 days, 30 days, 7 days, expired)
- Renewal reminders sent to employee and their manager
- Right-to-work check storage (document type, expiry, review date)
- Work permit tracking (expiry alerts for foreign nationals)
- Policy acknowledgement sign-off (employees confirm they've read the policy)
- Compliance completeness dashboard (what % of the workforce is compliant on each requirement)
- Audit report export (all compliance data for a given employee or date range)

---

---

# DOMAIN 2 — Projects & Work Management

**Filament Panel:** `projects`
**Domain Colour:** Indigo `#4F46E5`
**Domain Icon:** `rectangle-stack`

The work management domain. Everything teams do day-to-day — tasks, planning, documents, collaboration — lives here. It's the Jira + Notion + Google Drive replacement that doesn't require a certification to understand.

---

### Module: Task Management

The foundation of all project work. Tasks can exist independently or within projects. Multiple views for different working styles.

**Who uses it:** All employees
**Filament Panel:** `projects`
**Events fired:**
- `TaskCreated`
- `TaskCompleted` → consumed by Project Planning (updates project progress), Finance (triggers invoice if milestone-linked)
- `TaskOverdue`
- `TaskAssigned`

#### Sub-module: Task Views
**Features:**
- Kanban board (drag cards between custom status columns)
- List view (dense, sortable, filterable)
- Calendar view (tasks plotted on a calendar by due date)
- Timeline view (lightweight Gantt for individual task scheduling)
- My Work view (personal inbox — all tasks assigned to me, across all projects)
- Grouping options (by assignee, by priority, by label, by project)

#### Sub-module: Task Properties
**Features:**
- Title, description (rich text)
- Assignee (single or multiple)
- Due date and start date
- Priority (P1 Critical / P2 High / P3 Medium / P4 Low — or custom labels)
- Custom status columns (per board/project)
- Labels and tags (free-form, colour-coded)
- Estimated hours
- Subtasks (unlimited nesting depth)
- Dependencies (this task blocks / is blocked by another task)
- Attachments (files, images)
- Time logged (links to Time Tracking module if active)
- Linked records (link a task to a CRM deal, an employee record, an invoice)

#### Sub-module: Task Automations
Rules that trigger automatically based on task events.

**Features:**
- Trigger options: task created, task completed, status changed, due date reached, assignee changed
- Action options: notify a user, change a field, create a subtask, assign to someone, move to a status, create an invoice (if Finance active)
- Automation log (history of all triggered automations)
- Enable/disable automations per project

#### Sub-module: Recurring Tasks
**Features:**
- Recurrence options: daily, weekly, bi-weekly, monthly, quarterly, annually, custom
- Recurrence end: never, after N occurrences, on a date
- New instance created automatically when previous completes (or on schedule)
- Recurrence pausing

---

### Module: Project Planning

For managing larger bodies of work with milestones, dependencies, and resource allocation. The Gantt layer on top of tasks.

**Who uses it:** Project managers, team leads
**Filament Panel:** `projects`
**Depends on:** Task Management
**Events fired:**
- `ProjectCreated`
- `ProjectMilestoneReached` → consumed by Finance (trigger milestone invoice)
- `ProjectCompleted`
- `ProjectAtRisk` (schedule slippage detected)

**Features:**
- Project creation wizard (name, description, client (CRM link), start date, end date, budget (Finance link), team members)
- Gantt chart view (tasks and milestones on a timeline)
- Milestone markers (visual on Gantt, trigger invoice events)
- Task dependencies (Finish-to-Start, Start-to-Start, Finish-to-Finish, Start-to-Finish)
- Critical path detection (highlights tasks that determine project end date)
- Baseline recording (snapshot the original plan, compare against actuals)
- Project health indicator (on track / at risk / delayed — auto-calculated from schedule)
- Project templates (save a project structure as a reusable template)
- Portfolio view (all projects, their status, RAG rating, budget vs actual)
- Project archive and search

---

### Module: Time Tracking

One-click or manual time logging. Feeds automatically to payroll and client billing without manual reconciliation.

**Who uses it:** All employees, contractors, managers
**Filament Panel:** `projects`
**Depends on:** Task Management (optional — time can be logged independently)
**Events fired:**
- `TimeEntryCreated`
- `TimeEntryApproved` → consumed by Payroll (add to pay run), Finance/Client Billing (mark billable)
- `TimeEntryRejected`

**Features:**
- One-click timer (start/stop, shows elapsed time)
- Manual time entry (date, hours, minutes, description)
- Tag to: project, task, client (from CRM), internal category
- Billable vs non-billable flag
- Weekly timesheet view (grid: Mon-Sun × projects)
- Time entry bulk import (CSV for legacy data)
- Timesheet submission and approval flow (employee submits week → manager approves)
- Overtime calculation (based on contracted hours vs logged)
- Time reports: by employee, by project, by client, by date range
- Rounding rules (round to nearest 15min, 30min, or log exactly)

---

### Module: Document Management

Centralised file storage for the entire organisation. Organised, versioned, permissioned, searchable.

**Who uses it:** All employees
**Filament Panel:** `projects`
**Events fired:**
- `DocumentUploaded`
- `DocumentVersioned`
- `DocumentShared`

#### Sub-module: File Storage & Organisation
**Features:**
- Folder structure (nested, unlimited depth)
- Context folders (project folders, client folders, department folders auto-created)
- File upload (drag and drop, multi-file, up to 5GB per file on Enterprise)
- Supported types: PDF, Word, Excel, PowerPoint, images, video, audio, ZIP, CAD
- Storage backend: AWS S3 or Cloudflare R2 (configurable)
- File tagging and metadata
- Duplicate detection on upload
- File move and copy

#### Sub-module: Version History
**Features:**
- Every upload of the same filename creates a new version (not overwrite)
- Version list with uploader name, timestamp, file size
- Restore any previous version
- Compare versions (for text documents where diffing is possible)
- Version retention policy (keep last N versions, auto-delete older)

#### Sub-module: Permissions
**Features:**
- Folder and file permissions (view, edit, download, delete — per user or role)
- Public sharing links (time-limited, password-optional, download-only option)
- Share with external people (email invite to view without a FlowFlex account)
- Workspace-wide permissions (some folders accessible to all employees)

#### Sub-module: Search & Preview
**Features:**
- Full-text search across all file names and document content
- OCR on scanned PDFs (makes scanned documents searchable)
- In-browser preview (PDF, images, video, Office files via OnlyOffice or Google Viewer)
- Recent files list
- Starred/pinned files

#### Sub-module: Cloud Sync
**Features:**
- Google Drive folder sync (two-way sync for selected Drive folders)
- Microsoft OneDrive sync (two-way sync)
- Files modified in Google/OneDrive appear in FlowFlex and vice versa
- Sync conflict resolution

---

### Module: Document Approvals & E-Sign

Formal approval workflows for any document. Built-in e-signature so you never need DocuSign.

**Who uses it:** All employees (requesters), managers, legal, HR
**Filament Panel:** `projects`
**Depends on:** Document Management
**Events fired:**
- `ApprovalRequested`
- `ApprovalCompleted` → triggers relevant downstream action
- `DocumentSigned`
- `ApprovalRejected`

**Features:**
- Approval workflow builder (drag-and-drop step editor)
- Sequential approvals (A must approve before B sees it)
- Parallel approvals (A and B can approve simultaneously, both required)
- Optional approvers (one of these people must approve)
- Role-based approvers (e.g. "someone with Finance Manager role")
- Rejection flow (rejected document goes back to originator with reason)
- Revision cycle (submitter edits and resubmits, approval chain restarts or continues from rejection point)
- Deadline per step (auto-escalate if not actioned within N hours)
- E-signature fields (drag-and-drop signature boxes onto PDF)
- Audit trail (every view, every action, timestamped, with IP)
- Signed document stored automatically in Document Management

---

### Module: Knowledge Base & Wiki

The internal brain of the company. SOPs, runbooks, handbooks, and how-tos — searchable, versioned, and always current.

**Who uses it:** All employees
**Filament Panel:** `projects`

**Features:**
- Rich block editor (headings, paragraphs, bullet lists, numbered lists, tables, code blocks, callouts, image embeds, file embeds, dividers)
- Nested pages (unlimited depth — pages inside pages)
- Category and tag system
- Full-text search across all articles
- Article templates (for SOP, runbook, meeting notes, decision record)
- Change history per article (who changed what, when — revert to any version)
- Contributor tracking (who wrote/edited each article)
- Comments and inline suggestions on articles
- Article status (draft / published / archived / needs review)
- Public articles (embed in customer-facing portal or external knowledge base)
- Article feedback ("Was this helpful? Yes / No")
- Reading time estimate
- Related articles suggestions
- Article ownership and review schedule ("this article should be reviewed every 6 months")

---

### Module: Team Collaboration

Context-aware discussion attached to the work itself. Reduces Slack noise, keeps decisions close to where they were made.

**Who uses it:** All employees
**Filament Panel:** `projects`
**Depends on:** Task Management

**Features:**
- Comment threads on tasks, projects, documents, and any record across the platform
- @mention users (triggers notification)
- @mention teams (notifies all team members)
- File attachments in comments
- Emoji reactions to comments
- Threaded replies (reply to a specific comment)
- Edit and delete own comments (with edit history visible)
- Activity feed per project (all actions, comments, status changes in chronological order)
- Project announcements (pinned update at the top of a project, visible to all members)
- Watching (subscribe to updates on any record without being assigned)

---

### Module: Resource & Capacity Planning

Visibility into who has capacity and who is overloaded, before it becomes a problem.

**Who uses it:** Project managers, team leads, HR
**Filament Panel:** `projects`
**Depends on:** Employee Profiles, Task Management

**Features:**
- Workload heatmap (colour-coded by utilisation % — green/amber/red per person per week)
- Individual capacity calendar (see all allocations for one person across all projects)
- Team capacity view (how many hours available vs allocated per team member this week)
- Role-based demand view (how many hours of "Backend Developer" work do we have vs how many Backend Devs?)
- Time-off overlay (show leave from HR module on capacity calendar)
- Project demand forecasting (upcoming milestones, predict when demand peaks)
- Rebalancing suggestions (if person is over-allocated, surface under-allocated colleagues)

---

### Module: Agile & Sprint Management

For software and product teams. Full scrum/kanban support, backlog grooming, and sprint ceremonies.

**Who uses it:** Engineering and product teams
**Filament Panel:** `projects`
**Depends on:** Task Management

**Features:**
- Backlog (ordered list of all uncompleted work, drag to reprioritise)
- Sprint creation (define sprint duration, start/end date, sprint goal)
- Sprint planning (drag backlog items into sprint)
- Story point estimation (Fibonacci by default, configurable)
- Velocity calculation (average story points completed per sprint, last N sprints)
- Sprint burndown chart (remaining points vs ideal burn line)
- Sprint retrospective notes (what went well, what didn't, action items)
- Sprint review checklist
- Release management (group sprints into releases, track release readiness)
- Bug tracking workflow (bug status: reported → triaged → in progress → resolved → verified)
- Definition of Done (configurable checklist per team)

---

---

# DOMAIN 3 — Finance & Accounting

**Filament Panel:** `finance`
**Domain Colour:** Emerald `#059669`
**Domain Icon:** `banknotes`

The financial nerve system of the business. From invoices to payroll costs, from bank reconciliation to VAT returns. Every number in the platform that involves money routes through here.

---

### Module: Invoicing

Create, send, and track invoices. Auto-generate from approved time entries or project milestones. Handles all billing complexity without needing a separate invoicing tool.

**Who uses it:** Finance team, account managers
**Filament Panel:** `finance`
**Events fired:**
- `InvoiceCreated`
- `InvoiceSent`
- `InvoicePaid` → consumed by Bank Reconciliation (auto-match)
- `InvoiceOverdue` → consumed by CRM (create follow-up task), Notifications
- `CreditNoteIssued`

**Features:**
- Invoice builder (line items, quantities, rates, tax codes, discounts)
- Auto-generate from approved time entries (one click: all unbilled time → invoice)
- Auto-generate from project milestones (milestone hit → invoice triggered)
- Recurring invoice setup (weekly, monthly, quarterly — auto-sent)
- Invoice numbering (configurable format: INV-2025-0001)
- Multi-currency (set invoice currency per client)
- Tax codes per line item (VAT, GST, sales tax)
- Discount (percentage or fixed amount, per line or invoice total)
- Invoice PDF generation (branded, compliant layout)
- Email delivery (to client with PDF attached, tracked open)
- Payment link embed in invoice email
- Partial payments (record multiple payments against one invoice)
- Late payment fee automation (apply after N days overdue)
- Credit notes (reduce or cancel an invoice)
- Invoice status workflow: Draft → Sent → Partially Paid → Paid → Overdue → Written Off

---

### Module: Expense Management

Employee expense submission, approval, and reimbursement — fully connected to payroll.

**Who uses it:** All employees (submit), managers (approve), finance team (process)
**Filament Panel:** `finance`
**Depends on:** Employee Profiles, Payroll (for reimbursement)
**Events fired:**
- `ExpenseSubmitted`
- `ExpenseApproved` → consumed by Payroll (add reimbursement to next pay run)
- `ExpenseRejected`

**Features:**
- Receipt upload (mobile camera or file upload)
- OCR receipt scanning (auto-fill date, vendor, amount from photo)
- Expense categories (configurable: Travel, Accommodation, Meals, Equipment, etc.)
- Mileage tracking (distance + rate per mile/km = expense amount)
- Per diem rates (daily allowance by destination country)
- Expense report grouping (group multiple expenses into one report for submission)
- Approval workflow (line manager approves, finance reviews above threshold)
- Multi-currency expenses (employee paid in foreign currency, converted to base)
- Policy enforcement (flag expenses over category limits before approval)
- Reimbursement via payroll (approved expenses added to next pay run automatically)
- Finance export (approved expenses to accounting journal)

---

### Module: Accounts Payable & Receivable

Full AP and AR management. Track what you owe and what you're owed.

**Who uses it:** Finance team
**Filament Panel:** `finance`
**Events fired:**
- `BillReceived`
- `BillPaid`
- `PaymentRunCompleted`

**Features:**
- Supplier bill entry (upload bill, enter details, code to account and department)
- Bill approval workflow (approve bills above a threshold)
- Payment run (batch-approve bills for payment, export payment file)
- BACS/SEPA/ACH payment file export
- Supplier statement reconciliation
- Aged creditor report (what we owe, by how many days)
- Aged debtor report (what's owed to us, by how many days)
- Purchase orders (raise PO before bill received — 3-way match: PO → receipt → bill)
- Automated payment reminder sequences (Day 0, Day 7, Day 14, Day 30 overdue)
- Dispute management (mark an invoice as disputed, log reason, track resolution)

---

### Module: Bank Reconciliation

Connect bank accounts and match transactions automatically to invoices and bills.

**Who uses it:** Finance team, bookkeepers
**Filament Panel:** `finance`
**Depends on:** Invoicing, Accounts Payable

**Features:**
- Open Banking connection (Plaid for US/Canada, TrueLayer for UK/EU)
- Manual bank statement import (CSV / OFX / QIF)
- Auto-matching rules (match bank transaction to invoice by amount + reference)
- Confidence scoring on auto-matches (100% = exact match, lower = review needed)
- Unmatched transaction queue (manual classification: categorise, split, or create new bill/invoice)
- Multi-account support (current accounts, savings, credit cards, PayPal)
- Reconciliation history (all matched pairs, who matched, when)
- Bank balance vs book balance comparison
- Monthly reconciliation sign-off (finance manager confirms period is reconciled)

---

### Module: Budgeting & Forecasting

Set budgets, track actuals, forecast the year ahead. No more end-of-month surprises.

**Who uses it:** Finance team, department heads, leadership
**Filament Panel:** `finance`

**Features:**
- Budget creation (by department, by cost centre, by project)
- Budget period (monthly breakdown of annual budget)
- Actuals pull-through (actual spend from AP and payroll feeds in automatically)
- Budget vs actual variance (£ and % variance, per month, per category)
- Over-budget alerts (email + notification at 80% and 100% of budget)
- Rolling forecast (actuals replace budget for past months, forecast retained for future)
- Scenario planning (best case / worst case / base case — run multiple forecasts simultaneously)
- Budget approval workflow (department heads submit budget, finance director approves)
- Budget history (previous years' budgets for comparison)
- Commitment tracking (POs raised but not yet billed count against budget)

---

### Module: Financial Reporting

Management accounts, statutory reporting, and custom reports — auto-generated from the data already in the system.

**Who uses it:** Finance team, CEO, board, auditors
**Filament Panel:** `finance`

**Features:**
- Profit & Loss statement (configurable date range, by month or period)
- Balance sheet (as at any date)
- Cash flow statement (direct or indirect method)
- Trial balance
- Custom report builder (drag fields, group, filter, pivot)
- Scheduled report delivery (auto-email as PDF or Excel on first of month)
- Consolidation (if multiple entities — consolidate into group accounts)
- Prior period comparison (current month vs same month last year)
- Department P&L (break down income and costs by department)
- Project P&L (revenue and costs per project)
- Export: Excel, PDF, Google Sheets sync

---

### Module: Client Billing & Retainers

For professional services firms. Convert time and expenses into client invoices, manage retainer balances, and give clients a payment portal.

**Who uses it:** Account managers, finance team
**Filament Panel:** `finance`
**Depends on:** Invoicing, Time Tracking, CRM (client records)

**Features:**
- Time-to-invoice conversion (select a client, date range → all unbilled time becomes invoice line items)
- Expense-to-invoice conversion (add approved client expenses to invoice)
- Retainer setup (client pays monthly cap in advance, hours drawn down against it)
- Retainer balance tracking (hours consumed vs hours remaining in period)
- Rollover rules (unused hours carry over to next period — or don't)
- Overage billing (hours above retainer cap billed at standard or premium rate)
- Retainer burn rate alert (client used 80% of retainer, notify account manager)
- Client payment portal (branded, client logs in to see all invoices and pay online)
- Unbilled time alert (flag projects where time hasn't been billed in over 30 days)
- Client profitability report (revenue vs payroll/time cost per client)

---

### Module: Tax & VAT Compliance

Tax calculation, return preparation, and submission. Jurisdiction-aware.

**Who uses it:** Finance team, accountants
**Filament Panel:** `finance`

**Features:**
- Tax code configuration (set up VAT/GST/sales tax rates per jurisdiction)
- Tax on invoices (auto-apply based on client location and product/service type)
- Tax on bills (code input VAT for reclaim)
- VAT return preparation (summary of output and input VAT for a period)
- Making Tax Digital (MTD) compatible submission (UK HMRC API)
- EC Sales list (EU cross-border VAT reporting)
- US sales tax nexus tracking (multi-state sales tax complexity)
- Reverse charge handling (B2B cross-border EU services)
- Tax liability report (how much tax is owed per period)
- Audit trail (every transaction with tax applied, reason, rate)

---

### Module: Fixed Asset & Depreciation

Track capital assets on the balance sheet. Auto-calculate depreciation so finance reporting is always accurate.

**Who uses it:** Finance team
**Filament Panel:** `finance`
**Depends on:** Operations/Asset Management (for physical asset data)

**Features:**
- Asset register (financial view: cost, acquisition date, useful life, residual value)
- Multiple depreciation methods: straight-line, reducing balance, sum of digits, units of production
- Monthly depreciation journal auto-calculation
- Net book value (NBV) reporting at any date
- Disposal recording (sale or write-off — journal entry auto-generated)
- Impairment recording
- Asset revaluation
- Depreciation schedule report (per asset, all future charges)
- Capital expenditure tracking (planned vs actual capex)
- Integration with Operations Asset Management module (physical status linked to financial record)

---

### Module: Subscription & MRR Tracking

For SaaS and subscription businesses running on FlowFlex. Revenue metrics, churn, and recognition.

**Who uses it:** Finance team, founders, investors
**Filament Panel:** `finance`
**Depends on:** Invoicing, CRM

**Features:**
- MRR dashboard (total MRR, new MRR, expansion MRR, churned MRR, net MRR)
- ARR (annualised recurring revenue)
- Churn rate (customer and revenue churn, monthly and annual)
- Cohort analysis (MRR by signup cohort over time)
- Customer lifetime value (LTV) calculation
- LTV:CAC ratio tracking
- Revenue recognition (spread recognition over subscription period — ASC 606 / IFRS 15)
- Deferred revenue liability tracking (cash received but not yet earned)
- Dunning management (failed payment retry rules and sequences)
- Subscription health dashboard (at-risk accounts by payment history and engagement)

---

*(Domains 4–12 follow the same depth of detail — CRM & Sales, Marketing & Content, Operations & Field Service, Analytics & BI, IT & Security, Legal & Compliance, E-commerce, Communications, and Learning & Development. Each is structured identically: domain overview, module list, who uses it, events fired/consumed, full feature lists, and sub-sub-modules where scope demands it.)*

---

---

# Cross-Domain Event Reference

This table documents every cross-domain event and which modules react to it. When both the source and consuming modules are active for a tenant, these flows happen automatically.

| Event | Source Module | Consuming Modules | What Happens |
|---|---|---|---|
| `EmployeeHired` | Recruitment | Onboarding, Payroll, Scheduling, LMS | Starts onboarding, adds to payroll, adds to rota, assigns induction course |
| `OnboardingCompleted` | Onboarding | HR Compliance, LMS | Marks induction complete, triggers first compliance cert assignments |
| `EmployeeOffboarded` | Offboarding | IT/Access, Payroll, Asset Management | Revokes all access, runs final payroll, recalls assets |
| `TimeEntryApproved` | Time Tracking | Payroll, Finance/Client Billing | Adds to pay run, marks hours as billable |
| `LeaveApproved` | Leave Management | Payroll, Scheduling | Deducts from pay if unpaid, removes from rota |
| `ProjectMilestoneReached` | Project Planning | Finance/Invoicing, CRM | Triggers milestone invoice, updates deal status |
| `InvoiceOverdue` | Invoicing | CRM, Notifications | Creates follow-up task in CRM, alerts account manager |
| `InvoicePaid` | Invoicing | Bank Reconciliation, MRR Tracking | Auto-matches to bank transaction, updates MRR |
| `StockBelowReorderPoint` | Inventory | Purchasing | Creates draft purchase order |
| `PurchaseOrderApproved` | Purchasing | Finance/AP | Creates bill record, updates committed spend |
| `FieldJobCompleted` | Field Service | Finance/Invoicing, Inventory, CRM | Creates invoice, deducts parts used, closes support ticket |
| `TicketResolved` | Customer Support | Marketing/Email, CRM | Sends CSAT survey, updates contact timeline |
| `CandidateHired` | Recruitment ATS | Employee Profiles, Onboarding | Creates employee record, starts onboarding flow |
| `CourseCompleted` | LMS | HR Compliance, HR Performance | Fulfils certification requirement, logs development activity |
| `OrderPlaced` | E-commerce | Inventory, Finance, CRM | Deducts stock, records revenue, updates customer record |
| `CertificationExpired` | HR Compliance | LMS, Notifications | Triggers renewal course assignment, notifies employee and manager |
| `ContractExpiring` | Legal/CLM | CRM, Notifications | Creates renewal task in CRM, alerts account manager |
| `RiskFlagRaised` | Risk Register | Legal, Notifications | Notifies risk owner, creates mitigation task |
| `BurnoutSignalDetected` | Employee Feedback | HR, Notifications | Alerts HR manager and direct manager |
| `SaaSLicenceExpiring` | IT/SaaS Mgmt | Finance, Notifications | Alerts finance team, creates renewal task |

---

## Module Dependency Map

Some modules require or strongly benefit from other modules being active. This is shown here.

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
│   └── benefits from: Time Tracking (auto-fill hours), Leave (deductions), Expenses (reimbursements)
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

---

## Module Sizing Reference

Estimated build complexity for sprint planning (using the Filament modular monolith approach):

| Module | Complexity | Estimated Filament Resources | Migrations |
|---|---|---|---|
| Auth & Identity | Medium | 3 resources, 2 pages | 4 tables |
| RBAC | Medium | 2 resources | 3 tables (Spatie) |
| Module Billing | High | 1 resource, 3 pages | 4 tables |
| Notifications | Medium | 1 resource, 1 page | 2 tables |
| API Layer | Low | 1 resource | 1 table |
| Multi-tenancy | High | 2 pages | 2 tables (Spatie) |
| Employee Profiles | Medium | 1 resource, 1 page | 5 tables |
| Onboarding | High | 3 resources, 2 pages | 6 tables |
| Offboarding | Medium | 2 resources | 2 tables |
| Leave Management | High | 2 resources, 2 pages | 5 tables |
| Payroll | Very High | 4 resources, 3 pages | 10 tables |
| Performance | High | 3 resources, 2 pages | 8 tables |
| Recruitment (ATS) | Very High | 5 resources, 3 pages | 10 tables |
| Scheduling | High | 2 resources, 2 pages | 5 tables |
| Benefits | Medium | 2 resources | 4 tables |
| Feedback | Medium | 2 resources, 2 pages | 4 tables |
| HR Compliance | Medium | 2 resources, 1 page | 3 tables |
| Task Management | High | 3 resources, 4 pages | 6 tables |
| Project Planning | Very High | 2 resources, 3 pages | 5 tables |
| Time Tracking | High | 2 resources, 2 pages | 3 tables |
| Document Management | High | 2 resources, 1 page | 4 tables |
| Document Approvals | High | 2 resources, 2 pages | 5 tables |
| Knowledge Base | High | 2 resources, 1 page | 4 tables |
| Invoicing | Very High | 3 resources, 2 pages | 6 tables |
| Expense Management | High | 2 resources, 2 pages | 4 tables |
| Bank Reconciliation | Very High | 2 resources, 2 pages | 4 tables |
| CRM Contacts | Medium | 2 resources | 5 tables |
| Sales Pipeline | High | 2 resources, 2 pages | 4 tables |
| Customer Support | Very High | 3 resources, 3 pages | 8 tables |
| Inventory | Very High | 3 resources, 2 pages | 8 tables |
| Field Service | Very High | 4 resources, 3 pages | 9 tables |
| CMS | Very High | 3 resources, 2 pages | 6 tables |
| Email Marketing | Very High | 3 resources, 3 pages | 8 tables |

---

*Last updated: May 2026*
*Maintained by: Max (Founder)*
*Total platform scope: 13 domains · 99+ modules · 300+ individual features*