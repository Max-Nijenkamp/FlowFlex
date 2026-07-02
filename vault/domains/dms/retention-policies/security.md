---
domain: dms
module: retention-policies
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Retention Policies — Security

## Permissions

| Permission | Grants |
|---|---|
| `dms.retention.manage-policies` | Create / edit / delete retention policies |
| `dms.retention.manage-holds` | Place / release legal holds |
| `dms.retention.view-log` | View the append-only retention audit log |

> [!warning] UNVERIFIED
> The source's access contract references `dms.retention.view-any`, which is **not** in the permission list (`manage-policies` / `manage-holds` / `view-log`). Treated here as each artifact gating on its own manage/view permission. See [[unknowns]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('dms.retention.manage-policies')
        && BillingService::hasModule('dms.retention');
}
```

Legal-hold and log artifacts substitute their own permission (`manage-holds` / `view-log`).

## Data Ownership Boundary (security control)

Retention **acts on** documents but does not own them. Archive/delete are issued as **commands to `dms.library`'s `DocumentService`**, never as direct writes to `dms_documents`. Per [[../../../security/data-ownership|data-ownership]] this keeps blast-radius walls intact: a bug in retention cannot corrupt library data through a write path that doesn't exist. Media purge on hard-delete goes through [[../../core/file-storage/_module|core.files]]; the write always runs under the owning service's `CompanyContext`.

## Legal Hold Precedence

A legal hold **always wins** over any retention policy — it blocks both deletion **and** archive. `RetentionService::evaluate` skips any document with an active hold (`released_at IS NULL`) before applying any action.

## GDPR / Statutory Interplay

- **Erasure overrides retention** for person-files (coordinated with [[../../core/data-privacy/_module|core.privacy]], soft dep) — but **legal holds still win over policies**.
- **Statutory floors** from [[../../../architecture/data-lifecycle|data-lifecycle]] cap deletion: a policy cannot delete below a statutory retention class *(assumed: warning at save)*.

## Tenant Isolation

All three tables carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains every query, including the scheduled run. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None. `encrypted-fields: []` in source.
