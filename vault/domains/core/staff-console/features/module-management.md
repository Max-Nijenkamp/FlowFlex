---
domain: core
module: staff-console
feature: module-management
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Per-Company Module Management

Parent: [[../_module]] · See [[../architecture]] · [[../security]]

Activate any active catalog module for a company, deactivate any non-free-core module, and see the activation history — all from the company record in `/admin`. Core Feature 3. Every mutation runs through [[../../billing-engine/_module]]'s `BillingService` inside a set-then-forgotten `CompanyContext` (`RunsInCompanyContext`), since admin requests carry no context by default.

## UI

- **Kind**: simple-resource — `ModulesRelationManager` (a relation table) under `CompanyResource`, in the `/admin` panel.
- **Page**: `ModulesRelationManager` tab on the `EditCompany` page under `CompanyResource` (`/admin`, admin guard).
- **Layout**: a table of the company's module subscriptions (module, status, activation date), with row actions to Activate (from the active catalog) and Deactivate (blocked for free-core).
- **Key interactions**: open a company → Modules tab → Activate a catalog module (validated against catalog validity) or Deactivate a non-free-core one → `BillingService` call runs inside `RunsInCompanyContext` (context set, then forgotten in `finally`) → activation history updates.
- **States**: empty (no non-core modules yet — free-core seeded rows present) · loading (activate/deactivate service call) · error (deactivate free-core refused; catalog-invalid activation refused → notification) · selected (open company's module table).
- **Gating**: admin guard only — `canAccess() = auth('admin')->check()`. Cross-tenant, staff-only.

## Data

- Owns / writes: no tables of its own. Writes `company_module_subscriptions` **only through** `BillingService::activateModule` / `deactivateModule` — that table is **owned by [[../../billing-engine/_module]]**. The console never writes it directly; it sets the target company's `CompanyContext` per call and forgets it (`finally`).
- Reads: the module catalog (owned by [[../../../infrastructure/module-catalog]]) read-only for validity; the company's subscription rows read-only for the table.
- Cross-domain writes: exclusively via the owning `BillingService` under a scoped-then-cleared `CompanyContext`, never raw foreign-table writes ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none (no domain events).
- Feeds: no domain event of its own *(assumed — `fires-events: none`)*. Delegates to [[../../billing-engine/_module]]'s `BillingService`, whose activation/deactivation may itself fire billing events consumed elsewhere (e.g. RBAC permission suspension on module deactivation — see [[../../rbac/_module]]).
- Shared entity: `company_module_subscriptions` (owned by [[../../billing-engine/_module]]); module catalog (owned by [[../../../infrastructure/module-catalog]]).

## Related

- [[../_module]] · [[../architecture]] · [[../security]] · [[company-management]]
- [[../../billing-engine/_module]] · [[../../../infrastructure/module-catalog]] · [[../../../../security/data-ownership]]
