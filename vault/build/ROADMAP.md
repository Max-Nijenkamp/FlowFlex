---
type: build
category: roadmap
color: "#F97316"
last-updated: 2026-05-13
---

# Build Roadmap

35 phases. Each phase = one domain. Build all modules within a phase before moving to the next. Prerequisites listed — do not skip ahead.

---

## Phase Overview

| Phase | Domain | Panel | Modules | Milestone | Prerequisites |
|---|---|---|---|---|---|
| 0 | Foundation | — | 5 | Dev environment + scaffold running | None |
| 1 | Core Platform | /app | 14 | Multi-tenant SaaS live, billing wired | Phase 0 |
| 2 | HR & People | /hr | 23 | Full employee lifecycle, payroll, leave | Phase 1 |
| 3 | Finance & Accounting | /finance | 12 | Invoicing, GL, expenses, bank feeds | Phase 1 |
| 4 | Projects & Work | /projects | 11 | Tasks, kanban, sprints, time tracking | Phase 1 |
| 5 | CRM & Sales | /crm | 16 | Pipeline, contacts, deals, forecasting | Phase 1 |
| 6 | Document Management | /dms | 5 | Document library, approval workflows | Phase 1 |
| 7 | Marketing | /marketing | 10 | Campaigns, email, landing pages, SEO | Phases 5, 6 |
| 8 | Support & Help Desk | /support | 7 | Tickets, knowledge base, SLA, chat | Phases 5, 1 |
| 9 | Omnichannel Inbox | /inbox | 7 | WhatsApp, email, SMS, social, shared inbox | Phase 8 |
| 10 | Customer Success | /cs | 7 | Health scores, churn risk, playbooks | Phases 5, 8 |
| 11 | E-commerce | /ecommerce | 15 | Storefront, orders, payments, promotions | Phases 3, 12 |
| 12 | Operations | /operations | 8 | Inventory, warehouse, purchasing, logistics | Phase 3 |
| 13 | Procurement | /procurement | 5 | Purchase requisitions, POs, spend analytics | Phases 3, 12 |
| 14 | Communications | /comms | 8 | Messaging, announcements, AI voice, video | Phases 2, 1 |
| 15 | Learning & Dev | /lms | 10 | Courses, certifications, skills, learning paths | Phases 2, 6 |
| 16 | Analytics & BI | /analytics | 7 | Custom dashboards, reports, KPIs, connectors | Phases 2–5 |
| 17 | AI & Automation | /ai | 9 | Copilot, workflow builder, document AI, OCR | Phases 4–16 |
| 18 | IT & Security | /it | 10 | Assets, helpdesk, IAM, vulnerability, licences | Phases 2, 1 |
| 19 | Legal & Compliance | /legal | 8 | Contracts, e-signatures, matter management | Phases 6, 2 |
| 20 | Subscription Billing | /billing | 5 | SaaS subscriptions, MRR, dunning, revenue | Phase 3 |
| 21 | Financial Planning | /fpa | 5 | Budgets, forecasting, scenarios, variance | Phases 3, 2 |
| 22 | Partner & Channel | /partners | 6 | Partner portal, deal registration, commissions | Phases 5, 3 |
| 23 | Product-Led Growth | /plg | 6 | Feature flags, in-app guides, trial, activation | Phases 16, 1 |
| 24 | Community & Social | /community | 7 | Forums, groups, badges, member directory | Phase 14 |
| 25 | Professional Services | /psa | 6 | Engagements, utilisation, professional billing | Phases 4, 3, 2 |
| 26 | Workplace & Facility | /workplace | 5 | Desk booking, meeting rooms, visitors | Phase 2 |
| 27 | Events Management | /events | 6 | Event creation, registrations, tickets, speakers | Phases 5, 3 |
| 28 | Business Travel | /travel | 5 | Travel requests, bookings, expense reconciliation | Phases 2, 3 |
| 29 | ESG & Sustainability | /esg | 6 | Carbon tracking, ESG KPIs, supplier ratings | Phases 12, 2 |
| 30 | Field Service | /field | 6 | Work orders, dispatch, parts, job invoicing | Phases 12, 5, 3 |
| 31 | Pricing Management | /pricing | 4 | Price books, discount rules, competitive pricing | Phases 5, 11 |
| 32 | Risk Management | /risk | 5 | Risk register, controls, assessments, compliance | Phases 19, 16 |
| 33 | Whistleblowing & Ethics | /ethics | 6 | Incident reports, investigations, policy sign-off | Phases 2, 19, 6 |
| 34 | Real Estate | /realestate | 6 | Properties, leases, tenant management, IFRS 16 | Phase 3 |

**Total: 281 modules across 35 phases**

---

## Phase Groups

```
GROUP A — Infrastructure (Phases 0–1)
  Build the platform itself. Nothing else can run without this.

GROUP B — Core Business Tools (Phases 2–6)
  The modules every company needs on day one.
  Land any SMB with HR + Finance + Projects + CRM + DMS.

GROUP C — Revenue & Customer (Phases 7–10)
  Extend the CRM into a full customer lifecycle stack.
  Marketing → Support → Inbox → Success.

GROUP D — Commerce & Supply (Phases 11–13)
  Product and inventory companies. E-commerce needs Operations first.

GROUP E — People & Culture (Phases 14–15)
  Internal communications and learning on top of the HR foundation.

GROUP F — Intelligence (Phases 16–17)
  Analytics and AI — cross-domain. Build last so there is data to analyse.

GROUP G — Enterprise & Compliance (Phases 18–19)
  IT and Legal — needed once company size or regulatory exposure grows.

GROUP H — Financial Depth (Phases 20–21)
  SaaS billing and FP&A — extend the Finance foundation.

GROUP I — Growth & GTM (Phases 22–23)
  Partner channel and product-led growth — build after CRM is solid.

GROUP J — Specialist Domains (Phases 24–34)
  Community, PSA, Workplace, Events, Travel, ESG, Field Service,
  Pricing, Risk, Ethics, Real Estate — activate as needed.
```

