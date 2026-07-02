---
domain: marketing
module: utm-tracking
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# UTM Tracking — Decisions

Parent: [[_module]]

## ADR: First-touch immutable, last-touch upserted

- **Decision:** `UtmService::record` writes the first touch once (never overwritten) and upserts the last touch on each new capture; unique `(contact_id, touch_type)`.
- **Consequences:** Both attribution models available from two rows per contact; deterministic.

## ADR: Capture is event-driven from Forms

- **Decision:** UTM is captured on `FormSubmissionReceived` (forms carry UTM hidden fields *(assumed)*), not by a UTM-owned public endpoint.
- **Consequences:** No extra public surface; degrades gracefully if forms inactive. Landing-page visit capture is a soft add via `RecordVisitAction`.

## ADR: Revenue attribution is read-only via CRM

- **Decision:** Revenue-by-channel joins touches → contacts → deals through CRM services, read-only.
- **Consequences:** No CRM writes; attribution recomputes from live CRM data ([[../../../security/data-ownership]]).

## Related

- [[_module]] · [[architecture]] · [[unknowns]]
