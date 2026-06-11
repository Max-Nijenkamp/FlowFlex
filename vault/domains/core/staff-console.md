---
type: module
domain: Core Platform
domain-key: core
panel: admin
module-key: core.staff-console
status: complete
priority: v1-core
depends-on: ["core.billing", "core.invitations"]
soft-depends: []
fires-events: []
consumes-events: []
patterns: ["actions-pattern", "dto-pattern", "testing"]
tables: []
permission-prefix: admin.console
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Staff Console (core.staff-console)

## Purpose

The FlowFlex-staff side of the platform inside `/admin`: create and manage customer companies, activate/deactivate modules for them, see their subscriptions, invoices and users, and read platform revenue summaries (revenue this month, MRR, open invoices). Closes the loop required by [[build/decisions/decision-2026-06-10-no-public-registration|the no-public-registration ADR]] ("staff create companies in /admin") that no MVP module owned. *(assumed: single staff role — every Admin sees everything; per-admin RBAC deferred until the team grows)*

## Dependencies

- **core.billing** (hard) — BillingService for module activation, free-core seeding, suspension; BillingInvoice for revenue data
- **core.invitations** (hard) — owner invite on company provisioning (staff-sent invites carry `invited_by = null`)

## Core Features

1. **Company management** — list/search all companies (status, users, active modules, MRR contribution); edit locale/timezone/currency/trial; suspend with reason
2. **Company provisioning** — one create flow: company + owner role (all permissions, team-scoped) + free core modules + owner invitation email
3. **Module management per company** — activate any active catalog module, deactivate non-free-core, see activation history
4. **Billing overview** — all invoices cross-company, status filters; per-company invoice relation
5. **Platform dashboard** — companies by status, revenue this month (paid invoices), open/past-due balance, MRR estimate (active paid modules × user count), 12-month revenue chart

## Data Model

No new tables. Reads/writes `companies`, `company_module_subscriptions`, `billing_invoices`, `user_invitations` via existing models. Admin requests have **no CompanyContext** → `CompanyScope` no-ops → cross-company queries work natively. Mutating service calls (`activateModule`, `deactivateModule`) require a context — the console sets it per call and forgets it after (`finally`).

Schema change: `user_invitations.invited_by` becomes **nullable** (staff-provisioned owner invites have no tenant-user sender).

## DTOs

- `ProvisionCompanyData` — name, owner_email, timezone, locale, currency

## Services & Actions

- `ProvisionCompanyAction` (lorisleiva) — transaction: create company (unique slug), owner role + full permission sync (team = company), `seedFreeCoreModules`, owner `UserInvitation` + mail. Context set + forgotten internally.

## Filament

| Artifact | Kind (ui-strategy row) |
|---|---|
| `CompanyResource` (+ List/Create/Edit) | Standard CRUD resource (#1) |
| `CompanyResource` relation managers: Modules, Invoices, Users | Relation tables (#1) |
| `BillingInvoiceResource` | Read-only resource table (#1) |
| `PlatformStatsWidget` | Stats overview widget (#9) |
| `RevenueChartWidget` | Chart widget (#9) |
| `AdminResource` (staff accounts CRUD, self/last-admin delete guard) | Standard CRUD resource (#1) |
| `UserResource` (cross-company directory, read-only) | Read-only resource table (#1) |
| `ActivityResource` (cross-company audit trail, read-only) | Read-only resource table (#1) |
| `SystemHealthWidget` (spatie/laravel-health latest results) | Custom widget (#9) |
| Horizon + Pulse nav links (Monitoring group, staff-gated) | External links |

`canAccess()` on every artifact: `auth('admin')->check()`. The `/admin` panel is already staff-only (guard + IP allowlist in prod); no spatie permissions on the admin guard *(assumed)*.

## Permissions

None (admin guard has no spatie teams; access = being an Admin). Tenant-side permissions untouched.

## Test Checklist

- [ ] Non-admin (web user) cannot reach any console page
- [ ] Admin sees companies list incl. cross-company data
- [ ] Provisioning creates company + owner role with all web permissions + free-core subscriptions + pending owner invitation (invited_by null)
- [ ] Module activate via console respects catalog validity; deactivate refuses free-core
- [ ] Revenue widget counts only paid invoices in current month; MRR = Σ(active paid module price × company user count)
- [ ] CompanyContext is forgotten after console mutations (no leak into subsequent admin queries)

## Build Manifest

```
database/migrations/2026_06_11_224500_make_invited_by_nullable_on_user_invitations.php
app/Data/ProvisionCompanyData.php
app/Actions/ProvisionCompanyAction.php
app/Models/Company.php                      (add subscriptions()/invoices() relations)
app/Filament/Admin/Resources/CompanyResource.php
app/Filament/Admin/Resources/CompanyResource/Pages/{ListCompanies,CreateCompany,EditCompany}.php
app/Filament/Admin/Resources/CompanyResource/RelationManagers/{ModulesRelationManager,InvoicesRelationManager,UsersRelationManager}.php
app/Filament/Admin/Resources/BillingInvoiceResource.php
app/Filament/Admin/Resources/BillingInvoiceResource/Pages/ListBillingInvoices.php
app/Filament/Admin/Widgets/PlatformStatsWidget.php
app/Filament/Admin/Widgets/RevenueChartWidget.php
app/Filament/Admin/Widgets/SystemHealthWidget.php (+ blade view)
app/Filament/Admin/Resources/AdminResource.php (+ List/Create/Edit pages)
app/Filament/Admin/Resources/UserResource.php (+ ListUsers)
app/Filament/Admin/Resources/ActivityResource.php (+ ListActivities)
config/pulse.php + pulse migrations (viewPulse gate = staff/local)
tests/Feature/StaffConsoleTest.php
```

## Related

- [[domains/core/billing-engine]] — BillingService contract, invoice states
- [[domains/core/invitation-system]] — owner invite accept flow
- [[build/decisions/decision-2026-06-10-no-public-registration]]