---

## Phase Detail

---

### Phase 0 — Foundation
**Panel:** scaffold (no standalone panel)  
**Deliverable:** Docker dev environment running, Filament panels registered, multi-tenancy wired, test suite green  
**Prerequisites:** None  

| Module | Key | What it delivers |
|---|---|---|
| laravel-scaffold | foundation.scaffold | Laravel 13 app skeleton, env config, Dockerfile, CI pipeline |
| docker-environment | foundation.docker | Docker Compose: app, postgres, redis, meilisearch, reverb, horizon |
| filament-panels | foundation.panels | All 34 domain panels registered, admin panel, brand theme |
| multi-tenancy-layer | foundation.tenancy | CompanyContext, CompanyScope, BelongsToCompany, queue middleware |
| test-suite | foundation.tests | Pest config, SQLite in-memory, factory base classes, CI integration |

---

### Phase 1 — Core Platform
**Panel:** /app  
**Deliverable:** Companies can sign up, configure their workspace, pay, and access the module marketplace  
**Prerequisites:** Phase 0  

| Module | Key | What it delivers |
|---|---|---|
| setup-wizard | core.setup | First-run wizard: company profile, branding, first admin user |
| company-settings | core.settings | Workspace config: name, logo, locale, timezone, domains |
| billing-engine | core.billing | Stripe integration, subscription plans, module activation/deactivation |
| module-marketplace | core.marketplace | In-app module catalogue, one-click activation, pricing display |
| notifications | core.notifications | In-app + email notification system, preferences, digest |
| audit-log | core.audit | Append-only activity log, per-record history tab on every resource |
| data-import | core.import | CSV/Excel import wizard for any entity, column mapping, validation |
| data-privacy | core.privacy | GDPR/CCPA: DSR requests, consent records, breach log, DPA register |
| file-storage | core.storage | S3/R2 file management, signed URLs, media library integration |
| webhooks | core.webhooks | Outbound webhook endpoints, event subscriptions, HMAC delivery |
| api-clients | core.api | Sanctum API key management, scopes, usage analytics |
| sandbox-environments | core.sandbox | Isolated demo/test environment per company with seeded data |
| survey-builder | core.surveys | Reusable NPS/CSAT/custom survey engine shared by HR, support, marketing |
| i18n | core.i18n | Locale management, translation strings, per-user language preference |

---

### Phase 2 — HR & People
**Panel:** /hr  
**Deliverable:** Full employee lifecycle — profiles, contracts, onboarding, leave, payroll, performance, org chart  
**Prerequisites:** Phase 1  

| Module | Key | What it delivers |
|---|---|---|
| employee-profiles | hr.profiles | Master employee record: personal info, employment, emergency contacts |
| org-chart | hr.org-chart | Interactive org tree (D3.js/OrgChart.js), reporting lines, department view |
| recruitment | hr.recruitment | ATS: job postings, applicant pipeline, interview scheduling, offers |
| onboarding | hr.onboarding | New hire checklist wizard, document collection, IT provisioning tasks |
| leave-management | hr.leave | Leave types, balance tracking, approval workflow, calendar view |
| shift-scheduling | hr.shifts | Weekly shift grid, drag-and-drop scheduling, availability management |
| time-attendance | hr.time | Clock-in/out, timesheets, overtime rules, integration with payroll |
| payroll | hr.payroll | Domestic payroll run, deductions, payslips (PDF), bank file export |
| global-payroll | hr.global-payroll | Multi-country payroll via Remote.com API, cross-border compliance |
| compensation-benefits | hr.comp | Salary history, benefits enrolment, equity tracking |
| salary-benchmarking | hr.salary-benchmarking | Pay bands (min/mid/max), comp review cycles, compa-ratio, merit modelling |
| pay-transparency | hr.pay-transparency | Band publication, gender pay gap reports, equal pay audit, jurisdiction config |
| performance-reviews | hr.performance | Review cycles, goal tracking, 360 feedback, calibration |
| employee-feedback | hr.feedback | Continuous feedback, kudos, manager check-ins |
| pulse-surveys | hr.pulse-surveys | Anonymous recurring pulse surveys, eNPS, AI sentiment analysis |
| employee-wellbeing | hr.wellbeing | Wellbeing scores, EAP links, burnout risk alerts |
| dei-metrics | hr.dei | D&I KPIs, representation dashboard, hiring funnel equity analysis |
| employee-benefits | hr.benefits | Benefits catalogue, enrolment, provider integrations |
| employee-self-service | hr.self-service | Employee portal: payslips, leave requests, document access |
| workforce-planning | hr.workforce | Headcount plan vs actuals, open role tracking, attrition forecast |
| succession-planning | hr.succession | Key person identification, successor mapping, readiness scores |
| talent-intelligence | hr.talent | Skills heatmap, competency gap analysis, AI talent insights |
| hr-analytics | hr.analytics | HR KPI dashboard: headcount, turnover, time-to-hire, absenteeism |

---

### Phase 3 — Finance & Accounting
**Panel:** /finance  
**Deliverable:** Full double-entry accounting — GL, invoicing, expenses, bank reconciliation, financial reporting  
**Prerequisites:** Phase 1  

