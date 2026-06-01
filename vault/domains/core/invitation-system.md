---
type: module
domain: Core Platform
panel: app
module-key: core.invitations
status: planned
color: "#4ADE80"
---

# Invitation System

Team member invitation flow: owner sends email invite → recipient registers → gets role assigned automatically. The only way new users join a company workspace (no open self-registration).

---

## Core Features

- Owner/admin sends invite: email address + role selection
- Invitation email sent via queue (`notifications` queue)
- Invite token: UUID, 7-day expiry, single-use
- Recipient clicks link → registration form pre-filled with email
- On registration: user created with `company_id`, role assigned, `user_invitations.accepted_at` set
- Resend invite: invalidates old token, generates new one
- Revoke invite: marks as revoked before acceptance
- Invite list: pending invites visible in User Management with expiry countdown

---

## Data Model

| Table | Key Columns |
|---|---|
| `user_invitations` | company_id, email, token (UUID), role, invited_by (user_id), accepted_at, revoked_at, expires_at |

Token is not hashed (it's single-use and short-lived). Store as UUID, verify on click.

---

## Filament

**`/app` panel:**
- `InvitationResource` — list pending invites, create invite, resend, revoke
- Invite form: email + role select (populated from `roles` table for this company)

---

## Vue + Inertia (Registration Page)

`/register/invite/{token}` — public route, not behind auth:

```php
// AuthController@showInviteRegistration
$invitation = UserInvitation::where('token', $token)
    ->whereNull('accepted_at')
    ->whereNull('revoked_at')
    ->where('expires_at', '>', now())
    ->firstOrFail();

return inertia('Auth/InviteRegister', [
    'email' => $invitation->email,
    'company_name' => $invitation->company->name,
    'token' => $token,
]);
```

Registration form: name + password only (email pre-filled and read-only).

---

## Related

- [[domains/core/rbac]]
- [[architecture/email]] — invitation email template
