---
type: gap
severity: medium
category: feature
status: open
domain: core
color: "#F97316"
discovered: 2026-07-03
discovered-in: core.invitations
---

# Gap: Invitations has no bulk / CSV invite path

## Context

[[../../domains/core/invitation-system/features/send-invite|send-invite]] creates invitations
**one at a time**: an owner/admin opens a modal (`CreateInvitationData`: email + role), and
`SendInvitationAction` writes a single `user_invitations` row. There is no way to invite many people at
once. `core.data-import`'s ImporterRegistry is the platform-wide bulk mechanism (importers registered by
`hr.employees`, `crm.contacts`, products) — invitations does not register one.

## Problem

FlowFlex targets 50–500-employee companies. Onboarding a whole company, or migrating off another tool,
means adding one row per person through a modal — dozens to hundreds of manual submits. Admins expect to
upload a spreadsheet of `email, role` and have all invites sent (with per-row validation and duplicate
detection) in one pass.

## Impact

Slows first-value for every new tenant and makes the "bring your whole team" onboarding motion painful —
directly at odds with [[../../domains/core/_opportunities|core opportunity #8]] (guided activation /
time-to-first-value). Package-fit — no new dependency.

## Proposed Solution

Register a `core.invitations` importer with `core.data-import` using `maatwebsite/laravel-excel`: map CSV
columns to `CreateInvitationData` per row, reuse `SendInvitationAction` for each valid row (same UUID-token +
7-day-expiry + `InvitationMail` on the `notifications` queue path), surface a "N ready, M errors" preview
before dispatch, and reject duplicates / invalid roles per row rather than aborting the whole file.

## Sources

- [Import users/subscribers via CSV — standard onboarding expectation (Mailchimp Help)](https://mailchimp.com/help/import-subscribers-to-a-list/) (accessed 2026-07-03)
- [RBAC billing/role assignment patterns for SaaS admin onboarding (enterpriseready.io)](https://www.enterpriseready.io/features/role-based-access-control/) (accessed 2026-07-03)