| Module | Key | What it delivers |
|---|---|---|
| general-ledger | finance.gl | Chart of accounts, journal entries, trial balance, period close |
| invoicing | finance.invoicing | Customer invoices, PDF generation (dompdf), Stripe payment links, sequences |
| expenses | finance.expenses | Employee expense claims, receipt capture (OCR), approval, reimbursement |
| accounts-receivable | finance.ar | Customer balances, aging report, collections workflow, credit limits |
| accounts-payable | finance.ap | Supplier invoices, payment runs, bank file export, aging |
| bank-accounts | finance.bank | Bank account register, Plaid/TrueLayer sync, reconciliation workspace |
| multi-currency | finance.fx | Exchange rates (Open Exchange Rates API), FX gain/loss posting |
| budgets | finance.budgets | Department budgets, budget vs actuals, variance alerts |
| cash-flow | finance.cashflow | Cash flow statement, 13-week forecast, scenario toggle |
| fixed-assets | finance.assets | Asset register, depreciation schedules, disposal recording |
| tax-management | finance.tax | Tax codes, VAT/GST returns, tax report export |
| financial-reporting | finance.reports | P&L, balance sheet, cash flow — custom Filament pages, PDF + Excel export |

---

### Phase 4 — Projects & Work
**Panel:** /projects  
**Deliverable:** Full work management — tasks, kanban board, sprints, Gantt, time tracking, OKRs  
**Prerequisites:** Phase 1  

| Module | Key | What it delivers |
|---|---|---|
| tasks | projects.tasks | Task list with assignee, due date, priority, subtasks, comments |
| kanban | projects.kanban | Custom KanbanBoardPage — SortableJS + Livewire + Reverb for live moves |
| sprints | projects.sprints | Sprint planning, backlog, burndown chart, velocity tracking |
| gantt | projects.gantt | Gantt chart (Frappe Gantt), dependencies, critical path |
| milestones | projects.milestones | Project milestones, completion gates, milestone email notifications |
| time-tracking | projects.time | Timer (Alpine.js), manual entry, timesheet approval, billable/non-billable |
| documents | projects.documents | Project document store (DMS integration), version history |
| wikis | projects.wikis | Project wiki pages (Tiptap editor), nested pages, table of contents |
| portfolios | projects.portfolios | Multi-project portfolio view, RAG status, resource allocation overview |
| okrs | projects.okrs | Objective and key result tracking, check-ins, progress roll-up |
| approvals | projects.approvals | Approval request workflow for any entity in the projects domain |

---

### Phase 5 — CRM & Sales
**Panel:** /crm  
**Deliverable:** Full sales cycle — contacts, pipeline, deals, forecasting, quotes, sequences  
**Prerequisites:** Phase 1  

| Module | Key | What it delivers |
|---|---|---|
| contacts | crm.contacts | People and company records, custom fields, merge duplicates, import |
| deals | crm.deals | Deal records with value, close date, stage, owner, linked contacts |
| pipeline | crm.pipeline | Kanban deal pipeline — custom PipelineBoardPage, drag-and-drop stages |
| activities | crm.activities | Calls, emails, meetings, tasks logged against contacts and deals |
| quotes | crm.quotes | Quote builder, line items, discount, PDF, e-signature integration |
| contracts | crm.contracts | Customer contract records linked to deals and signed quotes |
| email-integration | crm.email | Gmail + Outlook sync (OAuth2), two-way email logging on contact record |
| appointment-scheduling | crm.scheduling | Booking pages (Calendly-style), calendar sync, payment on booking |
| sales-sequences | crm.sequences | Multi-step outreach sequences: email + call + task steps, enrol contacts |
| customer-segments | crm.segments | Dynamic contact segments by any field combination |
| territory-management | crm.territory | Sales territory assignment, rep routing, territory performance reports |
| deal-rooms | crm.deal-rooms | Buyer microsite (Vue 3 + Inertia) — shared deal workspace with stakeholders |
| forecasting | crm.forecasting | AI-weighted sales forecast, pipeline coverage, win probability |
| revenue-intelligence | crm.revenue | Deal velocity, win/loss analysis, conversion rates, rep leaderboard |
| loyalty | crm.loyalty | Customer loyalty points, rewards catalogue, tier management |
| referral-program | crm.referrals | Referral links, conversion tracking, reward automation |

---

### Phase 6 — Document Management
**Panel:** /dms  
**Deliverable:** Centralised document library with approval workflows, templates, and retention policies  
**Prerequisites:** Phase 1  

| Module | Key | What it delivers |
|---|---|---|
| document-library | dms.library | Folder tree, drag-and-drop upload, tagging, full-text search (Meilisearch) |
| document-templates | dms.templates | Reusable document templates with variable fields, clone to create new |
| document-workflows | dms.workflows | Approval chains for documents: sequential or parallel reviewers |
| document-collaboration | dms.collab | Tiptap Y.js collaborative editing, comments, version history |
| document-retention | dms.retention | Retention schedule rules, auto-archiving, legal hold flag |

---

### Phase 7 — Marketing
**Panel:** /marketing  
**Deliverable:** Campaign management, email marketing, landing pages, SEO tools, lead capture  
**Prerequisites:** Phases 5 (CRM contacts), 6 (DMS templates)  

