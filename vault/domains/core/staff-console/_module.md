---
domain: core
module: staff-console
type: module
build-status: complete
status: wip
color: "#4ADE80"
updated: 2026-07-04
---

# Staff Console

The FlowFlex-staff side of the platform inside `/admin`: create and manage customer companies, activate/deactivate modules for them, see their subscriptions, invoices and users, and read platform revenue summaries (revenue this month, MRR, open invoices). Closes the loop required by the no-public-registration ADR ("staff create companies in /admin") that no MVP module owned. *(assumed: single staff role — every Admin sees everything; per-admin RBAC deferred until the team grows)*

## Module-key

`core.staff-console`

**Priority:** v1-core  
**Panel:** admin  
**Permission prefix:** none (admin-guard access — no spatie permissions on the admin guard)  
**Tables:** none of its own — reads/writes `companies`, `company_module_subscriptions`, `billing_invoices`, `user_invitations` via their owning models/services  
**Events:** fires none · consumes none (drives other domains via service calls inside a set-then-forgotten `CompanyContext`)

## Sibling notes

- [[architecture]] — resources, relation managers, widgets, provisioning action, company-context handling
- [[security]] — admin-guard access, tenancy / context-leak handling
- [[decisions]] — no public registration
- [[unknowns]] — UNVERIFIED migration / config manifest items
- Features: [[features/company-management]] · [[features/company-provisioning]] · [[features/module-management]] · [[features/billing-overview]] · [[features/platform-dashboard]]

No `data-model.md` (no own tables — short note below) and no `api.md` (exposes no events/DTOs/contracts).

## Dependencies

- **[[../billing-engine/_module]]** (core.billing, hard) — BillingService for module activation, free-core seeding, suspension; BillingInvoice for revenue data
- **core.invitations** (hard) — owner invite on company provisioning (staff-sent invites carry `invited_by = null`)

## Core Features

1. **Company management** — list/search all companies (status, users, active modules, MRR contribution); edit locale/timezone/currency/trial; suspend with reason
2. **Company provisioning** — one create flow: company + owner role (all permissions, team-scoped) + free core modules + owner invitation email → see [[features/company-provisioning]]
3. **Module management per company** — activate any active catalog module, deactivate non-free-core, see activation history
4. **Billing overview** — all invoices cross-company, status filters; per-company invoice relation
5. **Platform dashboard** — companies by status, revenue this month (paid invoices), open/past-due balance, MRR estimate (active paid modules × user count), 12-month revenue chart

## Data Model (no own tables)

No new tables. Reads/writes `companies`, `company_module_subscriptions`, `billing_invoices`, `user_invitations` via existing models. Admin requests have **no CompanyContext** → `CompanyScope` no-ops → cross-company queries work natively. Mutating service calls (`activateModule`, `deactivateModule`) require a context — the console sets it per call and forgets it after (`finally`). Tables themselves: see [[../billing-engine/data-model]].

Schema change: `user_invitations.invited_by` becomes **nullable** (staff-provisioned owner invites have no tenant-user sender) — see [[unknowns]].

## DTOs / Services

- `ProvisionCompanyData` — `name, owner_email, timezone, locale, currency`
- `ProvisionCompanyAction` (lorisleiva) — transaction: create company (unique slug), owner role + full permission sync (team = company), `seedFreeCoreModules`, owner `UserInvitation` + mail. Context set + forgotten internally.

## Test Checklist

- [ ] Tenant isolation: `CompanyContext` is set-then-forgotten around console mutations — no leak into subsequent admin queries; a tenant web user can never reach any console page
- [ ] Module gating: n/a (platform staff module, always active — admin-guard only)
- [ ] Non-admin (web user) cannot reach any console page
- [ ] Admin sees companies list incl. cross-company data
- [ ] Provisioning creates company + owner role with all web permissions + free-core subscriptions + pending owner invitation (invited_by null)
- [ ] Module activate via console respects catalog validity; deactivate refuses free-core
- [ ] Revenue widget counts only paid invoices in current month; MRR = Σ(active paid module price × company user count)
- [ ] CompanyContext is forgotten after console mutations (no leak into subsequent admin queries)

## Build Manifest (corrected to flat paths)

```
database/migrations/2026_06_11_224500_make_invited_by_nullable_on_user_invitations.php
app/Data/ProvisionCompanyData.php
app/Actions/ProvisionCompanyAction.php
app/Models/Company.php                      (subscriptions()/invoices() relations)
app/Filament/Admin/Resources/CompanyResource.php
app/Filament/Admin/Resources/CompanyResource/Pages/{ListCompanies,CreateCompany,EditCompany}.php
app/Filament/Admin/Resources/CompanyResource/RelationManagers/{ModulesRelationManager,InvoicesRelationManager,UsersRelationManager}.php
app/Filament/Admin/Resources/BillingInvoiceResource.php
app/Filament/Admin/Resources/BillingInvoiceResource/Pages/ListBillingInvoices.php
app/Filament/Admin/Widgets/{PlatformStatsWidget,RevenueChartWidget,SystemHealthWidget}.php (+ health blade view)
app/Filament/Admin/Resources/AdminResource.php (+ List/Create/Edit pages)
app/Filament/Admin/Resources/UserResource.php (+ ListUsers)
app/Filament/Admin/Resources/ActivityResource.php (+ ListActivities)
app/Filament/Admin/Pages/AdminLogin.php
app/Filament/Admin/Concerns/RunsInCompanyContext.php
config/pulse.php + pulse migrations (viewPulse gate = staff/local)
tests/Feature/StaffConsoleTest.php
```

All Filament resources, pages, relation managers, widgets, `AdminLogin`, and `RunsInCompanyContext` are verified present. The migration and `config/pulse.php` rows are flagged in [[unknowns]].

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| fires | none | — | Staff console fires no domain events of its own *(assumed — `fires-events: none`)* |
| consumes | none | — | Consumes no domain events; drives other domains via **service calls** inside a set-then-forgotten `CompanyContext` |
| calls | `BillingService::seedFreeCoreModules / activateModule / deactivateModule / suspend` | [[../billing-engine/_module]] | provisioning seeds free-core; per-company activate/deactivate; company suspend |
| creates | owner `UserInvitation` (`invited_by = null`) | core.invitations | provisioning invites the company owner; accept flow lives in core.invitations |
| writes-via-owner | spatie roles + permission sync (team = company_id) | [[../rbac/_module]] | provisioning creates the owner role scoped to the new company |

Data ownership: staff-console owns and writes **no tables of its own**. It is a FlowFlex-**staff**, cross-tenant surface: it stands up and manages *other domains'* data (`companies`, `company_module_subscriptions`, `billing_invoices` read-only, `user_invitations`, spatie roles/permissions) **only through those domains' owning models/services (`BillingService`, `ProvisionCompanyAction`, `UserInvitation`) inside a per-call `CompanyContext` that is set and forgotten in `finally`** — never by raw foreign-table writes and never leaking context into later admin queries. This tenancy-boundary discipline is the staff-side equivalent of the events-only rule ([[../../../security/data-ownership]] · [[../../../security/tenancy-isolation]]).

## Related

- [[../billing-engine/_module]] — BillingService contract, invoice states
- core.invitations — owner invite accept flow
- [[../rbac/_module]] — owner role + module-scoped permissions
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[../../../security/data-ownership]] · [[../../../security/tenancy-isolation]]
- [[../../../glossary]]
