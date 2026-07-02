---
domain: customer-success
module: nps
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# NPS — Security

## Permissions

| Permission | Description |
|---|---|
| `cs.nps.view-any` | View surveys, responses, and the NPS dashboard |
| `cs.nps.manage` | Create / edit / delete surveys |
| `cs.nps.send` | Send a survey to its audience |

Seeded in `PermissionSeeder`.

---

## Access Contract

```php
canAccess() = Auth::user()->can('cs.nps.view-any')
           && BillingService::hasModule('cs.nps')
```

Per [[../../../architecture/filament-patterns]] #1. Create/edit requires `cs.nps.manage`; the Send action requires `cs.nps.send`. Custom pages state the gate explicitly.

---

## Public Surface (HIGH — token-scoped)

The response page (`/nps/{token}`, GET + POST) is served **outside any authenticated panel guard**:

- **No Sanctum session** — the tenant + recipient are resolved solely from the `token` (unique uuid).
- **Single-use** — a token whose row already has a `score` is rejected (idempotent "already responded" state); enforced at the controller boundary inside a row lock.
- **Rate limited** — per-IP + per-token throttle on the POST to prevent enumeration / abuse ([[../../../architecture/security]]).
- **No PII echo** — the page shows only the survey question, never other recipients or account data.
- Tokens are unguessable uuids; expiry is by single-use, not time *(assumed)*.

## Tenant Isolation

- Both tables carry `company_id` with a global `CompanyScope` — see [[../../../architecture/multi-tenancy]].
- The public controller sets `CompanyContext` from the token's company before any query, so scoped models resolve correctly with no authenticated user.
- Audience contact reads go through `crm.contacts`' tenant-scoped read API.

---

## Rate Limiting

Public GET + POST on `/nps/{token}` are throttled (per-IP and per-token). The authenticated Send action is naturally bounded by `cs.nps.send`.

---

## Encrypted Fields

None. Scores and free-text comments are feedback, not sensitive personal identifiers. (Comment free-text is HTML-purified on display.)