| Module | Key | What it delivers |
|---|---|---|
| campaigns | marketing.campaigns | Multi-channel campaigns with send schedule, audience, and reporting |
| email-marketing | marketing.email | Drag-and-drop email builder (Unlayer), SES/Mailgun, list management |
| landing-pages | marketing.landing | Landing page builder, custom domains, A/B test integration |
| lead-capture | marketing.forms | Embed forms, pop-ups, routing rules → auto-create CRM contacts |
| content-calendar | marketing.calendar | Marketing content plan — calendar view, status workflow, team assignments |
| seo-tools | marketing.seo | Page meta editor, sitemap generator, redirect management, crawl report |
| social-scheduling | marketing.social | LinkedIn/X/Facebook/Instagram/TikTok post scheduling via platform APIs |
| a-b-testing | marketing.ab | A/B test management, statistical significance calculator, winner promotion |
| analytics | marketing.analytics | Campaign attribution, UTM tracking, lead source report, funnel |
| review-management | marketing.reviews | Aggregate G2/Trustpilot/Google reviews, respond, request review campaigns |

---

### Phase 8 — Support & Help Desk
**Panel:** /support  
**Deliverable:** External customer ticket management — email-to-ticket, knowledge base, live chat, SLAs  
**Prerequisites:** Phases 5 (contacts), 1 (notifications)  

| Module | Key | What it delivers |
|---|---|---|
| support-tickets | support.tickets | Email-to-ticket, assignment, priority, status workflow, TicketDetailPage |
| knowledge-base | support.knowledge-base | Public help centre (Vue 3), Tiptap editor, Meilisearch, SEO, article analytics |
| live-chat-widget | support.live-chat | 15 KB shadow DOM widget, Reverb real-time, AI first response from KB |
| sla-management | support.sla | SLA policies (by priority + business hours), breach alerts, pause on pending |
| canned-responses | support.canned-responses | `//` shortcut search, variable substitution, personal/team/company scope |
| ticket-automations | support.automations | IF/THEN rule builder, AutomationBuilderPage, time-based triggers |
| support-analytics | support.analytics | CSAT, first response time, resolution time, volume trends, agent table |

---

### Phase 9 — Omnichannel Inbox
**Panel:** /inbox  
**Deliverable:** Unified external customer inbox — WhatsApp, email, SMS, Instagram, Facebook in one place  
**Prerequisites:** Phase 8 (Support — shares data model)  

| Module | Key | What it delivers |
|---|---|---|
| shared-inbox | inbox.shared | Three-panel InboxPage, Reverb real-time, contact identification, labels |
| whatsapp-channel | inbox.whatsapp | Meta Cloud API, 24hr window, template sync, WhatsAppSetupWizard |
| email-channel | inbox.email | Mailgun/SES inbound + IMAP fallback, In-Reply-To threading |
| sms-channel | inbox.sms | Twilio/Vonage, STOP opt-out compliance, TCPA consent gate |
| social-inbox | inbox.social | Instagram DM + Facebook Messenger via Meta Graph API |
| inbox-automations | inbox.automations | Round-robin assignment, auto-label, business-hours-aware rules |
| inbox-analytics | inbox.analytics | Volume by channel, response times, CSAT, busiest hours heatmap |

---

### Phase 10 — Customer Success
**Panel:** /cs  
**Deliverable:** Proactive customer health management — health scores, churn risk, playbooks, QBRs  
**Prerequisites:** Phases 5 (CRM), 8 (Support)  

| Module | Key | What it delivers |
|---|---|---|
| health-scores | cs.health | Configurable health score (usage + engagement + support + NPS), trend chart |
| churn-risk | cs.churn | AI churn risk model, risk tier alerts (red/amber/green), intervention prompts |
| playbooks | cs.playbooks | CS playbook library triggered by health events or lifecycle stage |
| success-plans | cs.plans | Co-created success plan with customer: goals, milestones, owner |
| onboarding-tracking | cs.onboarding | Customer onboarding progress tracker, completion %, time-to-value |
| support-tickets | cs.tickets | CS view into support ticket history per account — escalation path |
| knowledge-base-analytics | cs.kb-analytics | Failed searches, article gaps, content recommendations per segment |

---

### Phase 11 — E-commerce
**Panel:** /ecommerce  
**Deliverable:** Full online store — products, storefront, orders, payments, promotions, reviews  
**Prerequisites:** Phases 3 (Finance payments), 12 (Operations inventory)  

| Module | Key | What it delivers |
|---|---|---|
| products | ecommerce.products | Product catalogue, variants, SKUs, images, digital products |
| storefront | ecommerce.storefront | Vue 3 + Inertia public storefront, custom domain, SEO, geo-redirect |
| orders | ecommerce.orders | Order management, fulfilment status, shipping labels, customer emails |
| payments | ecommerce.payments | Stripe Checkout, payment methods, refunds, fraud detection |
| returns | ecommerce.returns | Return requests, refund approval, restocking, credit notes |
| inventory-sync | ecommerce.inventory | Live inventory sync with Operations domain warehouse |
| promotions | ecommerce.promotions | Discount codes, automatic discounts, BOGO, bundle offers |
| abandoned-carts | ecommerce.abandoned | Abandoned cart recovery email sequences, recovery rate analytics |
| product-reviews | ecommerce.reviews | Customer review collection, moderation, star rating display on storefront |
| recommendations | ecommerce.recommendations | AI product recommendations (embeddings + collaborative filtering) |
| multi-channel | ecommerce.multi-channel | Amazon SP-API, eBay REST, Meta Shops — unified order management |
| bundles | ecommerce.bundles | Product bundle builder, bundle pricing, component availability logic |
| subscriptions | ecommerce.subscriptions | Recurring product subscriptions, billing via Stripe, pause/cancel |
| gift-cards | ecommerce.gift-cards | Digital gift card issuance, balance tracking, redemption at checkout |
| analytics | ecommerce.analytics | Revenue, conversion rate, AOV, product performance, cohort LTV |

---

