---
tags: [flowflex, domain/it, access, permissions-audit, phase/5]
domain: IT & Security Management
panel: it
color: "#475569"
status: planned
last_updated: 2026-05-06
---

# Access & Permissions Audit

Know exactly who has access to what, and fix overprovision before it becomes a risk.

**Who uses it:** IT team, security team
**Filament Panel:** `it`
**Depends on:** [[Roles & Permissions (RBAC)]]
**Phase:** 5
**Build complexity:** Medium — 2 resources, 2 pages, 3 tables

## Events Consumed

- `OffboardingCompleted` (from [[Offboarding]]) → auto-revoke all platform access

## Database Tables (3)

1. `access_reviews` — scheduled access review cycles
2. `access_review_items` — individual access items under review (user × permission)
3. `access_revocations` — log of revoked access records with reason

## Features

- **Cross-system access map** — who has access to what, in which systems
- **Overprovision alerts** — users with more access than their role requires
- **Periodic access review cycles** — formal access review process
- **Auto-revoke on offboarding** — fires from HR [[Offboarding]] event
- **Principle of least privilege enforcement** — recommendations engine

## Related

- [[IT Overview]]
- [[Roles & Permissions (RBAC)]]
- [[Offboarding]]
- [[Security & Compliance]]
