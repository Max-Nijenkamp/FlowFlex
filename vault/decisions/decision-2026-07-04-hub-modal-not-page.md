---
type: adr
date: 2026-07-04
status: decided
domain: core
color: "#F97316"
---

# Workspace hub is a sidebar modal, not a page

## Context

`core.hub` spec called for `WorkspaceHubPage` — a gallery-grid custom page (ui-strategy row #17) as the domain launcher, and an earlier decision made it the post-login landing. Built that way in the phase-1 sweep. Owner review 2026-07-04: a whole page for picking a panel is dead weight — "I don't think we need a hub page; we need the hub thing somewhere in the menu and it only opens a modal popup for panel selection." The current workspace must always be visible and marked active, rows need hover borders.

## Decision

- `WorkspaceHubPage` deleted. Dashboard stays the post-login landing (was already, hub never became the landing).
- Switcher = sidebar menu entry pinned above navigation (app panel, `SIDEBAR_NAV_START` render hook) opening a modal: current Workspace row first (accent border + CURRENT tag), then one row per active-module ∩ `access.{domain}` domain, hover borders on every row.
- Row-composition logic lives in `App\Support\Services\WorkspacePanels` (tiles/canView/isOwner) so it stays unit-testable without a page.
- Gate unchanged: `core.hub.view`; empty state unchanged (owner → marketplace CTA, member → "ask your admin").

## Consequences

- One click from anywhere to any workspace; no dead landing page in nav.
- `core.hub` spec's Filament artifact row now reads "sidebar modal (chrome)" — no ui-strategy row applies (it's chrome, not a screen).
- When domain panels ship, the modal is the single place to grow (favourites, recents).

## Related

- [[decision-2026-06-20-workspace-hub-and-login-model]] (superseded on the landing-page half)
- `vault/domains/core/workspace-hub/` spec (artifact row updated)
