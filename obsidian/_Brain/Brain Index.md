---
tags: [brain, index]
last_updated: 2026-05-07
---

# Brain Index

> **Before starting ANY work:** Read [[Current State]], [[Patterns]], and [[Bug Registry]].  
> **After finishing work:** Update whichever Brain notes reflect what changed.  
> **For model/relation questions:** Go to the Domain file, not the Obsidian spec. Spec = intent. Brain = what's actually built.

The implementation brain for FlowFlex. **This folder is about how the code works right now** — bugs found and fixed, patterns enforced, current build state, every file location, every model field, every relation. The spec lives in `obsidian/00-14/` folders and describes what FlowFlex *should* be. The Brain describes what *is*.

---

## Notes

| File | Purpose |
|---|---|
| [[Current State]] | Build phase, active panels, test count, API endpoints, Phase 1.5 pages, pending decisions |
| [[Codebase Map]] | Where every file lives: models, resources, factories, routes, configs, Vue pages |
| [[Patterns]] | Enforced code patterns: model traits, Filament 5 API, tenant scoping, policies, factories |
| [[Bug Registry]] | Every bug ever found and fixed, organised by phase and domain. Check before writing similar code |
| [[Test Suite]] | Test count, structure per phase, factory docs, test helper patterns, coverage gaps |
| [[Relations Map]] | Every foreign key and cross-domain relation in the system |
| [[Features]] | User-facing feature list per panel — what works right now |

---

## Domain Documentation

Full model fields, casts, relations, enums, resources, and events per domain:

| File | Domain | Models | Resources |
|---|---|---|---|
| [[Domain — Core Platform]] | Core / Admin / Workspace | Company, Tenant, User, ApiKey, File, Module, SubModule, CompanyModule, NotificationPreference, Address + 12 Marketing models | AdminPanel (12 resources) + WorkspacePanel (4 pages) |
| [[Domain — HR]] | HR & People | 26 models | 14 resources |
| [[Domain — Projects]] | Projects & Work | 12 models | 6 resources |
| [[Domain — Finance]] | Finance | 10 models | 7 resources |
| [[Domain — CRM]] | CRM & Sales | 19 models | 10 resources |

---

## Obsidian Spec Cross-Reference

The Brain is implementation truth. For product intent and full feature specs, see:

| Topic | Spec Note |
|---|---|
| Architecture, stack, multi-tenancy | `00 - Project Overview/Architecture.md` |
| Build phase roadmap | `00 - Project Overview/Build Order (Phases).md` |
| Security rules | `00 - Project Overview/Security Rules.md` |
| Performance rules | `00 - Project Overview/Performance Rules.md` |
| Naming conventions | `00 - Project Overview/Naming Conventions.md` |
| Module dev checklist | `00 - Project Overview/Module Development Checklist.md` |
| HR domain spec | `02 - HR & People/` (Employee Profiles, Payroll, Leave Management, Onboarding) |
| Projects domain spec | `03 - Projects & Work/` (Task Management, Time Tracking, Document Management) |
| Finance domain spec | `04 - Finance/` (Invoicing, Expense Management, Financial Reporting) |
| CRM domain spec | `05 - CRM & Sales/` (Contact & Company Management, Sales Pipeline, Customer Support, Shared Inbox) |
| Marketing site spec | `14 - Marketing Site/Marketing Site Overview.md` |
| Filament panel map | `Filament Panels/Panel Map.md` |

---

## Quick Reference

**Tests:** 580 passing · `XDEBUG_MODE=off php -d memory_limit=768M vendor/bin/pest --no-coverage`  
**Phase:** 1, 1.5, 2, 3 complete · Phase 4 next (Operations + Ecommerce)  
**Models:** ~90 across 6 implemented domains  
**Active panels:** admin · workspace · hr · projects · finance · crm  
**Events wired:** 36 pairs · all listeners `ShouldQueue`  
**Marketing site:** 19 pages built (Inertia + Vue, not Blade — intentional deviation from spec)
