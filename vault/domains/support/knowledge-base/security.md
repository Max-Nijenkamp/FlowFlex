---
domain: support
module: knowledge-base
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Knowledge Base — Security

## Permissions

| Permission | Description |
|---|---|
| `support.kb.view-any` | List/manage articles in the panel |
| `support.kb.create` | Create an article |
| `support.kb.update` | Edit an article |
| `support.kb.publish` | Publish/unpublish an article |
| `support.kb.manage-categories` | CRUD categories |

Seeded in `PermissionSeeder`.

## Access Contract

```php
canAccess() = Auth::user()->can('support.kb.view-any')
           && BillingService::hasModule('support.kb')
```

Per [[../../../architecture/filament-patterns]] #1.

## Public Help Centre Guard

- Runs under a **guest guard** (not a panel session) — Vue + Inertia.
- Every public query filters `is_published = true` + `company_id` — drafts and other companies never leak ([[../../../architecture/search]] tenant-safe pattern).
- Feedback + view endpoints rate-limited per visitor to prevent count inflation.

## Rate Limiting

- Public help-centre feedback + view-tracking endpoints run under a named `help-centre` limiter *(assumed)* keyed by visitor IP/session ([[../../../architecture/security]] rate-limit registry) — public unauthenticated write endpoints require a cited limiter per [[../../../decisions/decision-2026-07-02-rate-limit-and-token-hardening]].

## Content Safety

Article bodies purified via `ezyang/htmlpurifier` before storage — XSS prevention per [[../../../architecture/security]].

## Encrypted Fields

None.
