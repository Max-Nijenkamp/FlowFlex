---
domain: core
module: invitation-system
feature: accept-flow
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Accept Flow

Parent: [[../_module]] · See [[../architecture]]

Recipient accepts an invite and becomes a company user.

- `AcceptInvitationAction::run(AcceptInvitationData $data)` validates the token (unexpired, unaccepted, unrevoked) or throws `InvalidInvitationTokenException`.
- Creates the `User` with the invitation's `company_id`, assigns the invited role **under the correct team id** (`company_id` = Spatie team), sets `user_invitations.accepted_at`, and logs the user in.
- Registration collects `name` + `password` only; email is carried from the invitation and is read-only.
- Expired/revoked/already-accepted tokens render the invalid page — no user is created.

## UI

- **Kind**: background
- **Trigger**: `AcceptInvitationAction::run(AcceptInvitationData)` invoked by the public register form POST (see [[public-register-vue]]); this feature is the server-side accept logic, not a screen of its own. Its only rendered surfaces are the `InviteRegister.vue` form and the "invalid invitation" page, both owned by [[public-register-vue]].
- **Gating**: public/guest (no `auth`); protection is token validity + `login` rate limiter (see [[../security]]).

## Data

- Owns / writes: `user_invitations` only — sets `accepted_at` on success.
- Reads: the pending invitation (loaded with `withoutGlobalScope(CompanyScope)` by the controller) and RBAC roles read-only (to resolve the invited role name under the company team).
- Cross-domain writes: creating the `User` and assigning the invited role are **not** this module's tables. Per [[../../../../security/data-ownership]] the invited role assignment is a cross-domain effect that must flow via a domain event to core.rbac, not a direct write into `model_has_roles`.

## Relations

- Consumes: none.
- Feeds: `InvitationAccepted` → consumed by [[../../rbac/_module|core.rbac]] to assign the invited role under the correct team id (`team_id = company_id`).

> [!warning] UNVERIFIED
> The existing note describes `AcceptInvitationAction` assigning the role **inline** (a direct write outside this module's tables). The constitution ([[../../../../decisions/decision-2026-06-20-full-mapping-conventions]] + [[../../../../security/data-ownership]]) requires that cross-domain write to happen via an `InvitationAccepted` event consumed by core.rbac. Event name `InvitationAccepted`, its payload (`company_id` scalar, `user_id`, `role`), and the split of "create user" vs "assign role" across the boundary are `*(assumed)*` — confirm at build. core.rbac's `_module.md` frontmatter currently reads `consumes-events: none`; it needs updating to consume this event.

- Shared entity: **roles** and **users** owned by [[../../rbac/_module|core.rbac]] / platform (read-only here; written only through the accept event / owning service).

## Test Checklist

### Unit
- [ ] Token validation rejects an expired / accepted / revoked token → `InvalidInvitationTokenException`

### Feature (Pest)
- [ ] Accept creates the user with the correct `company_id` + role under the correct team, sets `accepted_at`, and logs in
- [ ] An expired / revoked / already-accepted token creates no user (invalid page)
- [ ] Double-accept race prevented (pessimistic `lockForUpdate`) — only one user is created
- [ ] Accept fires `InvitationAccepted`, consumed by core.rbac for the role assignment *(assumed event — see UNVERIFIED note)*
