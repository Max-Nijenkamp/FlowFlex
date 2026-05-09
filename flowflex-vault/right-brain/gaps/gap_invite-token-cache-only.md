---
type: gap
severity: medium
category: architecture
status: open
color: "#F97316"
discovered: 2026-05-09
discovered_in: admin-panel-flowflex
last_updated: 2026-05-09
---

# Gap: Invite Tokens Stored Only in Redis Cache

## Context

Discovered during Phase 0 audit. When a new company is created via `CompanyCreationService::create()`, an owner invite token is generated and stored in the Redis cache with a 7-day TTL. The token is emailed to the owner via the `UserInvited` event.

## The Problem

`cache()->put("invite_token:{$token}", [...], now()->addDays(7))` — the token exists only in Redis. There is no DB persistence. If Redis is flushed (deploy, restart, `redis-cli FLUSHALL`), all pending invite tokens are permanently lost. The owner cannot set a password, cannot log in, and there is no way to resend the invite (the token is gone and no record exists that it was issued).

**File:** `app/Services/Foundation/CompanyCreationService.php:83-90`

## Impact

- Owner of a new company cannot log in if Redis is flushed during their 7-day invite window
- No resend-invite workflow possible (token reference is lost)
- No audit trail of invite issuance/acceptance

## Proposed Solution

Create a `user_invitations` table (Phase 1 scope):

```
user_invitations
- id (ULID)
- user_id (FK → users.id)
- company_id (FK → companies.id)
- token (string, unique, indexed)
- expires_at (timestamp)
- accepted_at (timestamp, nullable)
- created_at / updated_at
```

Replace `cache()->put(...)` with `UserInvitation::create([...])`. The invite acceptance flow reads from this table instead of cache. Cache can still be used as a performance layer on top.

This is Phase 1 scope (Core Platform auth flows).

## Links

- Source builder log: [[builder-log-admin-panel-flowflex]]
- Related: [[workspace-panel]], [[auth-rbac]]
