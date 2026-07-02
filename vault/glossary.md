---
type: glossary
status: verified
color: "#6B7280"
updated: 2026-06-20
---

# Glossary

Canonical terms — **one name per concept**, used everywhere in the vault. If a note uses a synonym,
fix it to the term here.

## Tenancy & identity

- **Company** — the tenant. Root of all multi-tenant data; everything `BelongsToCompany`. (Not "org", "account", "workspace" at the data layer.)
- **Tenant** — synonym for a Company in prose; prefer **Company** in data/model contexts.
- **User** — a member of a Company (tenant guard `web`). Has `first_name`/`last_name`, unique on `(company_id, email)`.
- **Admin** — FlowFlex **staff** (guard `admin`), operates the `/admin` console. Separate model + table from User.
- **Owner** — the Company role holding every permission; gated for settings + marketplace (see [[decisions/decision-2026-06-11-owner-only-settings-modules]]).
- **CompanyContext** — request/queue-scoped holder of the current Company; sets the Spatie permissions team id. See [[security/tenancy-isolation]].

## Structure

- **Domain** — a business area (HR, Finance, CRM, …). A vault folder + (when built) a panel. 31 defined.
- **Module** — a vertical capability within a domain, keyed `domain.module` (e.g. `hr.leave`). The unit of the catalog + billing.
- **Feature** — a self-contained vertical slice inside a module (data + infra + api + ui + tests). The leaf unit of the rebuilt vault.
- **Panel** — a Filament interface. Today **2 exist**: `/admin` (staff) + `/app` (tenant); plus a shared **Auth** namespace. (Not "21 panels" — that's aspirational.)

## Build state (frontmatter `build-status:`)

- **built** — code exists and is verified against the repo.
- **planned** — spec only; no code. Includes the stripped HR/Finance/CRM rebuild targets.
- **stripped** — historical: was built, then reverted (see [[decisions/decision-2026-06-19-strip-to-app-admin-shell]]). Now folded into `planned`.
- **deferred** — placeholder domain, stub index only.

## Platform concepts

- **Module Catalog** — code-defined list of activatable modules: `ModuleCatalog::FREE_CORE` (16) + `config/flowflex.php` (46 paid). See [[infrastructure/module-catalog]].
- **Switchboard+** — the canonical design system (public site + auth + panel skins). See [[frontend/design-system]].
- **Spotlight** — the ⌘K command palette Livewire component (`app/Livewire/Spotlight.php`). (The in-app notification bell is Filament `databaseNotifications()`, **not** a `NotificationBell` component.)
- **Staff Console** — the `/admin` panel: company provisioning, module mgmt, billing/revenue overview.

## Related

- [[00-index/MOC|Vault MOC]] · [[_meta/spec-template|Spec template]]
