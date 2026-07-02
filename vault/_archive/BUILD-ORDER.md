---
type: build-order
last-updated: 2026-06-10
color: "#F97316"
---

# Build Order

The exact sequence to build FlowFlex from zero. Follow top to bottom. Each step: run `/flowflex:start {module-key}` first, build, test, then `/flowflex:sync {module-key} status=complete`.

---

## Phase 0 — Foundation (build first, in this order)

Nothing else works until Foundation is done. No module gating or panels exist yet.

1. **`foundation.scaffold`** — Laravel project + install manifest + directory structure + ULID/SoftDeletes conventions
2. **`foundation.docker`** — Docker Compose stack (postgres, redis, meilisearch, mailpit, horizon, reverb)
3. **`foundation.tenancy`** — `CompanyContext`, `CompanyScope`, `BelongsToCompany`, `SetCompanyContext` + `WithCompanyContext` middleware
4. **`foundation.queues`** — Horizon config, named queues, scheduler
5. **`foundation.email`** — `FlowFlexMailable` base, Resend/Mailpit config
6. **`foundation.panels`** — `/admin` + `/app` PanelProviders, guards, middleware order
7. **`foundation.permissions`** — PermissionSeeder, ModuleCatalogSeeder, LocalDevSeeder
8. **`foundation.tests`** — Pest setup, SQLite in-memory, factory + CompanyContext helpers, first tenant-isolation test

**Gate before proceeding**: `php artisan migrate --seed` runs clean, demo company + owner login works, one passing tenant-isolation test.

---

## Phase 1 — MVP (paying-customer-ready)

Build domains in this order. Within a domain, build the **core** modules first.

### Core Platform (`/app`) — build before any business domain
Order: `core.settings` → `core.rbac` → `core.invitations` → `core.billing` → `core.marketplace` → `core.audit` → `core.notifications` → `core.files` → then the rest (`core.import`, `core.webhooks`, `core.api`, `core.setup`, `core.privacy`, `core.i18n`, `core.health`).

**Why this order**: settings + RBAC + invitations make a usable workspace; billing + marketplace make module gating real (everything downstream depends on `BillingService::hasModule()`); audit + notifications are consumed by every later domain.

### HR & People (`/hr`)
Order: `hr.profiles` (anchor — everything references employees) → `hr.org` → `hr.self-service` → `hr.leave` → `hr.onboarding` → `hr.payroll` → then Phase 2/3 HR modules as needed.

### Finance & Accounting (`/finance`)
Order: `finance.ledger` (anchor — invoices/expenses post here) → `finance.invoicing` → `finance.expenses` → `finance.bank` → then AR/AP/budgets/reporting/tax.

### CRM & Sales (`/crm`)
Order: `crm.contacts` (anchor) → `crm.deals` → `crm.pipeline` → `crm.activities` → `crm.quotes` → then the rest. Customer Success modules (`cs.*`) build last in this panel.

**MVP gate**: a company can be onboarded (staff-created in `/admin`, owner invited, setup wizard completed), activate HR + Finance + CRM modules, manage employees, send invoices, run a sales pipeline. This is sellable.

---

## Phase 2

Build full domains once MVP is live and validated:
`projects` → `support` → `communications` → `dms`

Within each, core modules first (marked **P2 core** in the domain `_index`).

---

## Phase 3

Build on demand / by customer pull:
`marketing`, `operations` (+ `procurement` in same panel), `analytics`, `it`, `legal`, `ecommerce`, `lms`, `ai`, `workplace`, `events`

(Customer Success already built inside `/crm`; Procurement builds inside `/operations`.)

---

## Per-Module Build Loop

```
/flowflex:start {module-key}      # read spec + relevant patterns + open gaps
# 1. Migration (BelongsToCompany columns, indexes)
# 2. Model (HasUlids, BelongsToCompany, SoftDeletes; states if status field)
# 3. Factory + states
# 4. DTOs (input + output)
# 5. Service (Interface→Service) OR Action (single-step)
# 6. Events + listeners (if cross-domain)
# 7. Filament resource (CRUD) or custom page; canAccess() with permission + module check
# 8. Permissions added to PermissionSeeder
# 9. Tests (feature + tenant-isolation + module-gating)
/flowflex:sync {module-key} status=complete
```

---

## Cross-Domain Build Dependencies

Build the source before the consumer where a hard dependency exists:

| Consumer needs | Build first |
|---|---|
| Any business module | `core.billing` (module gating) |
| HR payroll → GL entry | `finance.ledger` |
| CRM deal → invoice | `finance.invoicing` |
| Operations PO → bill | `finance.accounts-payable` |
| LMS auto-enrol on hire | `hr.profiles` + event bus |
| IT provisioning on hire | `hr.profiles` + event bus |
| CS health scores | `crm.contacts` + `support.tickets` |
| Procurement requisition → PO | `operations.purchase-orders` |

Events decouple most of these — build the emitter, stub the listener, wire the consumer when its domain is built.

---

## Related

- [[00-index/status-board]] — progress tracking
- [[domains/foundation/laravel-scaffold/_module]] — install manifest
- [[architecture/patterns/seeders]] — seeding order
- [[architecture/event-bus]] — cross-domain event contracts
