---
type: module
domain: Document Management
panel: dms
module-key: dms.retention
status: planned
color: "#4ADE80"
---

# Retention Policies

Automated document lifecycle: archive or delete documents after a defined retention period. Supports compliance (GDPR, legal holds).

## Core Features

- Retention policy: name, applies-to (folder or document type/tag), retention period, action (archive/delete)
- Retention clock starts from document creation or last-modified date
- Scheduled job evaluates policies daily, archives/deletes expired documents
- Legal hold: flag documents exempt from retention deletion
- Archive: move to a read-only archive folder (not deleted)
- Deletion: soft-delete then hard-delete after grace period
- Retention audit log: what was archived/deleted, when, under which policy
- Pre-deletion notification to document owner

## Data Model

| Table | Key Columns |
|---|---|
| `dms_retention_policies` | company_id, name, applies_to_type, applies_to_id, retention_days, action (archive/delete), clock_from (created/modified) |
| `dms_legal_holds` | company_id, document_id, reason, placed_by, placed_at, released_at |
| `dms_retention_log` | company_id, document_id, policy_id, action, executed_at |

## Filament

**Nav group:** Settings

- `RetentionPolicyResource` — define policies
- `LegalHoldResource` — place/release legal holds
- Retention log as read-only resource

## Cross-Domain / Jobs

- Daily scheduled job processes retention (see [[architecture/queue-jobs]])
- Integrates with Core Data Privacy GDPR erasure

## Related

- [[domains/dms/document-library]]
- [[domains/core/data-privacy]]
