---
domain: core
module: staff-console
feature: company-management
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Company Management

Parent: [[../_module]] · See [[../architecture]] · [[../security]]

List, search, and edit all customer companies from `/admin` — status, user count, active modules, MRR contribution — plus edit a company's locale/timezone/currency/trial and suspend it with a reason. Core Feature 1 of the staff console. Cross-company by design: admin requests carry no `CompanyContext`, so `CompanyScope` no-ops and the list spans every tenant.

## UI

- **Kind**: simple-resource — `CompanyResource` (List + Edit) in the `/admin` panel, standard Filament CRUD over the `companies` table.
- **Page**: `ListCompanies` and `EditCompany` under `CompanyResource` (`/admin` panel, admin guard). Routes: Filament resource index/edit routes for `CompanyResource`.
- **Layout**: a searchable/filterable table (status, users, active modules, MRR contribution columns); an edit form for locale/timezone/currency/trial; a "Suspend" action taking a reason.
- **Key interactions**: search/filter companies → open a row → edit settings or trigger suspend-with-reason → save. Relation-manager tabs (Modules / Invoices / Users) hang off the record — see [[module-management]], [[billing-overview]].
- **States**: empty (no companies yet — pre-first-provisioning) · loading (table query) · error (query/save failure → Filament notification) · selected (open company record with relation-manager tabs).
- **Gating**: admin guard only — `canAccess() = auth('admin')->check()`. Cross-tenant visibility is intentional and staff-only; no tenant user ever reaches this.

## Data

- Owns / writes: no tables of its own. Edits the `companies` table (locale/timezone/currency/trial, suspension) — a table **owned by the foundation/billing layer**, mutated here through the `Company` model. Suspension may call `BillingService` (suspend), owned by [[../../billing-engine/_module]].
- Reads: `companies` cross-company (native, since `CompanyScope` no-ops for admin); user counts and active-module counts read read-only from their owning modules' tables.
- Cross-domain writes: performed through the owning models/services, not by raw foreign-table writes ([[../../../../security/data-ownership]]). Suspension effects (module suspend) go through `BillingService`.

## Relations

- Consumes: none (no domain events).
- Feeds: no domain event *(assumed — `fires-events: none`)*. A suspend action invokes [[../../billing-engine/_module]]'s `BillingService`.
- Shared entity: `companies` — the shared tenant root, owned by the foundation/billing layer; staff-console edits it read/write via the `Company` model but owns none of it.

## Related

- [[../_module]] · [[../architecture]] · [[../security]] · [[module-management]] · [[billing-overview]]
- [[../../billing-engine/_module]] · [[../../../../security/data-ownership]] · [[../../../../security/tenancy-isolation]]
