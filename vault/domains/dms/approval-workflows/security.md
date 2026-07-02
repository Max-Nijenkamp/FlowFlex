---
domain: dms
module: approval-workflows
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Approval Workflows — Security

## Permissions

| Permission | Grants |
|---|---|
| `dms.approvals.manage-workflows` | Create / edit / delete approval workflows |
| `dms.approvals.submit` | Submit a document into a workflow |
| `dms.approvals.act` | Approve / reject / request-changes on a request the user is assigned to |

> [!warning] UNVERIFIED
> The [[architecture#Access contract\|access contract]] gates on `dms.approvals.view-any`, which is **not** in the source permission list above. Either `view-any` needs adding to the permission set, or the resources should gate on one of the three listed permissions. Flagged in [[unknowns]].

## Access Contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('dms.approvals.view-any')
        && BillingService::hasModule('dms.approvals');
}
```

## Action Authorization (second gate)

Beyond the resource-level permission, `ApprovalService::act()` enforces two runtime checks that permissions alone cannot express:

- **Current-step only** — only an approver assigned at the request's current step (sequential) or in the parallel approver set may act.
- **Submitter ≠ approver** — the user who submitted the document cannot approve their own request *(assumed)*.

These mirror the [[../document-library/security#Folder Access Inheritance\|"second gate"]] pattern in `dms.library`: the permission opens the screen, business rules gate the individual action.

## Tenant Isolation

All three tables carry `company_id` (indexed) via `BelongsToCompany`; `CompanyScope` constrains every query. Documents referenced by requests are already tenant-scoped in `dms.library`. See [[../../../security/tenancy-isolation]].

## Data Ownership

`dms.approvals` writes only its own three tables. The document **lock** lives in the [[../version-control/_module|dms.versions]] table and is set/cleared by commanding that service — never a direct cross-domain write. Notifications are commands to [[../../core/notifications/_module|core.notifications]]. See [[../../../security/data-ownership]].

## Encrypted Fields

None. Approval comments and audit rows are stored as plain columns; no sensitive-data columns identified *(assumed)*.
