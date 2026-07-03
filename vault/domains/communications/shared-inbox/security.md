---
domain: communications
module: shared-inbox
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Shared Inbox — Security

## Permissions

| Permission | Grants |
|---|---|
| `comms.inbox.view-any` | View conversations + messages |
| `comms.inbox.reply` | Send outbound replies / internal notes |
| `comms.inbox.assign` | Assign conversations to team members |
| `comms.inbox.change-status` | Transition status open / pending / resolved *(assumed — status change had no explicit verb)* |
| `comms.inbox.snooze` | Snooze / reopen a conversation *(assumed)* |
| `comms.inbox.manage-channels` | Activate / deactivate channels |

**Verb-per-command:** `change-status` and `snooze` cover the `open ⇄ pending ⇄ resolved ⇄ snoozed` transitions
in `InboxService::setStatus` / `snooze` ([[./architecture]] Services). Seeded in `PermissionSeeder`. See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('comms.inbox.view-any')
        && BillingService::hasModule('comms.inbox');
}
```

## Tenant Isolation

- `comms_channels`, `comms_conversations`, `comms_messages` all carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains every query.
- Inbound webhook processing resolves the channel — and therefore the company — from the provider payload, then runs under `WithCompanyContext` on the queue. See [[../../../security/tenancy-isolation]] and [[../../../architecture/patterns/tenant-context-pitfalls]].

## Webhook & Rate Limiting (medium — [[../../../_archive/build-history/security-audit-2026-06-11]])

- Inbound channel webhook controllers (in the channel modules) must be **signature-verified** and behind a **throttle / rate limiter** to protect the inbound pipeline from flooding.
- **Outbound send** (`InboxService::send`, the `comms.inbox.reply` action) is external outbound comms — it carries the `panel-action` rate limiter ([[../../../architecture/security]]); the channel driver's own provider limits apply downstream.
- Body content is HTML-purified (`ezyang/htmlpurifier`) before storage.

## Upload Contract (medium)

Message attachments: MIME/extension whitelist, max size, tenant-scoped path `companies/{company_id}/comms/...` via `core.files` (Media Library). See [[../../core/file-storage/_module]].

## Encrypted Fields

None in this module. Channel **secrets** (API keys, OAuth tokens) live in the channel modules' own tables and are encrypted there.

## GDPR

Conversations of erased contacts are unlinked (`contact_id` nulled); bodies retained as company records per [[../../../architecture/data-lifecycle]] *(assumed)*. See [[unknowns]].

## Related

- [[_module]] · [[../../../security/data-ownership]]
