---
type: adr
date: 2026-06-20
status: decided
domain: Core
color: "#F97316"
updated: 2026-06-20
---

# Workspace hub (domain selector) + two-login model

## Context

Two product decisions for the tenant experience:

1. On login, tenant users previously landed directly in a default panel/dashboard. The founder wants a
   **hub / launchpad** first — a domain selector — so the user chooses which domain to enter before going
   into one.
2. The set of ways to authenticate must be pinned down: only a **workspace login** (for tenant users,
   reachable directly or via the public website) and the **admin login** (internal FlowFlex staff). No
   other entry points.

## Decision

### Workspace hub

After a tenant user authenticates, they land on the **Workspace Hub** — a launcher that shows the domains
their company has activated **and** they have permission to enter, as selectable tiles. Selecting a domain
enters that domain's workspace. The hub is the tenant's home surface (replaces a default-panel landing).
Specced as [[../domains/core/workspace-hub/_module|core.workspace-hub]].

### Login model (exactly two)

| Login | Guard / identity | Who | Entry points | Lands on |
|---|---|---|---|---|
| **Workspace login** | `web` / `User` | Tenant company members | (a) the workspace login page directly, **and** (b) the public website front-end (Inertia + Vue) "Log in" → same auth | **Workspace Hub** |
| **Admin login** | `admin` / `Admin` | Internal FlowFlex staff only | `/admin` login (unchanged) | Staff console (`/admin`) |

- Both tenant entry points resolve to the **same** `web`-guard session — the public site's "Log in" is a
  funnel to workspace auth, not a separate mechanism.
- No per-domain logins; no public self-registration (consistent with
  [[decision-2026-06-10-no-public-registration]]); admin is fully separate (staff-only) and does **not**
  see the hub.

## Consequences

- The hub sits between authentication and any domain — a platform-level surface owned by core, driven by
  module activation ([[../infrastructure/module-catalog]]) ∩ the user's permissions.
- Domains remain distinct destinations behind the hub; the hub is the router/launcher.
- Auth model documented in [[../security/authn-authz]]; public-site funnel in [[../frontend/_index]].

## Related

- [[../domains/core/workspace-hub/_module]] · [[../security/authn-authz]] · [[decision-2026-06-10-no-public-registration]]
- [[decision-2026-06-19-strip-to-app-admin-shell]] · [[../architecture/ui-strategy]]
