---
domain: support
module: canned-responses
type: security
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Canned Responses — Security

## Permissions

| Permission | Description |
|---|---|
| `support.canned.view-any` | List canned responses (own + shared) |
| `support.canned.create` | Create a canned response |
| `support.canned.update` | Edit own canned responses |
| `support.canned.manage-shared` | Create/edit team-wide (shared) responses |

Seeded in `PermissionSeeder`.

## Access Contract

```php
canAccess() = Auth::user()->can('support.canned.view-any')
           && BillingService::hasModule('support.canned')
```

Per [[../../../architecture/filament-patterns]] #1.

## Visibility Scope

- Personal responses (`is_shared = false`) are visible only to `owner_id`.
- Shared responses require `support.canned.manage-shared` to create/edit.
- All scoped by `company_id` (global `CompanyScope`) — [[../../../architecture/multi-tenancy]].

## Content Safety

Bodies purified before storage; placeholder substitution never evaluates code — only string replacement of the known token set.

## Encrypted Fields

None.
