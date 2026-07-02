---
domain: dms
module: version-control
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Version Control — Security

## Permissions

| Permission | Grants |
|---|---|
| `dms.versions.upload` | Upload a new version of a document |
| `dms.versions.restore` | Restore a historical version (creates a new version) |
| `dms.versions.force-unlock` | Release a lock held by another user |

> [!warning] UNVERIFIED
> The access contract (`architecture.md`) gates on `dms.versions.view-any`, but the source spec's Permissions list does **not** define it. Either the permission is missing from the list or the gate should reuse `dms.library.view-any`. Flagged in [[unknowns]].

Folder access (via `dms.library`'s `accessibleFoldersFor`) is a **second gate** on top of these permissions for viewing history and downloading historical versions. See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('dms.versions.view-any')
        && BillingService::hasModule('dms.versions');
}
```

## Locking as a Concurrency Control

A document may hold at most one `dms_document_locks` row (`document_id` unique). Upload of a new version by a user who is **not** the lock holder throws `DocumentLockedException`. `dms.versions.force-unlock` allows an admin to override. Locks auto-expire after 4h *(assumed)* via `ExpireStaleLocksCommand` so an abandoned edit cannot block a document indefinitely.

## Upload Contract (explicit)

Per the [[../../../build/security-audit-2026-06-11]] audit (medium):

- **Whitelist** — `UploadVersionData` reuses the `dms.library` MIME/extension whitelist and max upload size using `mimes` + `max` rules, referencing the [[../../../architecture/security]] baseline values explicitly (not by link alone).
- **Storage path** — version bytes are stored under `companies/{id}/dms/` via `CompanyPathGenerator`, identical to the library upload path.
- **Signed URLs** — historical-version download always uses a short-lived temporary signed URL; no permanent public path.

## Tenant Isolation

Both tables carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains every query. Version bytes are stored under `companies/{company_id}/dms/` via `CompanyPathGenerator`. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None. Version bytes are stored as-is by [[../../core/file-storage/_module|core.files]]; `change_note` is treated as non-sensitive free text *(assumed)*.
