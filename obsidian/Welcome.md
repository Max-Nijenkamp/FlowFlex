---
tags: [flowflex, index, welcome]
domain: Platform
status: built
last_updated: 2026-05-08
---

# FlowFlex Knowledge Base

The complete product documentation for FlowFlex — a modular, multi-tenant SaaS platform that replaces 8–15 disconnected business tools with one unified workspace.

**16 domains · 170+ modules · 550+ individual features**
*Last updated: May 2026*

---

> [!tip] Start Here
> New to the codebase? Read [[FlowFlex Overview]] → [[Tech Stack]] → [[Architecture]] → [[Multi-Tenancy]] in that order.

---

## Quick Status

| Domain | Modules | Status |
|---|---|---|
| Core Platform | 7 | ✅ Built |
| HR & People | 15 | ✅ Core built · Planned extensions |
| Projects & Work | 11 | ✅ Core built · Planned extensions |
| Finance & Accounting | 14 | ✅ Core built · Planned extensions |
| CRM & Sales | 13 | Planned |
| Marketing & Content | 11 | Planned |
| Operations & Field Service | 11 | Planned |
| Analytics & BI | 8 | Planned |
| IT & Security | 8 | Planned |
| Legal & Compliance | 7 | Planned |
| E-commerce & Sales Channels | 10 | Planned |
| Communications | 9 | Planned |
| Learning & Development | 9 | Planned |
| Marketing Site | 14 pages | Planned |
| **AI & Automation** | **6** | **Planned (Phase 6)** |
| **Community & Social** | **6** | **Planned (Phase 7)** |

---

## Project Overview

- [[FlowFlex Overview]] — what it is, the problem it solves, the core promise
- [[Tech Stack]] — Laravel 13, Vue + Inertia, Filament 5, PostgreSQL, Redis, Stripe
- [[Architecture]] — modular monolith, Interface/Service pattern, DTOs, event bus
- [[Multi-Tenancy]] — tenant isolation, BelongsToCompany, global scopes
- [[Roles & Permissions (RBAC)]] — 2-layer RBAC: super-admin + company owner/roles
- [[Error Handling]] — Inertia errors, connection retry, custom 404/500 pages
- [[Rate Limiting]] — per-user, per-tenant, per-plan-tier limits
- [[Security Rules]] — non-negotiable security rules
- [[Performance Rules]] — N+1, queues, caching, pagination
- [[Naming Conventions]] — files, classes, database, events
- [[Module Development Checklist]] — step-by-step module build guide
- [[Cross-Module Event Map]] — every cross-domain event
- [[AI Strategy]] — how AI is woven through every domain

---

## Core Platform (Phase 1) ✅

- [[Authentication & Identity]]
- [[Roles & Permissions (RBAC)]]
- [[Module Billing Engine]]
- [[Notifications & Alerts]]
- [[API & Integrations Layer]]
- [[Multi-Tenancy & Workspace]]
- [[File Storage]]
- [[Setup Wizard & Guided Onboarding]]

---

## HR & People (Phase 2 core · Phase 8 extensions)

- [[HR Overview]]
- [[Employee Profiles]] · [[Onboarding]] · [[Offboarding]]
- [[Leave Management]] · [[Payroll]]
- [[Performance & Reviews]] · [[Recruitment & ATS]]
- [[Scheduling & Shifts]] · [[Benefits & Perks]]
- [[Employee Feedback]] · [[HR Compliance]]
- [[Org Chart & Workforce Planning]] · [[AI Recruiting Assistant]]
- [[DEI & Workforce Analytics]] · [[Compensation Management]]

---

## Projects & Work (Phase 2 core · Phase 8 extensions)

- [[Projects Overview]]
- [[Task Management]] · [[Time Tracking]] · [[Document Management]]
- [[Project Planning]] · [[Document Approvals & E-Sign]]
- [[Knowledge Base & Wiki]] · [[Team Collaboration]]
- [[Resource & Capacity Planning]] · [[Agile & Sprint Management]]
- [[OKR & Goal Management]] · [[Portfolio Management]]

---

## Finance & Accounting (Phase 3 core · Phase 6 extensions)

- [[Finance Overview]]
- [[Invoicing]] · [[Expense Management]] · [[Financial Reporting]]
- [[Accounts Payable & Receivable]] · [[Bank Reconciliation]]
- [[Budgeting & Forecasting]] · [[Client Billing & Retainers]]
- [[Tax & VAT Compliance]] · [[Fixed Asset & Depreciation]]
- [[Subscription & MRR Tracking]]
- [[Multi-Currency & FX Management]] · [[Open Banking & Bank Feeds]]
- [[Cash Flow Forecasting & Scenario Planning]] · [[Revenue Recognition]]

---

## CRM & Sales (Phase 3 core · Phase 8 extensions)

- [[CRM Overview]]
- [[Contact & Company Management]] · [[Sales Pipeline]]
- [[Shared Inbox & Email]] · [[Customer Support & Helpdesk]]
- [[Quotes & Proposals]] · [[Customer Data Platform]]
- [[Client Portal]] · [[Loyalty & Retention]]
- [[AI Sales Coach]] · [[Revenue Intelligence & Forecasting]]
- [[Deal Room]] · [[Sales Sequences & Cadences]]
- [[Customer Success Platform]]

