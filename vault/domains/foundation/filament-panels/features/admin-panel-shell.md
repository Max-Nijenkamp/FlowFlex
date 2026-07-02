---
domain: foundation
module: filament-panels
feature: admin-panel-shell
type: feature
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Admin Panel Shell (`/admin`)

The FlowFlex-staff console — separate `admin` guard, `Admin` model, no company scope. Where staff create tenants and run the platform.

## Behaviour

- `AdminPanelProvider`: id/path `admin`, `admin` guard, `Admin` model, **no CompanyScope**.
- Skin: primary `Color::Indigo`, gray `Slate` — visually distinct from `/app` so staff never confuse the two.
- Hosts staff tooling (tenant creation, billing oversight, Horizon link) — most staff resources return with [[../../../../domains/core/staff-console/_module|staff-console]].
- The `admin` guard never overlaps `web`: a tenant `User` cannot authenticate here.

## UI

- **Kind**: custom-page (panel shell / container).
- **Page**: `/admin` — staff nav + content.
- **Layout**: Filament sidebar + topbar, Indigo skin.
- **Key interactions**: manage companies, view billing, reach `/horizon`.
- **States**: authenticated staff · rejected (tenant user → 403/redirect).
- **Gating**: `admin` guard; role within (`super_admin`/`support`/`billing`/`developer`).

## Data

- Owns: no tables (hosts `Admin`-model resources). Can bypass CompanyScope for cross-tenant staff views — the only audited place that may.
- Cross-domain writes: only via the owning services, even from admin.

## Relations

- Consumes: `admins` table ([[../../laravel-scaffold/data-model]]); cross-tenant read views.
- Feeds: mount point for staff-console resources.

## Unknowns

> [!warning] UNVERIFIED — which staff resources ship in the stripped-down state (most returned with their
> domains). See [[../unknowns]].

## Related

- [[../_module|Filament Panels]] · [[app-panel-shell]] · [[../../../../domains/core/staff-console/_module]] · [[../../multi-tenancy-layer/security]]
