---
domain: core
module: staff-console
type: unknown
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Staff Console — Unknowns / UNVERIFIED

Parent: [[_module]]

## Build-Manifest items not confirmed against code

> [!warning] UNVERIFIED — needs confirmation
> - `database/migrations/2026_06_11_224500_make_invited_by_nullable_on_user_invitations.php` — the `UserInvitation` model is confirmed present, but this specific migration file (making `invited_by` nullable) was not verified in the migration set. The schema claim (staff invites carry `invited_by = null`) comes from the spec.
> - `config/pulse.php` + pulse migrations (viewPulse gate) — framework/config-level, not app domain code; not verified here.

## `*(assumed)*` markers carried from spec

- Single staff role — every Admin sees everything; per-admin RBAC deferred *(assumed)*.
- No spatie permissions on the admin guard; access = being an Admin *(assumed)*.
