---
domain: dms
module: retention-policies
feature: legal-hold
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Legal Hold

Exempt a specific document from all retention actions while a hold is active. A hold **always wins over any policy**.

## Behaviour

1. Place a hold via `PlaceLegalHoldData`: `document_id`, `reason` (required, max 1000). Records `placed_by`, `placed_at`.
2. **One active hold per document** — `released_at IS NULL` is the active state; a second active hold is rejected.
3. While active, the hold **blocks both deletion AND archive** — [[retention-run|Retention Run]] skips any held document before applying any action.
4. Release via `ReleaseLegalHoldAction` (sets `released_at`); the document becomes eligible for its policy again.
5. Legal hold wins over policy **and** over GDPR erasure precedence — erasure overrides retention for person-files, but a legal hold still blocks it.

## UI

- **Kind**: simple-resource (`LegalHoldResource`).
- **Page**: "Legal Holds" (`/dms/legal-holds`), nav group **Settings**.
- **Columns**: document · reason · placed_by · placed_at · status (active / released).
- **Form**: document selector; reason (required, textarea max 1000).
- **Filters**: status (active / released).
- **Row actions**: **place** (create) · **release** (sets `released_at`, requires no extra reason). No hard delete — holds are a compliance record.
- **States**: empty ("no legal holds") · error (duplicate active hold → toast).
- **Gating**: `dms.retention.manage-holds` + `BillingService::hasModule('dms.retention')`.

## Data

- Owns / writes: `dms_legal_holds` (this module).
- Reads: documents owned by [[../../document-library/_module|dms.library]] (to select the target document).
- Cross-domain writes: none — the hold row is retention's own; it only *exempts* a document, never mutates `dms_documents` ([[../../../../security/data-ownership|data-ownership]]).

## Relations

- Consumes: nothing.
- Feeds: [[retention-run|Retention Run]] reads active holds and skips those documents.
- Shared entity: the document (owned by `dms.library`).

## Test Checklist

### Unit
- [ ] Active-hold rule: a document with `released_at IS NULL` hold is reported held; released hold is not
- [ ] `PlaceLegalHoldData` validation: reason required, max 1000

### Feature (Pest)
- [ ] Placing a second active hold on the same document is rejected (concurrent placement race: only one succeeds under lockForUpdate)
- [ ] Held document is skipped by the retention run for BOTH archive and delete actions; release makes it eligible again
- [ ] Tenant isolation: company A cannot place/release a hold on company B's document; `dms.retention.manage-holds` required

### Livewire
- [ ] `LegalHoldResource` create validates required reason; release action sets `released_at`; no delete action exposed
- [ ] canAccess(): hidden without `dms.retention.manage-holds` or with `dms.retention` module inactive

## Unknowns

- On release, does an already-archived document re-expose to a delete policy immediately? — open ([[../unknowns]]).

## Related

- [[../_module|Retention Policies]] · [[retention-policy]] · [[retention-run]] · [[retention-audit-log]]
- [[../../document-library/_module|Document Library]] · [[../../../../core/data-privacy/_module|core.privacy]]
