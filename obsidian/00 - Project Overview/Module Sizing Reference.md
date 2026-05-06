---
tags: [flowflex, sprint-planning, sizing, complexity, build-order]
domain: Platform
status: built
last_updated: 2026-05-06
---

# Module Sizing Reference

Build complexity estimates for sprint planning. Based on Filament modular monolith approach.

**Complexity scale:** Low → Medium → High → Very High

---

## Core Platform

| Module | Complexity | Filament Resources | DB Tables |
|---|---|---|---|
| [[Authentication & Identity]] | Medium | 3 resources, 2 pages | 4 |
| [[Roles & Permissions (RBAC)]] | Medium | 2 resources | 3 (Spatie) |
| [[Module Billing Engine]] | High | 1 resource, 3 pages | 4 |
| [[Notifications & Alerts]] | Medium | 1 resource, 1 page | 2 |
| [[API & Integrations Layer]] | Low | 1 resource | 1 |
| [[Multi-Tenancy & Workspace]] | High | 2 pages | 2 (Spatie) |

---

## HR & People

| Module | Complexity | Filament Resources | DB Tables |
|---|---|---|---|
| [[Employee Profiles]] | Medium | 1 resource, 1 page | 5 |
| [[Onboarding]] | High | 3 resources, 2 pages | 6 |
| [[Offboarding]] | Medium | 2 resources | 2 |
| [[Leave Management]] | High | 2 resources, 2 pages | 5 |
| [[Payroll]] | Very High | 4 resources, 3 pages | 10 |
| [[Performance & Reviews]] | High | 3 resources, 2 pages | 8 |
| [[Recruitment & ATS]] | Very High | 5 resources, 3 pages | 10 |
| [[Scheduling & Shifts]] | High | 2 resources, 2 pages | 5 |
| [[Benefits & Perks]] | Medium | 2 resources | 4 |
| [[Employee Feedback]] | Medium | 2 resources, 2 pages | 4 |
| [[HR Compliance]] | Medium | 2 resources, 1 page | 3 |

---

## Projects & Work

| Module | Complexity | Filament Resources | DB Tables |
|---|---|---|---|
| [[Task Management]] | High | 3 resources, 4 pages | 6 |
| [[Project Planning]] | Very High | 2 resources, 3 pages | 5 |
| [[Time Tracking]] | High | 2 resources, 2 pages | 3 |
| [[Document Management]] | High | 2 resources, 1 page | 4 |
| [[Document Approvals & E-Sign]] | High | 2 resources, 2 pages | 5 |
| [[Knowledge Base & Wiki]] | High | 2 resources, 1 page | 4 |
| [[Team Collaboration]] | Medium | 1 resource | 2 |
| [[Resource & Capacity Planning]] | High | 1 resource, 2 pages | 3 |
| [[Agile & Sprint Management]] | High | 2 resources, 2 pages | 5 |

---

## Finance & Accounting

| Module | Complexity | Filament Resources | DB Tables |
|---|---|---|---|
| [[Invoicing]] | Very High | 3 resources, 2 pages | 6 |
| [[Expense Management]] | High | 2 resources, 2 pages | 4 |
| [[Accounts Payable & Receivable]] | High | 2 resources, 2 pages | 5 |
| [[Bank Reconciliation]] | Very High | 2 resources, 2 pages | 4 |
| [[Budgeting & Forecasting]] | High | 2 resources, 2 pages | 4 |
| [[Financial Reporting]] | High | 1 resource, 3 pages | 2 |
| [[Client Billing & Retainers]] | High | 2 resources, 2 pages | 4 |
| [[Tax & VAT Compliance]] | High | 1 resource, 2 pages | 3 |
| [[Fixed Asset & Depreciation]] | Medium | 1 resource, 1 page | 3 |
| [[Subscription & MRR Tracking]] | High | 1 resource, 3 pages | 4 |

---

## CRM & Sales

| Module | Complexity | Filament Resources | DB Tables |
|---|---|---|---|
| [[Contact & Company Management]] | Medium | 2 resources | 5 |
| [[Sales Pipeline]] | High | 2 resources, 2 pages | 4 |
| [[Shared Inbox & Email]] | Very High | 2 resources, 2 pages | 6 |
| [[Quotes & Proposals]] | High | 2 resources, 1 page | 4 |
| [[Customer Data Platform]] | High | 1 resource, 2 pages | 4 |
| [[Customer Support & Helpdesk]] | Very High | 3 resources, 3 pages | 8 |
| [[Client Portal]] | High | 2 pages (external) | 2 |
| [[Loyalty & Retention]] | Medium | 2 resources, 1 page | 3 |

---

## Operations

| Module | Complexity | Filament Resources | DB Tables |
|---|---|---|---|
| [[Inventory Management]] | Very High | 3 resources, 2 pages | 8 |
| [[Purchasing & Procurement]] | High | 2 resources, 2 pages | 5 |
| [[Asset Management]] | Medium | 2 resources, 1 page | 4 |
| [[Equipment Maintenance]] | High | 2 resources, 2 pages | 5 |
| [[Field Service Management]] | Very High | 4 resources, 3 pages | 9 |
| [[Supply Chain Visibility]] | High | 2 resources, 1 page | 3 |
| [[Point of Sale]] | Very High | 3 resources, 2 pages | 5 |
| [[Quality Control & Inspections]] | High | 2 resources, 2 pages | 4 |
| [[HSE]] | Medium | 2 resources, 1 page | 4 |

---

## Marketing

| Module | Complexity | Filament Resources | DB Tables |
|---|---|---|---|
| [[CMS & Website Builder]] | Very High | 3 resources, 2 pages | 6 |
| [[Email Marketing]] | Very High | 3 resources, 3 pages | 8 |
| [[Social Media Management]] | High | 2 resources, 2 pages | 5 |
| [[Forms & Lead Capture]] | High | 2 resources, 1 page | 4 |
| [[SEO & Analytics]] | Medium | 1 resource, 2 pages | 2 |
| [[Ad Campaign Management]] | Medium | 2 resources, 1 page | 3 |
| [[Events & Webinars]] | High | 2 resources, 2 pages | 5 |
| [[Affiliate & Partner Management]] | High | 2 resources, 2 pages | 4 |

---

## Phase Build Totals (Estimates)

| Phase | Modules | Est. DB Tables | Est. Filament Resources |
|---|---|---|---|
| Phase 1 — Core | 6 | ~16 | ~10 resources, 8 pages |
| Phase 2 — HR + Projects | 7 | ~35 | ~15 resources, 12 pages |
| Phase 3 — Finance + CRM | 7 | ~37 | ~15 resources, 12 pages |
| Phase 4 — Ops + Marketing | 6 | ~37 | ~13 resources, 10 pages |
| Phase 5 — Extended | ~30 | ~100+ | ~50 resources, 40 pages |

---

## Related

- [[Build Order (Phases)]]
- [[Module Development Checklist]]
- [[Architecture]]
- [[Cross-Module Event Map]]
