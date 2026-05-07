---
tags: [flowflex, marketing, features, modules, pages]
domain: Marketing Site
status: planned
last_updated: 2026-05-07
---

# Features & Modules Pages

The features section is the primary SEO surface for FlowFlex. Every module has a URL. Every domain has an overview page. These pages capture search traffic from people looking for specific tools ("HR software", "invoicing software", "project management tool").

## URL Structure

```
/features                    ← all-domains overview
/modules/{domain}            ← domain overview (e.g. /modules/hr)
/modules/{domain}/{module}   ← individual module page (e.g. /modules/hr/payroll)
```

## Features Overview Page (`/features`)

**Purpose:** Give visitors a high-level view of everything FlowFlex does. Works as a landing page for generic searches ("all-in-one business software").

**Meta:**
```
<title>Features — Everything Your Business Needs | FlowFlex</title>
<meta name="description" content="13 business domains. 99+ modules. HR, finance, projects, CRM, marketing, operations and more — one platform, one login, one data layer.">
```

**Page Structure:**

1. **Hero** — "Everything your business runs on. In one place."
2. **Domain grid** — 13 domain cards, each with colour accent, icon, name, 1-line description, 3 module names, and `Explore →`
3. **Cross-module highlight** — "Data flows between modules" — show example: employee hired in HR → auto-creates project team member in Projects → payroll record created in Finance → access provisioned in IT
4. **Platform promise** — Core Platform features (auth, RBAC, API, notifications) always included
5. **CTA** — "Not sure which modules you need? Let's talk." → demo

---

## Domain Overview Pages (`/modules/{domain}`)

One page per domain. All 13 must exist. Template structure:

**Header:**
- Domain colour accent (left border or background tint)
- Domain icon
- H1: "{Domain name} — everything you need in one place"
- 2-sentence description of the domain
- CTA: `Request Demo` + `View all {domain} modules ↓`

**Module list:**
- Card grid of all modules in this domain
- Each card: module name + 1-line description + key features list (3 bullets) + `Learn more →`

**Cross-module connections:**
- "Works with:" section — list modules from other domains that connect to this one
- Example: HR → "Works with: Projects (resource planning), Finance (payroll), IT (access provisioning)"

**CTA block:** `See {domain} in action — book a demo`

---

## Individual Module Pages (`/modules/{domain}/{module}`)

High-detail page per module. These are the primary SEO pages — they target specific tool-replacement keywords.

**Template structure:**

### Hero
- H1: "[Module Name] — [What it does, 5 words max]"
- Example: "Payroll — Pay your team, every time, on time."
- 2-3 sentence description
- Primary CTA: `Request Demo`
- Secondary CTA: `See pricing →`

### Problem it solves (2–3 sentences)
- Name the pain: "Most payroll tools are either too complex or too basic. They don't know about your employees' leave, overtime, or custom pay elements — so you're entering data twice."

### Feature list (3-column grid of feature cards)
- Each card: icon + feature name + 1-sentence description
- 6–9 features per module (not exhaustive, not shallow)

### Screenshot / UI preview section
- 1–3 screenshots of the actual UI
- Real data (curated dummy data, not Lorem Ipsum)
- Caption under each: what the user is seeing

### How it connects
- "Works seamlessly with:" — linked module cards
- Example (Payroll): Links to Employee Profiles, Leave Management, Time Tracking, Expense Management

### Comparison snippet
- Small table: FlowFlex vs. the named competitor
- Example for Payroll: FlowFlex vs. PayFit / Sage Payroll
- 5 key differences. Keep it factual.

### CTA
- "Start your free trial" or "Book a demo to see [Module Name] in action"

---

## Domain + Module Roster

### HR & People (`/modules/hr`)
Pages to create:
- `/modules/hr` (domain overview)
- `/modules/hr/employee-profiles`
- `/modules/hr/onboarding`
- `/modules/hr/leave-management`
- `/modules/hr/payroll`
- `/modules/hr/performance-reviews`
- `/modules/hr/recruitment`
- `/modules/hr/scheduling`
- `/modules/hr/benefits`
- `/modules/hr/employee-feedback`
- `/modules/hr/compliance`

SEO keywords to target (examples):
- `hr software for small business`
- `employee onboarding software`
- `leave management system`
- `payroll software uk`
- `performance review software`

---

### Projects & Work (`/modules/projects`)
Pages:
- `/modules/projects/task-management`
- `/modules/projects/project-planning`
- `/modules/projects/time-tracking`
- `/modules/projects/document-management`
- `/modules/projects/knowledge-base`
- `/modules/projects/agile-sprints`
- `/modules/projects/document-approvals`
- `/modules/projects/resource-planning`
- `/modules/projects/team-collaboration`

SEO keywords: `project management software`, `task management app`, `time tracking software`, `team collaboration tool`

---

### Finance (`/modules/finance`)
Pages:
- `/modules/finance/invoicing`
- `/modules/finance/expense-management`
- `/modules/finance/financial-reporting`
- `/modules/finance/accounts-payable-receivable`
- `/modules/finance/bank-reconciliation`
- `/modules/finance/budgeting`
- `/modules/finance/tax-vat`
- `/modules/finance/fixed-assets`
- `/modules/finance/client-billing`
- `/modules/finance/mrr-tracking`

