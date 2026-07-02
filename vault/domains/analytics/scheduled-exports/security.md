---
domain: analytics
module: scheduled-exports
type: security
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Scheduled Exports — Security

See also [[../../../security/tenancy-isolation]], [[../../../security/authn-authz]], [[../../../security/data-ownership]], [[../../../architecture/filament-patterns]].

---

## Permissions

| Permission | Description |
|---|---|
| `analytics.exports.view-any` | View schedules + delivery log |
| `analytics.exports.manage` | Create / edit / pause / resume / delete schedules |

---

## Access Contract

```php
canAccess() = Auth::user()->can('analytics.exports.view-any')
           && BillingService::hasModule('analytics.exports')
```

Per [[../../../architecture/filament-patterns]] #1.

---

## Key controls

1. **Upload/file contract** (medium, per [[../../../build/security-audit-2026-06-11]]): generated export files are stored under the **company disk** at `companies/{id}/exports/`. Large files (> 10 MB *(assumed)*) are delivered via a **tenant-scoped, time-limited signed link** instead of an attachment — never a raw public URL.
2. **Recipients are company users only** *(assumed: no external emails v1)* — validated against the company's user set on `CreateScheduleData`; no arbitrary email addresses.
3. **Source access check.** `source_id` must exist and be accessible to the schedule owner; a report/dashboard the user can't see can't be scheduled.
4. **No double-send.** `next_run_at` advances in the same transaction as the log write — the idempotency guard.

---

## Tenant Isolation

- `bi_export_schedules` + `bi_export_log` scoped by `company_id` via `BelongsToCompany` + `CompanyScope`.
- The run command + `ScheduledExportMail` execute under each company's `CompanyContext` (`WithCompanyContext`) — source reads and file paths are always company-scoped.

See [[../../../security/tenancy-isolation]] and [[../../../architecture/multi-tenancy]].
