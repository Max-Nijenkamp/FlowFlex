---
domain: core
module: invitation-system
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Invitation System — Security

Parent: [[_module]]

## Permissions

`core.invitations.view-any` · `core.invitations.create` · `core.invitations.resend` · `core.invitations.revoke`

## Authorization

Every Filament artifact gates on:
`canAccess() = Auth::user()->can('core.invitations.view-any') && BillingService::hasModule('core.invitations')`
per [[../../../architecture/filament-patterns]] #1. See [[../../../security/authn-authz]].

## Token security

- Token is a UUID, **single-use**, 7-day expiry — short-lived by design, so it is stored plain (not hashed).
- Resend rotates the token and invalidates the old one; revoke closes the invite before acceptance.
- The public accept route is **rate-limited** (`login` limiter) — see [[../../../architecture/security]].

## Tenancy

`user_invitations` is company-scoped via `CompanyScope`; company A's invites are invisible to company B. The public accept controller loads the invitation with `withoutGlobalScope(CompanyScope::class)` because the recipient is not yet authenticated into any tenant — validity is enforced by token + expiry + accepted/revoked checks. See [[../../../security/tenancy-isolation]].

## No public self-registration

`/register/invite/{token}` is the only public registration surface. There is no open sign-up form — new users can only join an existing company via a valid invite token. See [[decisions]].
