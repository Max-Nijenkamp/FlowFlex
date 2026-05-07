---
tags: [flowflex, index, welcome]
domain: Platform
status: built
last_updated: 2026-05-06
---

# FlowFlex Knowledge Base

Welcome to the FlowFlex Obsidian vault. This is the complete documentation for the FlowFlex platform — architecture, modules, design system, and build order.

**Start here when building any feature. Every decision traces back to a note in this vault.**

## Quick Navigation

### Project Overview

- [[FlowFlex Overview]] — what it is, the problem it solves, the promise
- [[Tech Stack]] — Laravel 12, Filament 5, PostgreSQL, Redis, Stripe
- [[Architecture]] — modular monolith, event bus, module structure
- [[Multi-Tenancy]] — tenant isolation, BelongsToTenant, global scopes
- [[Build Order (Phases)]] — MVP roadmap, phases 1–6
- [[Cross-Module Event Map]] — every cross-domain event, who fires, who consumes
- [[Module Sizing Reference]] — complexity estimates and DB table counts for sprint planning
- [[Environment Setup]] — dev environment commands, .env reference, artisan shortcuts
- [[Security Rules]] — non-negotiable security rules
- [[Performance Rules]] — N+1, queues, caching, pagination
- [[Naming Conventions]] — files, classes, database, events
- [[Module Development Checklist]] — step-by-step module build guide

### Filament Panels

- [[Panel Map]] — all panels, URLs, access rules
- [[Admin Panel]] — FlowFlex super-admin
- [[Workspace Panel]] — tenant settings and billing

### Core Platform (Phase 1)

- [[Authentication & Identity]]
- [[Roles & Permissions (RBAC)]]
- [[Module Billing Engine]]
- [[Notifications & Alerts]]
- [[API & Integrations Layer]]
- [[Multi-Tenancy & Workspace]]
- [[File Storage]]

### HR & People (Phase 2)

- [[HR Overview]]
- [[Employee Profiles]] — [[Onboarding]] — [[Offboarding]]
- [[Leave Management]] — [[Payroll]]
- [[Performance & Reviews]] — [[Recruitment & ATS]]
- [[Scheduling & Shifts]] — [[Benefits & Perks]]
- [[Employee Feedback]] — [[HR Compliance]]

### Projects & Work (Phase 2)

- [[Projects Overview]]
- [[Task Management]] — [[Time Tracking]] — [[Document Management]]
- [[Project Planning]] — [[Document Approvals & E-Sign]]
- [[Knowledge Base & Wiki]] — [[Team Collaboration]]
- [[Resource & Capacity Planning]] — [[Agile & Sprint Management]]

### Finance & Accounting (Phase 3)

- [[Finance Overview]]
- [[Invoicing]] — [[Expense Management]] — [[Financial Reporting]]
- [[Accounts Payable & Receivable]] — [[Bank Reconciliation]]
- [[Budgeting & Forecasting]] — [[Client Billing & Retainers]]
- [[Tax & VAT Compliance]] — [[Fixed Asset & Depreciation]]
- [[Subscription & MRR Tracking]]

### CRM & Sales (Phase 3)

- [[CRM Overview]]
- [[Contact & Company Management]] — [[Sales Pipeline]]
- [[Shared Inbox & Email]] — [[Customer Support & Helpdesk]]
- [[Quotes & Proposals]] — [[Customer Data Platform]]
- [[Client Portal]] — [[Loyalty & Retention]]

### Marketing (Phase 4)

- [[Marketing Overview]]
- [[CMS & Website Builder]] — [[Email Marketing]] — [[Forms & Lead Capture]]
- [[Social Media Management]] — [[SEO & Analytics]]
- [[Ad Campaign Management]] — [[Events & Webinars]]
- [[Affiliate & Partner Management]]

### Operations (Phase 4)

- [[Operations Overview]]
- [[Inventory Management]] — [[Purchasing & Procurement]] — [[Asset Management]]
- [[Equipment Maintenance]] — [[Field Service Management]]
- [[Supply Chain Visibility]] — [[Point of Sale]]
- [[Quality Control & Inspections]] — [[HSE]]

### Analytics (Phase 5)

- [[Analytics Overview]]
- [[Custom Dashboards]] — [[Report Builder]] — [[KPI & Goal Tracking]]
- [[Data Warehouse & Export]] — [[Audit Log & Activity Trail]]
- [[Team Velocity & Ops Metrics]]

### IT & Security (Phase 5)

- [[IT Overview]]
- [[IT Asset Management]] — [[Internal IT Helpdesk]]
- [[SaaS Spend Management]] — [[Access & Permissions Audit]]
- [[Security & Compliance]] — [[Uptime & Status Monitoring]]

### Legal (Phase 5)

- [[Legal Overview]]
- [[Contract Management]] — [[Policy Management]] — [[Risk Register]]
- [[Data Privacy]] — [[Insurance & Licence Tracking]]

### E-commerce (Phase 5)

- [[Ecommerce Overview]]
- [[Product Catalogue]] — [[Order Management]] — [[Storefront & Checkout]]
- [[Marketplace Channel Sync]] — [[Subscription Products]]
- [[Digital Products & Downloads]]

### Communications (Phase 5)

- [[Communications Overview]]
- [[Internal Messaging & Chat]] — [[Company Announcements]]
- [[Meeting & Video Integration]] — [[Company Intranet]]
- [[Booking & Appointment Scheduling]]

### Learning & Development (Phase 5)

- [[LMS Overview]]
- [[Course Builder & LMS]] — [[Skills Matrix & Gap Analysis]]
- [[Succession Planning]] — [[Mentoring & Coaching]]
- [[External Training Requests]]

### Design System

- [[Brand Foundation]] — [[Colour System]] — [[Typography]]
- [[Spacing & Layout]] — [[Component Library]] — [[Motion & Animation]]
- [[Iconography]] — [[Dark Mode]] — [[Data Visualisation]]
- [[Writing Style & Voice]] — [[Filament Implementation]]

### Marketing Site (Public-Facing)

- [[Marketing Site Overview]] — tech approach, goals, analytics, conversion events
- [[Page Structure & Sitemap]] — every public URL, redirects, subdomain map
- [[Homepage]] — section-by-section content spec, footer
- [[Pricing Page]] — plan cards, comparison table, FAQ, structured data
- [[Features & Modules Pages]] — domain overviews, module pages, comparison pages
- [[Demo Request Flow]] — form spec, lead handling, admin pipeline, tracking
- [[SEO Strategy]] — technical SEO, content clusters, structured data, link building
- [[SEM & Paid Advertising]] — Google Ads structure, LinkedIn, remarketing, geo targeting
- [[Blog & Content Strategy]] — categories, content types, newsletter, help centre, changelog
- [[About & Company Pages]] — about, contact, careers, partners, status page
- [[Legal & Compliance Pages]] — privacy policy, terms, cookies, DPA, AUP, security page
- [[Admin Panel CMS]] — all content management resources in the admin panel

---

**144 notes · 13 domains · 99+ modules · 1 marketing site**
*Last updated: May 2026*