### Phase 12 — Operations
**Panel:** /operations  
**Deliverable:** Inventory, warehouse management, purchasing, production planning, supplier management  
**Prerequisites:** Phase 3 (Finance — purchase orders link to AP)  

| Module | Key | What it delivers |
|---|---|---|
| inventory | operations.inventory | Stock items, stock levels, reorder points, stock movements, multi-location |
| warehousing | operations.warehousing | Warehouse locations, bin management, pick lists, stock transfers |
| purchase-orders | operations.pos | PO creation, approval, supplier delivery confirmation, AP integration |
| supplier-management | operations.suppliers | Supplier records, contact details, performance ratings, catalogue |
| production-planning | operations.production | Work orders, BOM, production runs, yield tracking |
| quality-control | operations.qc | Inspection checklists, pass/fail recording, non-conformance reports |
| logistics | operations.logistics | Shipment tracking, carrier integrations, delivery status, freight costs |
| fleet-management | operations.fleet | Vehicle register, maintenance schedules, fuel log, driver assignment |

---

### Phase 13 — Procurement
**Panel:** /procurement  
**Deliverable:** Purchase requisition to PO to goods receipt workflow with spend analytics  
**Prerequisites:** Phases 3 (Finance), 12 (Operations suppliers)  

| Module | Key | What it delivers |
|---|---|---|
| purchase-requisitions | procurement.requisitions | Staff raise PRs, manager approval, budget check, auto-PO on approval |
| purchase-orders | procurement.pos | PO management (extends Operations POs with procurement approval layer) |
| supplier-catalog | procurement.catalog | Approved supplier catalogue, contracted items, preferred pricing |
| goods-received-notes | procurement.grn | Three-way match: PO → GRN → supplier invoice, discrepancy flagging |
| spend-analytics | procurement.analytics | Spend by category, supplier, department, period — savings tracking |

---

### Phase 14 — Communications
**Panel:** /comms  
**Deliverable:** Internal communications — team messaging, announcements, AI voice, video conferencing  
**Prerequisites:** Phases 2 (HR — employee directory), 1 (Core notifications)  

| Module | Key | What it delivers |
|---|---|---|
| messaging | comms.messaging | Three-panel MessagingPage, Reverb real-time, DMs + group conversations |
| team-channels | comms.channels | Persistent team channels, topic threads, pinned messages, @mentions |
| announcements | comms.announcements | Company announcements, read receipts, priority levels, targeted audiences |
| email-broadcasts | comms.broadcasts | Internal broadcast emails to staff segments via SES/Mailgun |
| notification-center | comms.notifications | Aggregated notification feed across all domains, preference management |
| video-conferencing | comms.video | Daily.co or Whereby embed — start video call from messaging or calendar |
| ai-voice | comms.ai-voice | AI phone receptionist (Twilio + Deepgram + ElevenLabs), call transcription |
| live-chat-widget | comms.live-chat | Embeddable chat widget for company's own website — feeds into inbox |

---

### Phase 15 — Learning & Development
**Panel:** /lms  
**Deliverable:** Internal LMS — courses, lessons, certifications, skills matrix, learning paths  
**Prerequisites:** Phases 2 (HR employees), 6 (DMS content storage)  

| Module | Key | What it delivers |
|---|---|---|
| courses | lms.courses | Course builder, lesson sequencing, video (Mux), quiz, completion tracking |
| learning-paths | lms.paths | Ordered course sequences, prerequisite gates, path completion certificate |
| certifications | lms.certs | Certification issuance (PDF), expiry dates, renewal reminders |
| assessments | lms.assessments | Standalone quizzes and tests, scoring, pass threshold, retake rules |
| skills | lms.skills | Skill taxonomy, employee skill profiles, gap analysis against role profiles |
| content-library | lms.library | Shared content asset library: videos, PDFs, SCORM packages, slides |
| compliance-training | lms.compliance | Mandatory training assignment, deadline tracking, completion reporting |
| leaderboards | lms.leaderboards | Gamification: XP points, badges, learner leaderboard, streaks |
| mentoring | lms.mentoring | Mentorship programme: mentor matching, session logs, goal tracking |
| analytics | lms.analytics | Course completion rates, time-to-complete, knowledge gap reports |

---

### Phase 16 — Analytics & BI
**Panel:** /analytics  
**Deliverable:** Custom dashboards, report builder, KPI tracking, data connectors, product analytics  
**Prerequisites:** Phases 2–5 (data to analyse)  

| Module | Key | What it delivers |
|---|---|---|
| dashboards | analytics.dashboards | gridstack.js + chart.js drag-and-drop dashboards, widget library, Reverb live |
| kpi-metrics | analytics.kpis | KPI definition, target vs actual tracking, trend sparklines, alerting |
| reports | analytics.reports | Report builder — drag columns, filter, group, schedule, export |
| data-connectors | analytics.connectors | External data pull: Postgres, REST API, CSV upload — unified in BI engine |
| anomaly-detection | analytics.anomaly | Z-score statistical anomaly detection, AnomalyInboxPage, alert routing |
| scheduled-reports | analytics.scheduled | Schedule report delivery by email — PDF or Excel, recipient lists |
| product-analytics | analytics.product | User event tracking, funnel analysis, retention cohorts, feature adoption |

---

### Phase 17 — AI & Automation
**Panel:** /ai  
**Deliverable:** AI copilot, workflow automation builder, document intelligence, OCR, predictive analytics  
**Prerequisites:** Phases 4–16 (cross-domain data and workflows)  