SEO keywords: `invoicing software`, `expense management`, `accounting software small business`, `financial reporting tool`

---

### CRM & Sales (`/modules/crm`)
Pages:
- `/modules/crm/contact-management`
- `/modules/crm/sales-pipeline`
- `/modules/crm/shared-inbox`
- `/modules/crm/helpdesk`
- `/modules/crm/client-portal`
- `/modules/crm/quotes-proposals`
- `/modules/crm/customer-data-platform`
- `/modules/crm/loyalty-retention`

SEO keywords: `crm software small business`, `sales pipeline management`, `customer support software`, `helpdesk software`

---

### Marketing (`/modules/marketing`)
Pages:
- `/modules/marketing/cms-website`
- `/modules/marketing/email-marketing`
- `/modules/marketing/forms-lead-capture`
- `/modules/marketing/seo-analytics`
- `/modules/marketing/social-media`
- `/modules/marketing/events-webinars`
- `/modules/marketing/ad-campaigns`
- `/modules/marketing/affiliate-partners`

---

### Operations (`/modules/operations`)
- `/modules/operations/inventory`
- `/modules/operations/asset-management`
- `/modules/operations/purchasing`
- `/modules/operations/field-service`
- `/modules/operations/quality-control`
- `/modules/operations/hse`
- `/modules/operations/supply-chain`
- `/modules/operations/pos`
- `/modules/operations/equipment-maintenance`

---

### Analytics (`/modules/analytics`)
- `/modules/analytics/custom-dashboards`
- `/modules/analytics/report-builder`
- `/modules/analytics/kpi-tracking`
- `/modules/analytics/audit-log`
- `/modules/analytics/team-velocity`
- `/modules/analytics/data-warehouse`

---

### IT & Security (`/modules/it`)
- `/modules/it/asset-management`
- `/modules/it/helpdesk`
- `/modules/it/security-compliance`
- `/modules/it/saas-spend`
- `/modules/it/access-audit`
- `/modules/it/uptime-monitoring`

---

### Legal (`/modules/legal`)
- `/modules/legal/contract-management`
- `/modules/legal/policy-management`
- `/modules/legal/risk-register`
- `/modules/legal/data-privacy`
- `/modules/legal/insurance-licences`

---

### E-commerce (`/modules/ecommerce`)
- `/modules/ecommerce/product-catalogue`
- `/modules/ecommerce/order-management`
- `/modules/ecommerce/storefront`
- `/modules/ecommerce/marketplace-sync`
- `/modules/ecommerce/subscription-products`
- `/modules/ecommerce/digital-downloads`

---

### Communications (`/modules/communications`)
- `/modules/communications/internal-chat`
- `/modules/communications/announcements`
- `/modules/communications/booking-scheduling`
- `/modules/communications/intranet`
- `/modules/communications/meeting-video`

---

### Learning & Development (`/modules/lms`)
- `/modules/lms/course-builder`
- `/modules/lms/skills-matrix`
- `/modules/lms/succession-planning`
- `/modules/lms/mentoring`
- `/modules/lms/external-training`

---

## Comparison Pages (`/compare/{slug}`)

These pages are high-intent SEO gold. Someone searching "FlowFlex vs BambooHR" is actively evaluating. These pages must be honest — don't claim superiority on things that aren't true. Do highlight genuine differences.

**Pages to build:**
- `/compare/vs-bamboohr` — FlowFlex vs BambooHR
- `/compare/vs-jira` — FlowFlex vs Jira
- `/compare/vs-xero` — FlowFlex vs Xero
- `/compare/vs-hubspot` — FlowFlex vs HubSpot
- `/compare/vs-salesforce` — FlowFlex vs Salesforce
- `/compare/vs-notion` — FlowFlex vs Notion
- `/compare/vs-monday` — FlowFlex vs Monday.com
- `/compare/vs-microsoft365` — FlowFlex vs Microsoft 365
- `/compare/vs-sage` — FlowFlex vs Sage
- `/compare/vs-quickbooks` — FlowFlex vs QuickBooks

**Comparison page template:**
1. Hero: "FlowFlex vs [Competitor] — which is right for your business?"
2. TL;DR comparison summary (2 sentences)
3. Side-by-side feature table (20–30 rows)
4. "When to choose FlowFlex" (4 bullets)
5. "When [Competitor] might be a better fit" (2–3 bullets — honest)
6. Pricing comparison
7. CTA: "Try FlowFlex free for 14 days"

## Content Priority

Build pages in this order:
1. `/features` overview (launch with)
2. All 13 domain overview pages (launch with)
3. HR module pages (Phase 2 is built — SEO ROI immediate)
4. Projects module pages (same)
5. Finance module pages (Phase 3)
6. Comparison pages (build alongside module pages)
7. All remaining module pages (Phase 5 onward)

## Related

- [[SEO Strategy]]
- [[Admin Panel CMS]]
- [[Homepage]]
- [[Page Structure & Sitemap]]
