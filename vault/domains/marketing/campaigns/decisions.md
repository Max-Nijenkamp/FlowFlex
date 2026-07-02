---
domain: marketing
module: campaigns
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Campaigns — Decisions

Parent: [[_module]]

## ADR: Recipients snapshotted at schedule time

- **Context:** Segments change over time; a send must be deterministic.
- **Decision:** `CampaignService::schedule` materialises the recipient list into `mkt_campaign_recipients` at schedule time (not send time). Suppression + undeliverables excluded once, up front.
- **Consequences:** Deterministic sends; late segment edits don't alter an in-flight campaign.

## ADR: Resume-safe batched send

- **Decision:** `SendCampaignBatchJob` is chained + chunked, per-recipient try/catch, guarded on recipient `pending`. A mid-send failure resumes only pending recipients.
- **Consequences:** No double-sends on retry; `sending → failed` is resumable.

## ADR: Shared suppression list across marketing

- **Decision:** `mkt_unsubscribes` is one company-scoped table honoured by both campaigns and [[../email-sequences/_module|sequences]]. An unsubscribe from either suppresses both.
- **Consequences:** A single marketing opt-out is respected everywhere; consistent with GDPR withdrawal.

## ADR: Audiences read-only from CRM (data-ownership)

- **Decision:** Recipients come from `SegmentService::contacts()` — read-only. Campaigns writes only its own tables.
- **Consequences:** No write into CRM tables ([[../../../security/data-ownership]]).

## Related

- [[_module]] · [[architecture]] · [[unknowns]]