| Module | Key | What it delivers |
|---|---|---|
| copilot | ai.copilot | Anthropic Claude API, prompt caching, streaming via Reverb, context-aware |
| workflow-builder | ai.workflows | Visual IF/THEN automation builder across all domains, trigger + action library |
| document-intelligence | ai.doc-intelligence | AI field extraction from uploaded documents, template matching |
| ocr | ai.ocr | Google Cloud Vision / AWS Textract — image + PDF text extraction |
| sentiment-analysis | ai.sentiment | Sentiment scoring on support tickets, reviews, pulse survey text |
| anomaly-detection | ai.anomaly | Cross-domain anomaly detection (extends Analytics domain) |
| predictive-analytics | ai.predictive | Churn, revenue, demand forecasting — ML models via Python microservice |
| recommendation-engine | ai.recommendations | Cross-domain recommendations: next best action, content, products |
| chatbot | ai.chatbot | RAG chatbot (OpenAI Embeddings + Meilisearch + Claude) — embeddable widget |

---

### Phase 18 — IT & Security
**Panel:** /it  
**Deliverable:** Asset management, service desk, IAM, vulnerability management, software licences  
**Prerequisites:** Phases 2 (HR employees), 1 (Core notifications)  

| Module | Key | What it delivers |
|---|---|---|
| asset-management | it.assets | Hardware and software asset register, assignment, lifecycle, depreciation |
| service-desk | it.servicedesk | Internal IT ticketing — separate from external support (Phase 8) |
| access-management | it.access | User provisioning/deprovisioning, SSO config, access review workflows |
| incident-management | it.incidents | IT security incidents: severity, impact, response, post-mortem |
| change-management | it.changes | Change request, CAB approval, rollback plan, deployment window |
| vulnerability-management | it.vulns | CVE tracking, risk scoring, patch status, remediation SLA |
| software-licenses | it.licenses | Licence inventory, seat count, renewal dates, vendor contracts |
| audit-compliance | it.audit | IT compliance checklist (SOC 2, ISO 27001), evidence collection |
| capacity-planning | it.capacity | Server/resource utilisation trends, forecast, scaling recommendations |
| it-analytics | it.analytics | IT KPI dashboard: ticket volume, SLA compliance, asset health |

---

### Phase 19 — Legal & Compliance
**Panel:** /legal  
**Deliverable:** Contract management, e-signatures, matter management, compliance calendar  
**Prerequisites:** Phases 6 (DMS), 2 (HR)  

| Module | Key | What it delivers |
|---|---|---|
| contracts | legal.contracts | Contract repository, status lifecycle, renewal alerts, party management |
| e-signatures | legal.e-signatures | Native signing (PDF.js + FPDI + signature_pad.js), public signing page, audit trail |
| matter-management | legal.matters | Legal matter tracking, spend tracking, outside counsel management |
| document-review | legal.review | DocumentReviewWorkspacePage — PDF.js + annotation sidebar, version history |
| compliance-calendar | legal.calendar | Regulatory deadline calendar, filing reminders, completion tracking |
| ip-tracking | legal.ip | IP asset register (patents, trademarks, copyrights), renewal deadlines |
| regulatory-tracking | legal.regulatory | Regulatory watch list, obligation register, control mapping |
| risk-register | legal.risk | Legal risk register (distinct from enterprise risk in Phase 32) |

---

### Phase 20 — Subscription Billing
**Panel:** /billing  
**Deliverable:** SaaS subscription management, recurring invoicing, MRR tracking, dunning, revenue recognition  
**Prerequisites:** Phase 3 (Finance)  

| Module | Key | What it delivers |
|---|---|---|
| subscription-plans | billing.plans | Plan builder, pricing tiers, trial periods, feature gates |
| invoicing | billing.invoicing | Recurring invoice generation, Stripe billing, proration |
| mrr-analytics | billing.mrr | MRR, ARR, churn rate, expansion MRR, cohort revenue, LTV |
| dunning | billing.dunning | Failed payment retry logic, dunning email sequences, grace periods |
| revenue-recognition | billing.recognition | ASC 606 / IFRS 15 deferred revenue waterfall, schedule export |

---

### Phase 21 — Financial Planning
**Panel:** /fpa  
**Deliverable:** Budgeting, forecasting, scenario modelling, headcount planning, variance analysis  
**Prerequisites:** Phases 3 (Finance), 2 (HR headcount)  

| Module | Key | What it delivers |
|---|---|---|
| budget-planning | fpa.budgets | Annual budget builder by department, line item, monthly phasing |
| financial-forecasting | fpa.forecast | Rolling 12-month forecast, driver-based model, AI trend projection |
| variance-analysis | fpa.variance | Budget vs actuals vs forecast, drill-down by GL account and department |
| headcount-planning | fpa.headcount | Headcount plan vs actuals, hire plan cost modelling, attrition simulation |
| kpi-scorecards | fpa.kpis | Executive KPI scorecard, RAG status, commentary, board pack export |

---

### Phase 22 — Partner & Channel
**Panel:** /partners  
**Deliverable:** Partner portal, deal registration, commissions, affiliate tracking  
**Prerequisites:** Phases 5 (CRM), 3 (Finance payouts)  

| Module | Key | What it delivers |
|---|---|---|
| partner-portal | partners.portal | Separate auth guard, Vue 3 + Inertia portal, tier display, dashboard |
| deal-registration | partners.deal-registration | Partner deal submission, 90-day protection, CRM sync, status workflow |
| partner-commissions | partners.commissions | Commission rules engine, approval, Stripe Connect payout, PDF statements |
| partner-onboarding | partners.onboarding | Public application, checklist, LMS training tracks, completion score |
| co-marketing | partners.co-marketing | Asset library, MDF budget + request + proof workflow, co-brand generator |
| affiliate-management | partners.affiliates | Referral links, first-party tracking, fraud detection, GDPR-safe IP hashing |

