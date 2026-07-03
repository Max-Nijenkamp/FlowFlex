---
domain: core
module: invitation-system
type: architecture
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-07-03
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

## Filament Artifacts

**Nav group:** User Management *(assumed)*

| Artifact | Kind ([[../../../architecture/ui-strategy]] row) | Blueprint / Tweaks | Notes |
|---|---|---|---|
| `InvitationResource` (/app) | #1 CRUD resource | tweaks: state-badge-column (pending / accepted / revoked / expired), custom-header-actions (resend, revoke) | list = pending invites with an expiry-countdown column; create form = email + role ([[./features/send-invite]]) |

**Access contract (mandatory):** the resource and its actions gate on
`canAccess() = Auth::user()->can('core.invitations.view-any') && BillingService::hasModule('core.invitations')`
per [[../../../architecture/filament-patterns]] #1. The **resend** and **revoke** header actions each carry their own permission (`core.invitations.resend` / `.revoke`) and the `panel-action` rate limiter (create + resend send invite emails — a comms action). The public **accept** surface `/register/invite/{token}` is **Vue + Inertia** (ui-strategy row #13, guest guard), not a Filament artifact — it uses single-use token semantics (UUID, 7-day expiry, `withoutGlobalScope`) + the `login` rate limiter ([[security]], [[./features/public-register-vue]]).

## Concurrency

| Write path | Tier | Mechanism |
|---|---|---|
| Invite create (`user_invitations` row + queue mail) | n/a | Insert-once — a duplicate pending invite for the same email is rejected by a uniqueness guard; no concurrent-edit surface |
| Invite accept (validate token → create user, set `accepted_at`) | Pessimistic | `DB::transaction()` + `lockForUpdate()` on the invitation row: re-read, validate unexpired/unaccepted/unrevoked, create user + assign role, set `accepted_at` atomically — prevents double-accept and accept-after-revoke races ([[../../../architecture/patterns/states]]) |
| Resend (rotate token) / Revoke (set `revoked_at`) | Pessimistic | `lockForUpdate()` on the invite row — guard against mutating an already-accepted invite before rotating/revoking |

Tiers per [[../../../decisions/decision-2026-07-02-optimistic-locking-standard]].
