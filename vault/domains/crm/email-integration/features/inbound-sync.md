---
domain: crm
module: email-integration
type: feature
feature: inbound-sync
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Inbound Sync

Planned scheduled incremental pull of received emails, matched to contacts by address and logged to the activity timeline.

## Flow

1. `SyncMailboxesCommand` runs every 10 min (default queue), dispatching a per-connection `SyncMailboxJob`.
2. `EmailSyncService::sync(connectionId)` reads incrementally from `last_synced_at`.
3. Each message is wrapped in `try/catch`; a bad message does not abort the batch.
4. **Dedupe** on `message_id` (unique `(connection_id, message_id)`) — running twice yields no duplicates.
5. **Match** the contact by from/to address; unmatched messages are stored **unlinked** *(assumed)*.
6. An activity is logged on the [[../../activities/_module|Activities]] timeline.
7. The cursor (`last_synced_at`) advances.

## Notes

- v1 is scheduled-pull only; provider webhooks are deferred to v1.x *(assumed)* — see [[../unknowns]].
- Bodies are HTML-purified before storage — see [[../../../../security/data-privacy-gdpr]].

## Test Checklist

- [ ] Sync dedupes on `message_id` (run twice = no duplicates).
- [ ] Inbound matched to contact by address; unmatched stored unlinked *(assumed)*.
- [ ] Provider API mocked (`Http::fake`) — no real calls in tests.

## UI
- **Kind**: background — a scheduled sync job pulling messages; no interactive page of its own.
- **Page**: no dedicated page. Trigger: `SyncMailboxesCommand` (every 10 min) → per-connection `SyncMailboxJob`. Synced mail surfaces in `EmailThread` on contact/deal pages and on the activities timeline.
- **Layout**: n/a (headless). Sync status/last-synced shown on the connection row in `EmailConnectionResource`.
- **Key interactions**: none direct; users read the resulting thread/timeline. Manual "sync now" *(assumed)*.
- **States**: empty (nothing new since `last_synced_at`) · loading (job running) · error (per-message try/catch; bad message skipped, batch continues) · selected (n/a)
- **Gating**: sync honours the connection owner; thread visibility per email `visibility` + `crm.email.view`

## Data
- Owns / writes: `crm_emails` (message rows, dedupe on unique `(connection_id, message_id)`), `crm_email_connections` (advances `last_synced_at` cursor)
- Reads: [[../../contacts/_module|crm.contacts]] to match sender/recipient by address (read-only, never written); provider mailbox API
- Cross-domain writes: via events only ([[../../../../security/data-ownership]]) — activity logging done by dispatching to activities, not writing `crm_activities` directly

## Relations
- Consumes: scheduled tick → pull; provider mailbox messages
- Feeds: `EmailReceived` *(assumed)* → consumed by [[../../activities/_module|crm.activities]] (auto-log activity on timeline)
- Shared entity: contacts (matched read-only)

## Related

- [[../architecture]] · [[../data-model]] · [[../../contacts/_module|Contacts]] · [[../../activities/_module|Activities]]
