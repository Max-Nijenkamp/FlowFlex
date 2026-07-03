---
domain: core
module: workspace-hub
type: module
module-key: core.hub
priority: v1-core
build-status: planned
status: wip
depends-on: [core.billing, core.rbac]
soft-depends: []
patterns: [custom-pages, ux-states, perceived-performance]
tables: []
permission-prefix: core.hub
color: "#4ADE80"
updated: 2026-07-03
---

# Workspace Hub

The tenant's **post-login landing** — a domain selector / launchpad. After a workspace user authenticates,
they arrive here and pick which domain to enter before going into it.

> [!important] Blueprint only
> Nothing is built — the app project was removed ([[../../../decisions/decision-2026-06-20-app-project-removed]]).
> This spec + [[../../../decisions/decision-2026-06-20-workspace-hub-and-login-model|the hub/login ADR]] define the target.

## Module-key

`core.hub`

**Priority:** v1-core  
**Panel:** app (tenant default post-login landing)  
**Permission prefix:** `core.hub`  
**Tables:** none of its own — a pure read/compose launcher  
**Events:** fires none · consumes `ModuleActivated` / `ModuleDeactivated` *(assumed, optional — to warm a cached tile list)*

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../billing-engine/_module\|core.billing]] | source of module activation (tile visibility) |
| Hard | [[../rbac/_module\|core.rbac]] | source of `access.<domain>` permissions (tile visibility) |

## Purpose

Replace "log in → dropped into a default panel" with "log in → choose a domain". The hub presents the
domains the company has **activated** (∩ the ones the user is **permitted** to enter) as selectable tiles.
Selecting one enters that domain's workspace. It is the tenant home surface.

## Core Features

- **Domain launcher**: tiles for each accessible domain (icon, name, short descriptor, domain colour). See [[features/domain-launcher]].
- **Access filter**: a tile appears only if the company has the domain's module(s) active
  ([[../../../infrastructure/module-catalog]]) **and** the user holds the domain's panel/access permission
  ([[../rbac/_module]]).
- **Empty / partial states**: no domains active → prompt owner to visit the marketplace ([[../module-marketplace/_module]]); non-owners see a "ask your admin" state ([[../../../architecture/patterns/ux-states]]).
- **Quick resume** *(assumed)*: recently-visited / favourite domains surfaced first.
- Company + user identity chrome (switch account, settings, sign out).

## What it is NOT

- Not a per-domain login (there is none — see [[../../../security/authn-authz]]).
- Not shown to **admin** (staff) users — they go straight to the staff console.

## Entity notes

- [[architecture]] — where the hub lives (panel/route), how it computes the domain list
- [[security]] — access rules (activation ∩ permission), tenant isolation
- [[unknowns]] — `*(assumed)*` items (favourites, single-vs-multi-panel routing)
- [[features/domain-launcher]] — the tile grid + routing

## Build Manifest

```
app/Filament/App/Pages/WorkspaceHubPage.php
resources/views/filament/app/pages/workspace-hub.blade.php
tests/Feature/Core/WorkspaceHubTest.php
```

Owns no migrations/models/tables — a read/compose custom page over billing + rbac reads *(assumed — blueprint only, app project removed)*.

## Test Checklist

- [ ] Tenant isolation: activation + permission lookups run under the current company context; a user never sees another company's domains
- [ ] Module gating: n/a (platform module, always active — tiles are individually gated by activation ∩ `access.<domain>`)
- [ ] A tile renders only when the company has the domain's module active AND the user holds `access.<domain>`
- [ ] Admin (staff) users never reach the hub — they land on the staff console
- [ ] No active domains → owner sees the marketplace CTA; non-owner sees the "ask your admin" state
- [ ] No enumeration: a forbidden domain is simply absent (not shown-but-disabled)

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| consumes | `ModuleActivated` / `ModuleDeactivated` *(assumed, optional)* | core.billing | optionally refresh a cached tile list; the hub can also compute activation synchronously per request |

Data ownership: workspace-hub owns and writes **no tables** (`tables: []`); it reads module activation (`company_module_subscriptions` / `ModuleCatalog`, owned by [[../billing-engine/_module|core.billing]]) and access permissions (owned by [[../rbac/_module|core.rbac]]) read-only under the current company context, and never mutates another domain's data ([[../../../security/data-ownership]]).

## Related

- [[../../../decisions/decision-2026-06-20-workspace-hub-and-login-model]]
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[../module-marketplace/_module]] · [[../rbac/_module]] · [[../../../infrastructure/module-catalog]]
- [[../../../security/authn-authz]] · [[../../../frontend/_index]] · [[../../../architecture/ui-strategy]] · [[../../../glossary]]
