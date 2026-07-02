---
domain: foundation
module: filament-panels
feature: app-panel-shell
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# App Panel Shell (`/app`)

The tenant-facing Filament 5 shell every domain's resources plug into — Switchboard+ skin, sky primary, company-scoped, subscription-gated.

## Behaviour

- `AppPanelProvider`: id/path `app`, `web` guard, `users` broker, CompanyScope active.
- Skin: brand "FlowFlex", primary `#38BDF8`, gray `Slate`, font Instrument Sans, `System` theme, collapsible-on-desktop sidebar, custom `viteTheme` ([[../../../../frontend/design-system]]).
- Auth middleware chain (persistent): Authenticate → SetCompanyContext → SetLocale → EnsureSubscriptionActive → RedirectToSetupWizard.
- Database notifications, 30s polling.
- Domain resources/pages register into this panel as their domains rebuild (21-panel target).

## UI

- **Kind**: custom-page (the panel shell itself — the container, not a single resource).
- **Page**: `/app` — sidebar nav + topbar + content region.
- **Layout**: full-height collapsible sidebar (Switchboard+ chrome, [[../../../../architecture/patterns/filament-panel-chrome]]); topbar with spotlight, notifications, user menu.
- **Key interactions**: navigate resources; global search/spotlight; notification bell (polling).
- **States**: authenticated (nav visible) · unauthenticated (→ login) · suspended (blocked by `EnsureSubscriptionActive`) · setup-incomplete (→ wizard).
- **Gating**: `web` guard + per-resource `canAccess()`.

## Data

- Owns: no tables. Hosts every domain's resources; reads company branding for the skin.
- Cross-domain writes: none (it is a container).

## Relations

- Consumes: RBAC permissions (nav visibility), company branding, subscription status.
- Feeds: the mount point for all `/app` domain UIs.

## Unknowns

> [!warning] UNVERIFIED — subscription-gate behaviour per status; relation-manager tenant scoping. See [[../unknowns]].

## Related

- [[../_module|Filament Panels]] · [[admin-panel-shell]] · [[panel-auth]] · [[../../../../frontend/design-system]] · [[../../../../architecture/patterns/filament-panel-chrome]]
