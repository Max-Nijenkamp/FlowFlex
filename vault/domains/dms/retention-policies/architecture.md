---
domain: dms
module: retention-policies
type: architecture
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Retention Policies — Architecture

## Services & Actions

| Class | Type | Responsibility |
|---|---|---|
| `RetentionService::evaluate(): RetentionResult` | service method | Per active policy: find matching documents (folder subtree / tag) past the retention period (measured from `clock_from`), **skip documents under an active legal hold**, execute the policy action, log it, notify before delete. Chunked per-document with a per-document `try/catch` so one failure doesn't abort the run. |
| `RetentionService` — archive path | service method | Issues an **archive command to `dms.library`** (`DocumentService::archive`) which sets `is_archived = true` and moves the document to the read-only archive folder. Retention never writes `dms_documents`. |
| `RetentionService` — delete path | service method | Notifies owner 7 days before *(assumed)*, then commands `DocumentService::softDelete`. A later grace pass hard-deletes + purges media via [[../../core/file-storage/_module\|core.files]] after 30 days *(assumed)*. |
| `PlaceLegalHoldAction` | action (lorisleiva) | Places a hold on a document (reason required); enforces one active hold per document. |
| `ReleaseLegalHoldAction` | action (lorisleiva) | Releases a hold (sets `released_at`); document becomes eligible for policy again. |

Every state-changing document effect is a **command to the owning `dms.library` service**, per [[../../../security/data-ownership|data-ownership]]. See [[decisions]] for the ownership-boundary ADR.

## Filament Artifacts

| Artifact | Nav group | ui-strategy row | Notes |
|---|---|---|---|
| `RetentionPolicyResource` | Settings | #1 CRUD resource | Define policies; preview affected-count *(assumed)*. |
| `LegalHoldResource` | Settings | #1 CRUD resource | Place / release holds with a required reason. |
| Retention log | Settings | #1 (read-only) | Compliance view — append-only, no create/edit/delete. |

See [[../../../architecture/filament-patterns]] and [[../../../architecture/ui-strategy]].

### Access contract

```php
public static function canAccess(): bool
{
    return Auth::user()->can('dms.retention.manage-policies')
        && BillingService::hasModule('dms.retention');
}
```

Each artifact gates on its own permission (`manage-policies` / `manage-holds` / `view-log`) + module gating. The source's access contract references `dms.retention.view-any`, which is **not** in the permission list — flagged in [[unknowns]].

## Events

None fired or consumed. Source `fires-events: []` / `consumes-events: []`. Whether retention **should** consume a `core.privacy` `ErasureRequested` event (rather than coordinating via a read call) is an open question — see [[unknowns]].

## Jobs & Scheduling

| Job / Command | Queue | Schedule | Idempotency |
|---|---|---|---|
| `ProcessRetentionCommand` | `default` | daily **03:00** | **Log-row guard** per `(document_id, action)` — before acting, check `dms_retention_log` for an existing row for that document+action; date guards ensure a same-day re-run is a no-op. Re-run safe. |

The command iterates active policies, chunks matching documents, and wraps each document in `try/catch` so a single failure logs and continues. Archive/soft-delete are issued as commands to `dms.library`; the hard-delete grace pass purges media via `core.files`.

## Search & Realtime

None. No Meilisearch index, no realtime — retention is a background + settings surface.