---

## Marketing & Content (Phase 5)

- [[Marketing Overview]]
- [[CMS & Website Builder]] · [[Email Marketing]] · [[Forms & Lead Capture]]
- [[Social Media Management]] · [[SEO & Analytics]]
- [[Ad Campaign Management]] · [[Events & Webinars]]
- [[Affiliate & Partner Management]]
- [[AI Content Studio]] · [[SMS & WhatsApp Marketing]] · [[Push Notifications]]
- [[Influencer & UGC Management]]

---

## Operations & Field Service (Phase 4–5)

- [[Operations Overview]]
- [[Inventory Management]] · [[Purchasing & Procurement]] · [[Asset Management]]
- [[Equipment Maintenance]] · [[Field Service Management]]
- [[Supply Chain Visibility]] · [[Point of Sale]]
- [[Quality Control & Inspections]] · [[HSE]]
- [[Route Optimization & Dispatch]] · [[Vendor Portal]]

---

## Analytics, BI & Reporting (Phase 6)

- [[Analytics Overview]]
- [[Custom Dashboards]] · [[Report Builder]] · [[KPI & Goal Tracking]]
- [[Data Warehouse & Export]] · [[Audit Log & Activity Trail]]
- [[Team Velocity & Ops Metrics]]
- [[AI Insights Engine]] · [[Predictive Analytics]]

---

## IT & Security Management (Phase 4–6)

- [[IT Overview]]
- [[IT Asset Management]] · [[Internal IT Helpdesk]]
- [[SaaS Spend Management]] · [[Access & Permissions Audit]]
- [[Security & Compliance]] · [[Uptime & Status Monitoring]]
- [[SSO & Identity Provider]] · [[MDM & Device Management]]

---

## Legal & Compliance (Phase 4–7)

- [[Legal Overview]]
- [[Contract Management]] · [[Policy Management]] · [[Risk Register]]
- [[Data Privacy]] · [[Insurance & Licence Tracking]]
- [[AI Contract Intelligence]] · [[E-Signature Native]]

---

## E-commerce & Sales Channels (Phase 4–5)

- [[Ecommerce Overview]]
- [[Product Catalogue]] · [[Order Management]] · [[Storefront & Checkout]]
- [[Marketplace Channel Sync]] · [[Subscription Products]]
- [[Digital Products & Downloads]]
- [[AI Product Recommendations]] · [[Returns & Refunds Management]]
- [[Abandoned Cart Recovery]] · [[B2B Commerce Portal]]

---

## Communications & Internal Comms (Phase 5)

- [[Communications Overview]]
- [[Internal Messaging & Chat]] · [[Company Announcements]]
- [[Meeting & Video Integration]] · [[Company Intranet]]
- [[Booking & Appointment Scheduling]]
- [[Native Video Calls]] · [[Voice Channels]]
- [[Async Video Messaging]] · [[External Chat Widget]]

---

## Learning & Development (Phase 7)

- [[LMS Overview]]
- [[Course Builder & LMS]] · [[Skills Matrix & Gap Analysis]]
- [[Succession Planning]] · [[Mentoring & Coaching]]
- [[External Training Requests]]
- [[AI Learning Coach]] · [[Certification & Compliance Training]]
- [[External Learner Portal]] · [[Live Virtual Classroom]]

---

## AI & Automation (Phase 6) 🆕

The intelligence layer. Replace Zapier, Microsoft Copilot, and standalone AI tools.

- [[AI Overview]]
- [[Workflow Automation Builder]] — no-code trigger/action automation across all domains
- [[AI Assistant & Copilot]] — cross-domain AI chat with full data access
- [[AI Agents]] — autonomous background agents for recurring operations
- [[Integration Hub]] — 200+ third-party app connectors
- [[Smart Notifications & Triggers]] — intelligent alert routing
- [[AI Infrastructure]] — LLM management, cost controls, privacy

---

## Community & Social (Phase 7) 🆕

Run a branded customer or employee community. Replace Circle.so and Discord.

- [[Community Overview]]
- [[Discussion Forums & Channels]] — topic-based discussion boards
- [[Member Directory & Profiles]] — searchable member profiles
- [[Events & Meetups]] — virtual and in-person events
- [[Gamification & Reputation]] — points, badges, leaderboards
- [[Content Gating & Membership Tiers]] — access control and paid tiers

---

## Design System

- [[Brand Foundation]] · [[Colour System]] · [[Typography]]
- [[Spacing & Layout]] · [[Component Library]] · [[Motion & Animation]]
- [[Iconography]] · [[Dark Mode]] · [[Data Visualisation]]
- [[Writing Style & Voice]] · [[Filament Implementation]]
- [[AI & Conversational UI]] 🆕

---

## Marketing Site (Public-Facing)

- [[Marketing Site Overview]] · [[Page Structure & Sitemap]]
- [[Homepage]] · [[Pricing Page]] · [[Features & Modules Pages]]
- [[Demo Request Flow]] · [[SEO Strategy]] · [[SEM & Paid Advertising]]
- [[Blog & Content Strategy]] · [[About & Company Pages]]
- [[Legal & Compliance Pages]] · [[Admin Panel CMS]]
