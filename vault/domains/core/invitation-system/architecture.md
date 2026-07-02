---
domain: core
module: invitation-system
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Invitation System — Architecture

Parent: [[_module]] · See also [[api]] · [[data-model]]

## Actions

All four are `lorisleiva/laravel-actions` single-class actions.

| Action | Signature | Behavior |
|---|---|---|
| `SendInvitationAction` | `run(CreateInvitationData $data): UserInvitation` | creates row (token, 7-day expiry), queues `InvitationMail` on `notifications` |
| `ResendInvitationAction` | `run(string $invitationId): UserInvitation` | rotates token, invalidates old, re-queues mail |
| `RevokeInvitationAction` | `run(string $invitationId): void` | sets `revoked_at` (only before acceptance) |
| `AcceptInvitationAction` | `run(AcceptInvitationData $data): User` | creates user, assigns role under correct team id, sets `accepted_at`, logs user in; throws `InvalidInvitationTokenException` |

## Mail

`InvitationMail` — queued mailable on the `notifications` queue, links to `/register/invite/{token}`. See [[../../../architecture/email]] and [[../../../infrastructure/mail]].

## Accept flow (public route)

The only public registration surface is `/register/invite/{token}` — not behind `auth`. The controller loads the invitation with the company scope removed, validates it is unexpired/unaccepted/unrevoked, and renders the Inertia page. Registration submits `name` + `password` only; email is pre-filled and read-only.

```mermaid
flowchart TD
    Owner[Owner/Admin] -->|CreateInvitationData| Send[SendInvitationAction]
    Send --> Row[(user_invitations)]
    Send --> Mail[InvitationMail queued]
    Mail --> Link["/register/invite/{token}"]
    Link --> Ctrl[AuthController@showInviteRegistration]
    Ctrl -->|withoutGlobalScope CompanyScope| Valid{valid + unexpired + unaccepted + unrevoked?}
    Valid -->|no| Invalid[404 / invalid page]
    Valid -->|yes| Vue[InviteRegister.vue]
    Vue -->|AcceptInvitationData| Accept[AcceptInvitationAction]
    Accept --> User[(users w/ company_id + role)]
    Accept --> AcceptedAt[user_invitations.accepted_at set]
    Accept --> Login[user logged in]
```

## Exceptions

`InvalidInvitationTokenException` — thrown by `AcceptInvitationAction` when the token is expired, already accepted, or revoked.
