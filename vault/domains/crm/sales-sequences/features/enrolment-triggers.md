---
domain: crm
module: sales-sequences
type: feature
feature: enrolment-triggers
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature — Enrolment Triggers

A sequence's `trigger_type` decides how contacts enter it.

## Trigger types

| Trigger | Source | Config |
|---|---|---|
| `manual` | Rep enrols from Contact/Deal | none |
| `stage-change` | Deal moves to a configured stage | `trigger_config.stage_id` |
| `segment-entry` | Contact enters a segment | `trigger_config.segment_id` |
| `deal-won` | `DealWon` event | trigger rules |
| `invoice-paid` | `InvoicePaid` event | trigger rules |

## Event-driven enrolment

Payload contracts are defined in [[../../../../architecture/event-bus]].

- `DealWon` → `EnrollInSuccessSequenceListener` enrols the won deal's account contacts into any sequence with trigger `deal-won`.
- `InvoicePaid` → `TriggerUpsellSequenceListener` enrols per `invoice-paid` trigger rules (upsell cadence).

Both listeners `implements ShouldQueue` + `WithCompanyContext`. If no sequence matches the trigger, the listener is a no-op.

## Enrolment rules

- One active enrolment per (contact, sequence) — a second attempt is rejected with "Contact is already in this sequence."
- Re-enrolment is allowed once the prior enrolment completes.
- `deal_id` is optional context on the enrolment.

## UI
- **Kind**: background — event-driven enrolment listeners; no page of its own. (The `manual` trigger is an action on the Contact/Deal view; trigger config is set on the sequence builder custom-page.)
- **Page**: no dedicated page. Listeners: `EnrollInSuccessSequenceListener` (`DealWon`), `TriggerUpsellSequenceListener` (`InvoicePaid`); manual enrol via a Contact/Deal action.
- **Layout**: n/a (headless). Trigger type + `trigger_config` edited on `SequenceResource` / sequence builder.
- **Key interactions**: none direct for event triggers; manual = "Enrol in sequence" action on contact/deal.
- **States**: empty (no matching sequence → listener no-op) · loading (listener queued) · error (retried on queue) · selected (already-enrolled contact rejected: "already in this sequence")
- **Gating**: `crm.sequences.enrol` *(assumed)* for manual enrol; listeners run under `WithCompanyContext`

## Data
- Owns / writes: `crm_sequence_enrolments` (creates enrolment; enforces one active per `(contact, sequence)`), reads `crm_sequences` for `trigger_type` / `trigger_config`
- Reads: [[../../contacts/_module|crm.contacts]] (target contact) and deal context (read-only)
- Cross-domain writes: via events only ([[../../../../security/data-ownership]])

## Relations
- Consumes: `DealWon` from crm.deals/finance, `InvoicePaid` from finance, `SegmentEntered` *(assumed)* from [[../../customer-segments/_module|crm.segments]], `DealStageChanged` *(assumed)* → enrol
- Feeds: enrolment created → picked up by [[step-advancement|step-advancement]]
- Shared entity: contacts; deals; segments (audiences owned by customer-segments)

## Test Checklist

### Unit
- [ ] `trigger_type` / `trigger_config` resolves the right enrolment source (manual / stage-change / segment-entry / deal-won / invoice-paid)
- [ ] A second active enrolment per (contact, sequence) rejected; re-enrol allowed after completion

### Feature (Pest)
- [ ] `DealWon` → `EnrollInSuccessSequenceListener` enrols account contacts into `deal-won` sequences; no match = no-op
- [ ] `InvoicePaid` → `TriggerUpsellSequenceListener` enrols per `invoice-paid` rules; listeners run `ShouldQueue` + `WithCompanyContext` (correct tenant)
- [ ] Manual enrol requires `crm.sequences.enrol`

## Related

- [[../architecture]]
- [[step-advancement]]
- [[../../customer-segments/_module|Customer Segments]]