---

### Phase 23 — Product-Led Growth
**Panel:** /plg  
**Deliverable:** Feature flags, in-app guides, trial management, product analytics, activation metrics  
**Prerequisites:** Phases 16 (Analytics), 1 (Core)  

| Module | Key | What it delivers |
|---|---|---|
| feature-flags | plg.flags | Feature flag management, percentage rollout, company/user targeting |
| onboarding-flows | plg.onboarding | In-app onboarding checklist and tooltip flows, completion tracking |
| trial-management | plg.trials | Trial period control, extension, conversion trigger, expiry notifications |
| activation-metrics | plg.activation | Activation rate tracking, time-to-value dashboard, cohort analysis |
| usage-analytics | plg.usage | Feature usage heatmap, session replay, user journey paths |
| changelog | plg.changelog | Public changelog page, in-app "What's New" widget, notification on release |

---

### Phase 24 — Community & Social
**Panel:** /community  
**Deliverable:** Discussion forums, member directory, groups, badges, moderation  
**Prerequisites:** Phase 14 (Communications — shared messaging patterns)  

| Module | Key | What it delivers |
|---|---|---|
| forums | community.forums | Public/private discussion forums, threads, replies, voting, Meilisearch search |
| groups | community.groups | Subgroups/circles within the community, group membership, group feeds |
| member-profiles | community.profiles | Public member profile: bio, skills, contributions, badges earned |
| badges | community.badges | Achievement badge engine, earn criteria, badge display on profiles |
| tiers | community.tiers | Member tier levels (XP-based), tier benefits, tier display |
| events-calendar | community.events | Community events calendar (distinct from Events Management domain) |
| moderation | community.moderation | Content flagging, moderator review queue, ban/warn actions, audit log |

---

### Phase 25 — Professional Services
**Panel:** /psa  
**Deliverable:** Client engagement tracking, resource utilisation, professional billing  
**Prerequisites:** Phases 4 (Projects), 3 (Finance), 2 (HR)  

| Module | Key | What it delivers |
|---|---|---|
| project-delivery | psa.delivery | Client engagement tracker: scope, budget, status, milestone burn |
| resource-planning | psa.resources | Resource allocation across engagements, utilisation % per person |
| capacity-planning | psa.capacity | Forward-looking resource demand vs supply, bench visibility |
| time-billing | psa.billing | Billable time tracking, rate cards, invoice generation from timesheets |
| profitability | psa.profitability | Engagement margin: revenue vs cost of people, software, subcontractors |
| client-reporting | psa.reporting | Client-facing status reports and dashboards (via client portal) |

---

### Phase 26 — Workplace & Facility
**Panel:** /workplace  
**Deliverable:** Desk booking, meeting room booking, visitor management, maintenance requests  
**Prerequisites:** Phase 2 (HR — employee directory)  

| Module | Key | What it delivers |
|---|---|---|
| office-spaces | workplace.spaces | Floor plan registry, space types, capacity, amenity tagging |
| desk-booking | workplace.desks | SVG floor plan hotspot picker, Reverb real-time availability |
| visitor-management | workplace.visitors | Pre-registration, QR check-in, badge print, host notification |
| maintenance-requests | workplace.maintenance | Facility issue reporting, assignee, status, resolution tracking |
| occupancy-analytics | workplace.analytics | Desk utilisation heatmap, peak hour analysis, space planning report |

---

### Phase 27 — Events Management
**Panel:** /events  
**Deliverable:** Event creation, registrations, ticketing (Stripe), speakers, sponsors  
**Prerequisites:** Phases 5 (CRM), 3 (Finance)  

| Module | Key | What it delivers |
|---|---|---|
| events | events.events | Event creation: type, date, venue, capacity, description, agenda |
| registrations | events.registrations | Registration form, waitlist, confirmation email, attendee list |
| speakers | events.speakers | Speaker profiles, session assignment, bio pages, speaker portal |
| sponsors | events.sponsors | Sponsor tiers, logo placement, activation tracking |
| check-in | events.check-in | QR code check-in, badge print, on-the-day attendance tracking |
| post-event-analytics | events.analytics | Attendance rate, session ratings, NPS survey, revenue report |

---

### Phase 28 — Business Travel
**Panel:** /travel  
**Deliverable:** Travel request approval, booking management, travel policy, expense reconciliation  
**Prerequisites:** Phases 2 (HR), 3 (Finance)  

| Module | Key | What it delivers |
|---|---|---|
| travel-requests | travel.requests | Trip request: destination, dates, purpose, cost estimate, approval |
| traveller-profiles | travel.profiles | Passport details, loyalty numbers, seat/meal preferences per employee |
| travel-policies | travel.policies | Class rules, daily rate caps, preferred suppliers, out-of-policy alerts |
| bookings | travel.bookings | Booking record (flights, hotels, rail) — manual entry or TMC API sync |
| expense-reports | travel.expenses | Post-trip expense report linked to travel request, receipt OCR, Finance sync |

---

### Phase 29 — ESG & Sustainability
**Panel:** /esg  
**Deliverable:** Carbon footprint tracking, ESG KPIs, sustainability reports, supply chain ratings  
**Prerequisites:** Phases 12 (Operations), 2 (HR)  

