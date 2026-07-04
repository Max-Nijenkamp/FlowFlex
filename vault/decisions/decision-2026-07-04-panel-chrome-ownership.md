---
type: adr
date: 2026-07-04
status: decided
domain: All
color: "#F97316"
---

# Panel chrome ownership — minimal topbar, sidebar-owned account & navigation controls

## Context

The first implementation of the Switchboard+ panel chrome followed the design handoff (`panel/panel.jsx`) literally: topbar carried a panel crumb, sidebar collapse chevrons, the native Filament logo, the user-menu avatar dropdown, plus the search trigger and bell. During 2026-07-04 build reviews the owner found the topbar duplicated content that already lived (or belonged) in the sidebar, and the crumb duplicated page headings.

## Options Considered

1. Keep handoff-literal topbar (crumb + avatar + toggles + search + bell).
2. Minimal topbar: search + bell only; everything identity/navigation-related lives in the sidebar; breadcrumbs render on the pages themselves.

## Decision

Option 2, decided by the owner across four review rounds:

- **Topbar carries ONLY the ⌘K/Ctrl+K search trigger and the 34px bordered bell.** No crumb, no logo, no avatar, no collapse buttons.
- **Breadcrumbs are a page concern**: Filament's native page-header breadcrumbs render (styled faint 13px); the `TopbarCrumb`/`TOPBAR_START` hook was removed.
- **The sidebar user card IS the account menu**: upward Alpine popover with theme switcher (light/dark/system), Profile, Sign out. The topbar `.fi-user-menu` is hidden.
- **Sidebar collapse/expand lives in the sidebar header**, pinned to the right edge beside the brand; on mobile (<1024px) the same button renders a plain X because the sidebar is an overlay and the vendor topbar close button hides underneath it.
- **Profile page saves per section** (Profile / Password as independent cards with own footer actions, no global save), with enforced password policy (min 12 + mixed case + number + symbol + current-password check) and a live requirements checklist.

## Consequences

- Deviates from handoff §12 (which showed a topbar crumb + avatar); `frontend/design-system.md` §12 and `architecture/patterns/filament-panel-chrome.md` are the corrected sources of truth.
- Every future domain panel inherits this automatically (skin CSS + shared chrome views + provider hooks); no per-panel work.
- `PanelSwitchItems`-style duplication is structurally prevented: one account surface, one switcher surface (sidebar chips), one crumb surface (page header).

## Related

[[../architecture/patterns/filament-panel-chrome|filament-panel-chrome]] · [[../frontend/design-system|design-system]] · [[decision-2026-06-12-switchboard-plus-design-system]]
