---
type: module
domain: Document Management
domain-key: dms
panel: dms
module-key: dms.retention
status: planned
priority: p2
depends-on: [dms.library, core.billing, core.rbac, core.notifications, foundation.queues]
soft-depends: [core.privacy]
fires-events: []
consumes-events: []
patterns: [gdpr, queues]
tables: [dms_retention_policies, dms_legal_holds, dms_retention_log]
permission-prefix: dms.retention
encrypted-fields: []
last-reviewed: 2026-06-11
color: "#4ADE80"
---

# Retention Policies

Automated document lifecycle: archive or delete documents after a defined retention period. Supports compliance (GDPR, legal holds). Implements [[architecture/data-lifecycle]] rules for DMS content.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/dms/document-library\|dms.library]] | acts on documents |
| Hard | [[domains/core/billing-engine\|core.billing]] + [[domains/core/rbac\|core.rbac]] + [[domains/core/notifications\|core.notifications]] + [[domains/foundation/queue-workers\|foundation.queues]] | gating, permissions, pre-deletion notices, daily job |
| Soft | [[domains/core/data-privacy\|core.privacy]] | GDPR erasure interplay (erasure overrides retention for person-files) |

---

## Core Features

- Retention policy: name, applies-to (folder subtree or tag), retention period, action (archive/delete)
- Retention clock starts from document creation or last-modified date (per policy)
- Scheduled job evaluates policies daily, archives/deletes expired documents
- Legal hold: flag documents exempt from retention deletion — **hold always wins over any policy**
- Archive: move to a read-only archive folder (not deleted), `is_archived = true`
- Deletion: soft-delete, then hard-delete (file + media) after 30-day grace *(assumed)*
- Retention audit log: what was archived/deleted, when, under which policy
- Pre-deletion notification to document owner 7 days before *(assumed)*
- Statutory floors: policies cannot delete below statutory retention classes from [[architecture/data-lifecycle]] *(assumed: warning at save)*

---

## Data Model

### dms_retention_policies

| Column | Type | Notes |
|---|---|---|
| id, company_id (indexed) | ulid | |
| name | string | |
| applies_to_type / applies_to_id | string / ulid nullable | folder / tag |
| retention_days | int min 1 | |
| action | string | archive / delete |
| clock_from | string | created / modified |
| is_active | boolean default true | |
| deleted_at | timestamp nullable | |

### dms_legal_holds — id, company_id (indexed), document_id FK, reason (required), placed_by, placed_at, released_at nullable; one active hold per document
### dms_retention_log — id, company_id (indexed), document_id, policy_id, action (archived/soft-deleted/hard-deleted/notified), executed_at — append-only, kept as compliance proof

---

## DTOs

### CreateRetentionPolicyData — name, applies_to {type in:folder,tag, id}, retention_days (min:1), action (in set), clock_from (in set)
### PlaceLegalHoldData — document_id, reason (required, max:1000)

## Services & Actions

- `RetentionService::evaluate(): RetentionResult` — per policy: matching documents past period, skip active holds, execute action, log, notify-before-delete; chunked, per-document try/catch
- `PlaceLegalHoldAction` / `ReleaseLegalHoldAction`
- Hard-delete grace pass: soft-deleted past grace → media purge + log

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ProcessRetentionCommand` | default | daily 03:00 | log-row guard per (document, action); date guards — re-run safe |

---

## Filament

**Nav group:** Settings

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| `RetentionPolicyResource` | #1 CRUD resource | preview affected-count *(assumed)* |
| `LegalHoldResource` | #1 CRUD resource | place/release with reason |
| Retention log | #1 (read-only) | compliance view |


**Access contract:** every artifact above gates on `canAccess() = Auth::user()->can('dms.retention.view-any') && BillingService::hasModule('dms.retention')` per [[architecture/filament-patterns]] #1 — custom pages state it explicitly. Public/portal surfaces use a guest or scoped-portal guard (Vue+Inertia per [[architecture/ui-strategy]]).

---

## Permissions

`dms.retention.manage-policies` · `dms.retention.manage-holds` · `dms.retention.view-log`

---

## Test Checklist

- [ ] Tenant isolation + module gating
- [ ] Expired document archived/deleted per policy + clock_from
- [ ] Active legal hold blocks delete AND archive
- [ ] Pre-deletion notification 7 days before, once
- [ ] Soft→hard delete after grace; media purged
- [ ] Every action logged; log append-only
- [ ] Daily run idempotent

---

## Build Manifest

```
database/migrations/xxxx_create_dms_retention_policies_table.php
database/migrations/xxxx_create_dms_legal_holds_table.php
database/migrations/xxxx_create_dms_retention_log_table.php
app/Models/DMS/{RetentionPolicy,LegalHold,RetentionLog}.php
app/Data/DMS/{CreateRetentionPolicyData,PlaceLegalHoldData}.php
app/Services/DMS/RetentionService.php
app/Actions/DMS/{PlaceLegalHoldAction,ReleaseLegalHoldAction}.php
app/Console/Commands/DMS/ProcessRetentionCommand.php
app/Filament/DMS/Resources/{RetentionPolicyResource,LegalHoldResource}.php
database/factories/DMS/{RetentionPolicyFactory,LegalHoldFactory}.php
tests/Feature/DMS/{RetentionTest,LegalHoldTest}.php
```

---

## Related

- [[domains/dms/document-library]]
- [[domains/core/data-privacy]]
- [[architecture/data-lifecycle]]
