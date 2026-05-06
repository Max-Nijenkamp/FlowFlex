---
tags: [flowflex, filament, admin-panel, super-admin]
domain: Platform
panel: admin
status: built
last_updated: 2026-05-06
---

# Admin Panel

The FlowFlex platform super-admin panel. Only accessible to FlowFlex staff. Never visible to tenants.

**Panel ID:** `admin`
**URL:** `/admin`
**Access:** FlowFlex staff only (internal `is_platform_admin` flag, not tenant-based)

## What Lives Here

- **Tenant management** — create, view, suspend, delete tenant workspaces
- **Platform user management** — FlowFlex staff accounts
- **Module registry** — manage available modules, update pricing
- **Platform billing** — Stripe subscription overview across all tenants
- **Impersonation** — enter any tenant workspace as any user (for support)
- **Platform metrics** — total tenants, MRR, active modules, usage trends
- **Audit log** — all platform-level actions

## Security

- Requires `is_platform_admin = true` on the user record
- Separate auth guard from tenant panels
- All impersonation sessions are logged
- 2FA enforced for all admin users
- IP allowlist for admin access (configurable)

## Related

- [[Panel Map]]
- [[Authentication & Identity]]
- [[Multi-Tenancy & Workspace]]
- [[Audit Log & Activity Trail]]
