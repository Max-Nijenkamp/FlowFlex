---
domain: core
module: invitation-system
feature: send-invite
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Send Invite

Parent: [[../_module]] · See [[../architecture]]

Owner/admin invites a team member from `InvitationResource` in `/app`.

- Input: email + role (`CreateInvitationData`) — see [[../api]].
- `SendInvitationAction` creates a `user_invitations` row with a UUID token and 7-day expiry, then queues `InvitationMail` on the `notifications` queue.
- Duplicate pending invite for the same email in the same company is rejected: "This email already has a pending invitation."
- Pending invites list in User Management shows an expiry countdown with resend + revoke actions.
- `ResendInvitationAction` rotates the token (invalidating the old link) and re-queues the mail; `RevokeInvitationAction` closes the invite before acceptance.

## UI

- **Kind**: simple-resource
- **Page**: `InvitationResource` (`/app/invitations`) — pending-invites list + create/resend/revoke actions, surfaced inside User Management.
- **Layout**: Filament table of pending invites (email, role, invited-by, expiry countdown, status). Header "Invite" create action opens a modal form (email + role select).
- **Key interactions**:
  1. Owner/admin clicks **Invite** → modal form (`CreateInvitationData`: email + role).
  2. Submit → `SendInvitationAction` writes a `user_invitations` row (UUID token, 7-day expiry) and queues `InvitationMail` on `notifications`.
  3. Row actions **Resend** (`ResendInvitationAction`, rotates token) and **Revoke** (`RevokeInvitationAction`, before acceptance) per row.
  4. Duplicate pending invite for the same email in the same company is rejected inline: "This email already has a pending invitation."
- **States**: empty (no pending invites → "No invitations yet" prompt) · loading (table skeleton) · error (validation message on duplicate/invalid role) · selected (row action menu open on a pending invite).
- **Gating**: `core.invitations.view-any` (list) · `core.invitations.create` · `core.invitations.resend` · `core.invitations.revoke`; `canAccess()` also requires `BillingService::hasModule('core.invitations')`.

## Data

- Owns / writes: `user_invitations` only (token, role, expiry, `accepted_at`/`revoked_at`).
- Reads: RBAC roles read-only (role select must list roles that exist for this company team) and `users` read-only (to reject an email that is already a user in this company).
- Cross-domain writes: none from this feature — role assignment happens only on accept, via event ([[../../../../security/data-ownership]]).

## Relations

- Consumes: none.
- Feeds: none at send time — a pending invite fires no cross-domain effect until it is accepted (see [[accept-flow]]).
- Shared entity: **roles** owned by [[../../rbac/_module|core.rbac]] (read-only for the role picker); **users** owned by the platform/RBAC layer (read-only for duplicate detection).

## Test Checklist

### Unit
- [ ] `CreateInvitationData` validates email + role; a duplicate pending invite for the same email/company is rejected

### Feature (Pest)
- [ ] Tenant isolation: invites of company A invisible to company B
- [ ] `SendInvitationAction` creates a row with a UUID token + 7-day expiry and queues `InvitationMail` on `notifications` (never sent synchronously)
- [ ] `ResendInvitationAction` rotates the token, invalidating the old link (pessimistic row lock)

### Livewire
- [ ] Resend/Revoke actions require `core.invitations.resend` / `.revoke`; `canAccess()` gated on `view-any` + module active
- [ ] The invite modal rejects a duplicate pending email inline
