---
domain: core
module: invitation-system
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Invitation System

Team-member invitation flow: an owner/admin sends an email invite → the recipient registers via a public token link → the correct role is assigned automatically under the company team. This is the **only** way new users join a company workspace — there is no open self-registration (see [[decisions]]).

## Module-key

`core.invitations`

**Priority:** v1-core  
**Panel:** app  
**Permission prefix:** `core.invitations`  
**Tables:** `user_invitations`  
**Fires events:** `InvitationAccepted` *(assumed — see UNVERIFIED note below)* · consumes none

## Sibling notes

- [[architecture]] — actions, accept flow, mail + flow diagram
- [[data-model]] — `user_invitations` table + ERD
- [[api]] — `CreateInvitationData`, `AcceptInvitationData` DTOs
- [[security]] — single-use token, rate-limited accept, tenant isolation
- [[decisions]] — no public self-registration
- Features: [[features/send-invite]] · [[features/accept-flow]] · [[features/public-register-vue]]

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | foundation.panels | `InvitationResource` in `/app` |
| Hard | foundation.email | invite emails |
| Hard | [[../rbac/_module]] | role selected at invite time, assigned under correct team |

## Core Features

- Owner/admin sends invite: email address + role selection
- Invitation email sent via queue (`notifications` queue) — see [[../../../architecture/email]]
- Invite token: UUID, 7-day expiry, single-use (not hashed — short-lived, single-use)
- Recipient clicks link → registration form pre-filled with email (read-only)
- On registration: user created with `company_id`, role assigned, `user_invitations.accepted_at` set
- Resend invite: invalidates old token, generates a new one, re-queues mail
- Revoke invite: marks as revoked before acceptance
- Invite list: pending invites visible in User Management with expiry countdown

## Test Checklist

- [ ] Tenant isolation: invites of company A invisible to company B
- [ ] Module gating: `InvitationResource` hidden when `core.invitations` inactive
- [ ] Accept flow creates user with correct `company_id` + role under correct team
- [ ] Expired token → 404/invalid page, no acceptance
- [ ] Revoked token unusable; resend invalidates old token
- [ ] Duplicate pending invite for same email rejected with message
- [ ] Invite mail queued on `notifications`, never sent sync
- [ ] Accept route rate-limited

## Build Manifest (corrected to flat paths)

```
database/migrations/xxxx_create_user_invitations_table.php
app/Models/UserInvitation.php
app/Data/{CreateInvitationData,AcceptInvitationData}.php
app/Actions/{SendInvitationAction,ResendInvitationAction,RevokeInvitationAction,AcceptInvitationAction}.php
app/Exceptions/InvalidInvitationTokenException.php
app/Mail/InvitationMail.php
app/Http/Controllers/AuthController.php (showInviteRegistration, acceptInvite)
resources/js/Pages/Auth/InviteRegister.vue
routes/web.php (/register/invite/{token})
app/Filament/App/Resources/InvitationResource.php
database/factories/UserInvitationFactory.php
tests/Feature/Core/{InvitationFlowTest,InvitationSecurityTest}.php
```

> [!note]
> Spec listed `app/Models/Core/...`, `app/Data/Core/...`, `app/Actions/Core/...`, `app/Exceptions/Core/...`, `app/Mail/Core/...`, and `database/factories/Core/...`; real layout is flat — corrected above. `resources/js/...`, `routes/...`, and `tests/Feature/Core/...` kept as-is.

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| fires | `InvitationAccepted` | core.rbac | on accept, rbac's own listener assigns the invited role under the correct team id (`team_id = company_id`) — the role write stays inside rbac |

Data ownership: invitation-system owns and writes only `user_invitations`, reads RBAC roles + `users` + `companies` read-only, and effects other domains (role assignment on accept) only via the `InvitationAccepted` event ([[../../../security/data-ownership]]).

> [!warning] UNVERIFIED
> Frontmatter/prose above still list `fires-events: none`. The full-mapping constitution requires invitation-system to fire `InvitationAccepted` (consumed by core.rbac for role assignment) instead of the inline role write described in [[architecture]]. Event name + payload (`company_id` scalar, `user_id`, `role`) are `*(assumed)*`; core.rbac's `_module.md` frontmatter (`consumes-events: none`) needs a matching update at build. See [[features/accept-flow]].

## Related

- [[../rbac/_module]]
- [[../../../architecture/email]] — invitation email template
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[../../../glossary]]
