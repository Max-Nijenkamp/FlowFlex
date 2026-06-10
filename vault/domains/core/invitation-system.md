---
type: module
domain: Core Platform
domain-key: core
panel: app
module-key: core.invitations
status: planned
priority: v1-core
depends-on: [foundation.panels, foundation.email, core.rbac]
soft-depends: []
fires-events: []
consumes-events: []
patterns: [email]
tables: [user_invitations]
permission-prefix: core.invitations
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Invitation System

Team member invitation flow: owner sends email invite → recipient registers → gets role assigned automatically. The only way new users join a company workspace (no open self-registration — [[build/decisions/decision-2026-06-10-no-public-registration]]).

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/foundation/filament-panels\|foundation.panels]] | InvitationResource in `/app` |
| Hard | [[domains/foundation/email-setup\|foundation.email]] | invite emails |
| Hard | [[domains/core/rbac\|core.rbac]] | role selected at invite time |

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

### user_invitations

| Column | Type | Constraints | Notes |
|---|---|---|---|
| id | ulid | PK | |
| company_id | ulid | not null, indexed | |
| email | string | not null | one pending invite per email per company *(assumed)* |
| token | uuid | not null, unique | not hashed — single-use, short-lived |
| role | string | not null | role name to assign |
| invited_by | ulid | not null, FK users | |
| accepted_at | timestamp | nullable | |
| revoked_at | timestamp | nullable | |
| expires_at | timestamp | not null | created + 7 days |

**Indexes:** `(company_id, email)`, `token` unique

---

## DTOs

### CreateInvitationData (input)
| Field | Type | Validation |
|---|---|---|
| email | string | required, email, not already a user in this company, no pending invite in this company |
| role | string | required, exists for this company team, not `owner` *(assumed: owners transferred, not invited)* |

Message: "This email already has a pending invitation."

### AcceptInvitationData (input — public route)
| Field | Type | Validation |
|---|---|---|
| token | string | required, uuid, valid (unexpired, unaccepted, unrevoked) |
| name | string | required, max:200 |
| password | string | required, Password::defaults() (12+ chars, uncompromised) |

## Services & Actions

Actions:
- `SendInvitationAction::run(CreateInvitationData $data): UserInvitation` — creates row + queues `InvitationMail`
- `ResendInvitationAction::run(string $invitationId): UserInvitation` — new token, old invalidated, re-queues mail
- `RevokeInvitationAction::run(string $invitationId): void`
- `AcceptInvitationAction::run(AcceptInvitationData $data): User` — creates user, assigns role under correct team id, sets `accepted_at`, logs the user in; throws `InvalidInvitationTokenException`

---

## Vue + Inertia (the only public registration surface)

`/register/invite/{token}` — public route, not behind auth:

```php
// AuthController@showInviteRegistration
$invitation = UserInvitation::withoutGlobalScope(CompanyScope::class)
    ->where('token', $token)
    ->whereNull('accepted_at')->whereNull('revoked_at')
    ->where('expires_at', '>', now())
    ->firstOrFail();

return inertia('Auth/InviteRegister', [
    'email' => $invitation->email,
    'company_name' => $invitation->company->name,
    'token' => $token,
]);
```

Registration form: name + password only (email pre-filled and read-only). Rate-limited (`login` limiter).

---

## Filament

**Nav group:** Team

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `InvitationResource` | #1 CRUD resource | pending list w/ expiry countdown; create, resend, revoke actions |

---

## Permissions

`core.invitations.view-any` · `core.invitations.create` · `core.invitations.resend` · `core.invitations.revoke`

---

## Test Checklist

- [ ] Tenant isolation: invites of company A invisible to company B
- [ ] Module gating: n/a (always-on core) — but permission gating verified
- [ ] Accept flow creates user with correct `company_id` + role under correct team
- [ ] Expired token → 404/invalid page, no acceptance
- [ ] Revoked token unusable; resend invalidates old token
- [ ] Duplicate pending invite for same email rejected with message
- [ ] Invite mail queued on `notifications`, never sent sync
- [ ] Accept route rate-limited

---

## Build Manifest

```
database/migrations/xxxx_create_user_invitations_table.php
app/Models/Core/UserInvitation.php
app/Data/Core/{CreateInvitationData,AcceptInvitationData}.php
app/Actions/Core/{SendInvitationAction,ResendInvitationAction,RevokeInvitationAction,AcceptInvitationAction}.php
app/Exceptions/Core/InvalidInvitationTokenException.php
app/Mail/Core/InvitationMail.php
app/Http/Controllers/AuthController.php (showInviteRegistration, acceptInvite)
resources/js/Pages/Auth/InviteRegister.vue
routes/web.php (/register/invite/{token})
app/Filament/App/Resources/InvitationResource.php
database/factories/Core/UserInvitationFactory.php
tests/Feature/Core/{InvitationFlowTest,InvitationSecurityTest}.php
```

---

## Related

- [[domains/core/rbac]]
- [[architecture/email]] — invitation email template
- [[build/decisions/decision-2026-06-10-no-public-registration]]
- [[frontend/_index]]
