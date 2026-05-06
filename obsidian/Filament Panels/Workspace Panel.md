---
tags: [flowflex, filament, workspace-panel]
domain: Platform
panel: workspace
status: built
last_updated: 2026-05-06
---

# Workspace Panel

The tenant settings and configuration panel. Where workspace admins configure everything.

**Panel ID:** `workspace`
**URL:** `/app/settings`
**Access:** Workspace owners and admins (role: Owner or Admin)

## What Lives Here

- **Workspace settings** — name, subdomain, custom domain, timezone, locale, currency
- **Branding** — logo upload, brand colour, email sender
- **Module management** — toggle modules on/off, module marketplace
- **Billing** — plan management, invoice history, upgrade/downgrade
- **User management** — invite users, manage roles, set permissions
- **RBAC** — role builder, permission matrix
- **API keys** — create and manage API keys
- **Webhooks** — configure outbound webhooks
- **Native integrations** — connect Google Workspace, Xero, QuickBooks, etc.
- **Notification settings** — workspace-level defaults

## Modules Managed Here

Every module that gets activated/deactivated goes through this panel. This is the module marketplace for the tenant.

## Related

- [[Panel Map]]
- [[Module Billing Engine]]
- [[Roles & Permissions (RBAC)]]
- [[Multi-Tenancy & Workspace]]
- [[API & Integrations Layer]]
- [[Notifications & Alerts]]
