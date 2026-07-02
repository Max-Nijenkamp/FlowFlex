---
domain: core
module: staff-console
type: decision
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Staff Console — Decisions

Parent: [[_module]]

## No public registration

There is no public sign-up. FlowFlex staff create customer companies from `/admin` (the company-provisioning flow), which sends the owner an invitation. Staff console exists to own this loop, which no other MVP module did.

→ [[../../../decisions/decision-2026-06-10-no-public-registration]]

## Single staff role *(assumed)*

Every `Admin` sees everything; per-admin RBAC on the admin guard is deferred until the team grows.
