---
domain: core
module: invitation-system
feature: public-register-vue
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Public Register (Vue + Inertia)

Parent: [[../_module]] · See [[../architecture]]

The only public (guest) registration surface in FlowFlex.

- Route: `/register/invite/{token}` (`routes/web.php`), not behind `auth`.
- `AuthController@showInviteRegistration` loads the invitation with `withoutGlobalScope(CompanyScope::class)`, `firstOrFail()` on token + validity, and renders `resources/js/Pages/Auth/InviteRegister.vue` with `email`, `company_name`, and `token`.
- Per [[../../../architecture/ui-strategy]], this is a Vue + Inertia page because it is a public/guest surface — not a Filament panel page.
- The registration form is rate-limited (`login` limiter) — see [[security]].

## UI

- **Kind**: public-vue
- **Page**: `InviteRegister.vue` (`resources/js/Pages/Auth/InviteRegister.vue`), route `/register/invite/{token}` (`routes/web.php`), guest — not behind `auth`.
- **Layout**: centered Vue + Inertia auth card showing the company name, a read-only email field (carried from the invitation), and editable **name** + **password** fields with a submit button. Invalid/expired token renders a standalone "invitation invalid" page instead of the form.
- **Key interactions**:
  1. Recipient opens the link → `AuthController@showInviteRegistration` loads the invite `withoutGlobalScope(CompanyScope)`, `firstOrFail()` on token + validity, renders the page with `email`, `company_name`, `token`.
  2. Recipient sets name + password → submits `AcceptInvitationData` (token from route, name, password).
  3. Server hands off to `AcceptInvitationAction` (see [[accept-flow]]) → user created, logged in, redirected into the workspace.
- **States**: empty (fresh form, email prefilled) · loading (submit spinner / Inertia progress) · error (validation errors under fields; invalid-token → invalid page) · selected (n/a — single form).
- **Gating**: public/guest surface; access control is token validity + `login` rate limiter, not a permission string.

## Data

- Owns / writes: none directly — this is the presentation surface; the write to `user_invitations.accepted_at` happens in [[accept-flow]].
- Reads: the pending `user_invitations` row (own table, read for the form) to render `email` + `company_name`.
- Cross-domain writes: none — see [[accept-flow]] for the accept-time cross-domain effect ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: none directly — it triggers [[accept-flow]], which feeds `InvitationAccepted` to [[../../rbac/_module|core.rbac]].
- Shared entity: **companies** owned by the platform/tenancy layer (read-only, for the displayed company name).
