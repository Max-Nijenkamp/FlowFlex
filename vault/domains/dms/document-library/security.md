---
domain: dms
module: document-library
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Document Library — Security

## Permissions

| Permission | Grants |
|---|---|
| `dms.library.view-any` | View the library + document records |
| `dms.library.upload` | Upload documents |
| `dms.library.move` | Move / copy documents |
| `dms.library.delete` | Delete documents |
| `dms.library.manage-folders` | Create / edit / delete folders |
| `dms.library.manage-access` | Configure folder access restrictions |

Folder-access list is a **second gate** on top of these permissions. See [[../../../security/authn-authz]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('dms.library.view-any')
        && BillingService::hasModule('dms.library');
}
```

## Folder Access Inheritance

`accessibleFoldersFor(User)` is the single source of truth for what a user may see. A `restricted` folder is invisible in the tree, the document grid, Meilisearch results, AND on the direct viewer URL for non-permitted users. Restriction is **inherited down the subtree**: restricting a parent restricts all descendants.

## Upload Contract (explicit)

Per the [[../../../build/security-audit-2026-06-11]] audit (medium):

- **Whitelist** — `UploadDocumentData` enforces a MIME/extension whitelist and max upload size using `mimes` + `max` rules, referencing the [[../../../architecture/security]] baseline values explicitly (not by link alone).
- **Rate limiter** — `RateLimiter::for` on the document **search** and **upload** endpoints, scoped per company/user, to prevent abuse of Meilisearch and storage.
- **Signed URLs** — preview/download always uses a short-lived temporary signed URL; no permanent public path.

## Tenant Isolation

All four tables carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains every query. Bytes are stored under `companies/{company_id}/dms/` via `CompanyPathGenerator`. See [[../../../security/tenancy-isolation]].

## Encrypted Fields

None. Document bytes are stored as-is by [[../../core/file-storage/_module|core.files]]; sensitive-document handling is deferred to folder access + retention/legal-hold, not column encryption *(assumed)*.