| Module | Key | What it delivers |
|---|---|---|
| carbon-footprints | esg.carbon | Scope 1/2/3 emissions tracking, emission factor library, carbon dashboard |
| esg-kpis | esg.kpis | ESG KPI library (GRI/SASB/TCFD aligned), target setting, progress tracking |
| sustainability-initiatives | esg.initiatives | Project-based sustainability initiatives: goal, owner, status, impact |
| supply-chain | esg.supply-chain | Supplier ESG questionnaire, risk scoring, EcoVadis score import |
| stakeholder-reporting | esg.reporting | ESG report builder for GRI/CSRD/BRSR disclosure, PDF/Excel export |
| esg-reports | esg.published | Published ESG report archive, version history, regulatory submission log |

---

### Phase 30 — Field Service
**Panel:** /field  
**Deliverable:** Work order management, technician dispatch, parts inventory, job invoicing  
**Prerequisites:** Phases 12 (Operations inventory), 5 (CRM contacts), 3 (Finance)  

| Module | Key | What it delivers |
|---|---|---|
| work-orders | field.work-orders | Work order creation from CRM or customer request, priority, SLA |
| technician-dispatch | field.dispatch | DispatchBoardPage — Mapbox/Google Maps, Reverb live GPS, drag assignment |
| part-inventory | field.parts | Parts stock linked to Operations inventory, parts consumption on jobs |
| customer-assets | field.assets | Customer equipment register, maintenance history, warranty tracking |
| service-level-agreements | field.slas | Field SLA policies, response time targets, breach alerting |
| job-invoicing | field.invoicing | Job completion → invoice generation → Finance AP integration |

---

### Phase 31 — Pricing Management
**Panel:** /pricing  
**Deliverable:** Price books, discount rules, volume pricing, competitive price tracking  
**Prerequisites:** Phases 5 (CRM quotes), 11 (E-commerce products)  

| Module | Key | What it delivers |
|---|---|---|
| price-books | pricing.books | Multiple price books (by region, channel, customer tier), effective dates |
| product-pricing | pricing.products | Per-product pricing rules: tiered, volume, subscription, bundles |
| discount-rules | pricing.discounts | Automatic discount engine: customer segment, quantity, promo period |
| competitive-pricing | pricing.competitive | Competitor price tracking, price index alerts, positioning analysis |

---

### Phase 32 — Risk Management
**Panel:** /risk  
**Deliverable:** Enterprise risk register, risk assessments, controls mapping, compliance monitoring  
**Prerequisites:** Phases 19 (Legal), 16 (Analytics)  

| Module | Key | What it delivers |
|---|---|---|
| risk-register | risk.register | Risk records: category, likelihood, impact, inherent/residual risk score |
| risk-assessments | risk.assessments | Structured risk assessment workflow, scoring matrix, reviewer sign-off |
| risk-controls | risk.controls | Control library, control-to-risk mapping, control testing schedule |
| risk-reporting | risk.reporting | Risk dashboard, heat map, top risks report, board-level summary |
| compliance-monitoring | risk.compliance | Regulatory obligation tracking, control evidence, compliance score |

---

### Phase 33 — Whistleblowing & Ethics
**Panel:** /ethics  
**Deliverable:** EU Whistleblowing Directive-compliant incident reporting, case management, policy tracking  
**Prerequisites:** Phases 2 (HR), 19 (Legal), 6 (DMS)  

| Module | Key | What it delivers |
|---|---|---|
| incident-reports | ethics.incidents | Anonymous intake (one-time token, no IP logged), category, evidence upload |
| case-management | ethics.cases | Investigator assignment, case notes, confidential timeline, outcome |
| investigator-actions | ethics.actions | Interview logs, evidence review, escalation, interim measures |
| resolution-outcomes | ethics.outcomes | Outcome type, corrective actions, closure memo, appeal handling |
| policy-acknowledgments | ethics.policies | Policy publication, employee sign-off deadlines, completion tracking |
| analytics | ethics.analytics | Report volumes, resolution time, category breakdown, programme health |

---

### Phase 34 — Real Estate
**Panel:** /realestate  
**Deliverable:** Property register, lease management, tenant tracking, IFRS 16 accounting  
**Prerequisites:** Phase 3 (Finance — lease accounting integration)  

| Module | Key | What it delivers |
|---|---|---|
| property-register | realestate.properties | Property records: address, type, size, ownership, valuation history |
| lease-management | realestate.leases | Lease records, term dates, rent schedule, break clauses, renewal alerts |
| tenant-occupancy-management | realestate.tenants | Tenant records, occupancy periods, contact management |
| property-maintenance | realestate.maintenance | Maintenance requests, contractor assignment, cost tracking |
| rental-billing-arrears | realestate.billing | Rent invoice generation, payment tracking, arrears management |
| ifrs-16-lease-accounting | realestate.ifrs16 | Right-of-use asset, lease liability amortisation schedule, GL posting |

---

## Build Checklist Per Phase

Before marking a phase complete, every module within it must have:

- [ ] Migration written and tested
- [ ] Model with HasUlids + BelongsToCompany + SoftDeletes
- [ ] Service interface + concrete implementation + ServiceProvider binding
- [ ] Filament Resource or custom Page registered in panel
- [ ] canAccess() permission check on every Resource/Page
- [ ] Module key gating via BillingService::hasModule()
- [ ] Factory + Seeder (added to LocalDemoDataSeeder)
- [ ] Pest feature tests passing
- [ ] module_catalog entry for the module
- [ ] Left-brain spec status set to `complete`
- [ ] Build log entry created in vault/build/logs/

---

## Related

- [[build/STATUS]] — track Built vs Total per domain
- [[build/ACTIVATION]] — how to run a build session
- [[build/gaps/INDEX]] — open spec gaps
- [[build/decisions/INDEX]] — architectural decisions made during build
- [[domains/INDEX]] — all domains and module specs
- [[architecture/filament-patterns]] — must read before Phase 0
