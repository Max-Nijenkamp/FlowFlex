---
domain: dms
module: retention-policies
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Retention Policies

Automated document lifecycle for the DMS domain: archive or delete documents after a defined retention period, exempt documents under legal hold, and keep an append-only compliance log of every action. Implements [[../../../architecture/data-lifecycle|data-lifecycle]] rules (GDPR + statutory floors) for DMS content. Layers on top of the [[../document-library/_module|Document Library]] — it acts on documents but never owns them.

## Module-key

| Field | Value |
|---|---|
| key | `dms.retention` |
| priority | p2 |
| panel | dms |
| permission-prefix | `dms.retention` |
| tables | `dms_retention_policies`, `dms_legal_holds`, `dms_retention_log` |

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[../document-library/_module\|Document Library]] (`dms.library`) | Acts on documents; archive/delete executed **through** its `DocumentService`, never by writing `dms_documents` directly |
| Hard | [[../../core/billing-engine/_module\|core.billing]] | Module gating (`hasModule`) |
| Hard | [[../../core/rbac/_module\|core.rbac]] | Permissions, `canAccess()` |
| Hard | [[../../core/notifications/_module\|core.notifications]] | Pre-deletion notices to document owners |
| Hard | [[../../foundation/queue-workers/_module\|foundation.queues]] | The daily scheduled job runs on the queue |
| Soft | [[../../core/data-privacy/_module\|core.privacy]] | GDPR erasure interplay — erasure overrides retention for person-files; legal holds win over policies |

## Core Features

- **Retention policy** — name, applies-to (folder subtree or tag), retention period (`retention_days`, min 1), action (`archive` / `delete`), clock starts from document `created` or `modified` date, `is_active` toggle.
- **Scheduled evaluation** — `ProcessRetentionCommand` runs daily at 03:00, evaluates active policies, archives/deletes expired documents, notifies owners before deletion, logs every action.
- **Legal hold** — flag a document exempt from retention; a hold **always wins over any policy** and also **blocks archive**, not just deletion. One active hold per document.
- **Archive** — move to a read-only archive folder, `is_archived = true` (not deleted).
- **Deletion** — soft-delete first, then hard-delete + media purge after a 30-day grace *(assumed)*.
- **Retention audit log** — append-only record of what was archived / soft-deleted / hard-deleted / notified, when, under which policy — kept as compliance proof.
- **Pre-deletion notification** — to document owner 7 days before deletion *(assumed)*.
- **Statutory floors** — policies cannot delete below statutory retention classes from [[../../../architecture/data-lifecycle|data-lifecycle]] *(assumed: warning at save)*.

## See features/

- [[features/retention-policy|Retention Policy]] — define policy (applies-to, period, action, clock_from).
- [[features/legal-hold|Legal Hold]] — place/release a hold; always wins over policy.
- [[features/retention-run|Retention Run]] — the daily background job that evaluates, archives/deletes, notifies, logs.
- [[features/retention-audit-log|Retention Audit Log]] — append-only compliance proof, read-only view.

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

## Test Checklist

- [ ] Tenant isolation + module gating.
- [ ] Expired document archived/deleted per policy + `clock_from`.
- [ ] Active legal hold blocks delete AND archive.
- [ ] Pre-deletion notification 7 days before, once.
- [ ] Soft → hard delete after grace; media purged via `core.files`.
- [ ] Every action logged; log append-only.
- [ ] Daily run idempotent (re-run same day writes no duplicate actions).
- [ ] Archive/delete goes through `dms.library` `DocumentService`, never a direct `dms_documents` write.

## Cross-Domain Edges

| Direction | Event / API | Counterpart | Notes |
|---|---|---|---|
| Commands | `DocumentService::archive` / `::softDelete` | `dms.library` | Sets `is_archived` / soft-deletes documents **through the owning service** — retention never writes `dms_documents` |
| Commands | media purge | `core.files` | On hard-delete, purge media bytes through the file-storage service |
| Commands | pre-deletion notice | `core.notifications` | Notifies document owner 7 days before deletion *(assumed)* |
| Consumes / coordinates | GDPR erasure | `core.privacy` (soft) | Erasure overrides retention for person-files; legal holds still win over policies |
| Fires | *(none)* | — | Source `fires-events: []`. Whether retention should react to a `core.privacy` erasure event is open — see [[unknowns]] |

**Data ownership:** `dms.retention` writes only `dms_retention_policies`, `dms_legal_holds`, `dms_retention_log`. It **acts on** documents (archive → `is_archived`, delete → soft/hard-delete) but those live in `dms_documents` owned by [[../document-library/_module|dms.library]]; per [[../../../security/data-ownership|data-ownership]] retention must go through `dms.library`'s `DocumentService`, never write `dms_documents` directly. Media purge goes through [[../../core/file-storage/_module|core.files]]; notices through [[../../core/notifications/_module|core.notifications]].

---

## Related

- [[architecture]] · [[data-model]] · [[api]] · [[security]] · [[decisions]] · [[unknowns]]
- [[../document-library/_module|Document Library]]
- [[../../core/billing-engine/_module|core.billing]] · [[../../core/rbac/_module|core.rbac]] · [[../../core/notifications/_module|core.notifications]] · [[../../foundation/queue-workers/_module|foundation.queues]] · [[../../core/data-privacy/_module|core.privacy]]
- [[../../../architecture/data-lifecycle]]
