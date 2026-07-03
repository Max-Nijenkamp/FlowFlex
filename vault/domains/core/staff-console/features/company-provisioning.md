---
domain: core
module: staff-console
feature: company-provisioning
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Company Provisioning

Parent: [[../_module]] · See [[../architecture]] · [[../decisions]]

The one create-flow that stands up a new customer company. Closes the no-public-registration loop ([[../decisions]]).

- Input DTO: `ProvisionCompanyData` — `name, owner_email, timezone, locale, currency`.
- `ProvisionCompanyAction` (lorisleiva) runs a single transaction:
  1. create company with a unique slug
  2. create owner role + full permission sync (team = company)
  3. `seedFreeCoreModules` (via BillingService)
  4. create owner `UserInvitation` (`invited_by = null`) + send invite mail
- CompanyContext is set and forgotten internally (`finally`) so it doesn't leak into later admin queries.
- Triggered from the `CreateCompany` page under `CompanyResource`.

Owner invite accept flow lives in core.invitations.

## UI

- **Kind**: custom-page — the `CreateCompany` Filament resource page in the `/admin` panel, driving a single provisioning action (not a plain CRUD create — it fans out to role sync, module seeding, and an invite).
- **Page**: `CreateCompany` under `CompanyResource` (`/admin` panel, admin guard). Route: Filament resource create route for `CompanyResource`.
- **Layout**: a form capturing `name`, `owner_email`, `timezone`, `locale`, `currency`; a primary "Create" submit that runs `ProvisionCompanyAction` in one transaction.
- **Key interactions**: staff fills the form → submit → `ProvisionCompanyData` built → `ProvisionCompanyAction` transaction (company + unique slug → owner role + full permission sync → `seedFreeCoreModules` → owner `UserInvitation` + invite mail) → redirect to the new company record.
- **States**: empty (blank form) · loading (submit in flight, action transaction) · error (validation e.g. duplicate email / slug collision → inline; transaction rollback on failure) · selected (n/a — create form).
- **Gating**: admin guard only — `canAccess() = auth('admin')->check()`. No tenant permission (this is FlowFlex-staff cross-tenant provisioning).

## Data

- Owns / writes: **no tables of its own.** Staff-console writes *other domains'* tables here, but does so **through the owning services/models**, inside `ProvisionCompanyAction` running in the target company's `CompanyContext`: `companies` (owned by billing/foundation), spatie role + permission tables (owned by [[../../rbac/_module]]), `company_module_subscriptions` (owned by [[../../billing-engine/_module]] via `BillingService::seedFreeCoreModules`), and `user_invitations` (owned by core.invitations). It sets and forgets (`finally`) the `CompanyContext` per call so nothing leaks into later admin queries.
- Reads: — (creation flow; no cross-domain read beyond uniqueness checks on `companies`).
- Cross-domain writes: performed via the **owning modules' service/model layer inside a set-then-forgotten `CompanyContext`**, not by touching foreign tables directly — the tenancy-boundary equivalent of the events rule ([[../../../../security/data-ownership]]).

> [!warning] UNVERIFIED — the `2026_06_11_224500_make_invited_by_nullable_on_user_invitations.php` migration (staff invites carry `invited_by = null`) is a spec claim not confirmed in the migration set — see [[../unknowns]].

## Relations

- Consumes: none (no domain events).
- Feeds: no domain event fired *(assumed — `fires-events: none` on [[../_module]])*. The owner `UserInvitation` it creates is later consumed by **core.invitations** (owner accept flow), and `seedFreeCoreModules` invokes **[[../../billing-engine/_module]]**'s `BillingService`.
- Shared entity: `companies` (foundation/billing-owned), `user_invitations` (core.invitations-owned), spatie roles/permissions ([[../../rbac/_module]]-owned), `company_module_subscriptions` ([[../../billing-engine/_module]]-owned) — all written only via their owners' services within the target `CompanyContext`.

## Test Checklist

### Unit
- [ ] `ProvisionCompanyData` validates presence of `name`, `owner_email`, `timezone`, `locale`, `currency`; slug derives uniquely from name

### Feature (Pest)
- [ ] `ProvisionCompanyAction` in one transaction creates the company + owner role (full permission sync, team = company) + free-core subscriptions + owner `UserInvitation` (`invited_by = null`) and sends the invite mail
- [ ] A failure mid-transaction (e.g. duplicate email) rolls the whole thing back — no orphan company/role/subscription
- [ ] `CompanyContext` is set then forgotten (`finally`) — a subsequent admin query is not scoped to the new company
- [ ] The `panel-action` rate limiter throttles rapid repeat provisioning submits

### Livewire
- [ ] `CreateCompany` form validation rejects a duplicate owner email / slug collision inline
- [ ] `CreateCompany` denied to a non-admin; admin submit redirects to the new company record
