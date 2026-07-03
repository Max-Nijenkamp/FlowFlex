---
domain: communications
module: internal-messaging
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Internal Messaging — Security

## Permissions

| Permission | Grants |
|---|---|
| `comms.internal.use` | Use chat — read/post, DMs, join public channels, react, mark-read (all users by default) |
| `comms.internal.manage-channels` | Create/administer channels, invite to private channels |

`use` covers the member-level command actions (post, join public, invite where member, mark-read, toggle-reaction);
`manage-channels` covers channel create/admin. Seeded in `PermissionSeeder`. See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('comms.internal.use')
        && BillingService::hasModule('comms.internal');
}
```

*(gate reconciled to `comms.internal.use` — the prior `view-any` was never defined in the permission set)*

## Visibility — second scope (critical)

Private-channel and DM content is **members-only**, enforced in **three** layers so no path leaks:

1. **Query**: message reads filter by membership on top of `CompanyScope`.
2. **Reverb channel auth** (`routes/channels.php`): non-members are rejected from the presence channel.
3. **Search post-filter**: Meilisearch results are filtered to the user's member channels *(assumed)*.

A missing layer is a data-leak — all three are required. See [[../../../security/tenancy-isolation]].

## Upload Contract (medium — [[../../../build/security-audit-2026-06-11]])

Chat attachments: MIME/extension whitelist, max size, tenant-scoped path via `core.files`.

## Tenant Isolation

All three tables carry `company_id` (indexed) via `BelongsToCompany`. Bodies purified (max 4000). See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None.

## Related

- [[_module]] · [[../../../security/data-ownership]]
