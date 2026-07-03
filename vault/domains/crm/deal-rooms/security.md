---
domain: crm
module: deal-rooms
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Deal Rooms — Security

## Permissions

| Permission | Purpose |
|---|---|
| crm.deal-rooms.view-any | List / view rooms in the panel. |
| crm.deal-rooms.create | Create a room. |
| crm.deal-rooms.update | Update room content. |
| crm.deal-rooms.revoke | Revoke room access. |

## Access Contract

```php
public static function canAccess(): bool
{
    return auth()->user()?->can('crm.deal-rooms.view-any')
        && hasModule('crm.deal-rooms');
}
```

## Tenant Isolation

All four tables carry `company_id` and are scoped via `BelongsToCompany` / `CompanyScope`. The public route resolves company context from the `access_token`, never from an authenticated app guard. Shared documents are delivered via signed temp URLs so the raw media path is never exposed. See [[../../../security/tenancy-isolation]].

## Module Gating

Gated behind `crm.deal-rooms` in [[../../../infrastructure/module-catalog]].

## Encrypted Fields

None.

## Rate Limiting

| Surface | Limiter | Notes |
|---|---|---|
| Public room `/room/{token}` (`PublicDealRoomController`) | named guest-route limiter (per [[../../../architecture/security]] registry) | unauthenticated token surface — throttled per IP; token resolved server-side |
| Document delivery / `TrackDocumentViewAction` | named guest-route limiter | signed temp URL issuance; raw media path never exposed |

Named limiter (not *(assumed)*) is a requirement per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]]; exact registry name reconciled in [[./unknowns]].

## Source Security Notes

- **Public / portal guard (HIGH)** — `/room/{token}` is a public route on the guest guard with no app session. The token resolves company context without exposing the authenticated app guard; a document middleware protects `/room/{token}` document delivery. See [[../../../security/authn-authz]].
- **Signed temp URLs** — document delivery uses signed, expiring URLs so tenant-scoped media paths are never leaked to external buyers. See [[../../../security/tenancy-isolation]].
