---
domain: core
module: invitation-system
type: decision
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Invitation System — Decisions

Parent: [[_module]]

## No public self-registration

FlowFlex has no open sign-up. The only way a new user joins a company workspace is via an invite sent by an owner/admin and accepted through a single-use token link at `/register/invite/{token}`. This keeps every user bound to exactly one tenant at creation time and avoids the account-orphaning / tenant-ambiguity problems of open registration.

→ [[../../../decisions/decision-2026-06-10-no-public-registration]]
