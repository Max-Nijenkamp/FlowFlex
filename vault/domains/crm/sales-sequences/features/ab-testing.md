---
domain: crm
module: sales-sequences
type: feature
feature: ab-testing
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature — A/B Testing

Email steps can carry two variants so reps can test subject lines and copy. *(assumed: v1 supports two variants per email step, random split)*

## Assignment

At enrolment, each A/B-enabled step is assigned a variant by random split. The assignment is recorded in the enrolment's `variant_map` jsonb, so a given enrolment sends a consistent variant for that step.

## Sending

When `advanceDue()` executes an email step, it reads the enrolment's `variant_map` to pick the variant template and queues `SequenceStepMail` accordingly.

## Tracking

Per-step tracking (open/click/reply) is attributed per variant. `SequenceService::performance()` returns a per-variant breakdown in `SequenceStatsData`, letting reps compare variant reply rates.

## UI
- **Kind**: custom-page + widget — variant config on the sequence builder custom-page, results shown via a per-variant stats widget. (Chosen over pure widget because variant setup is interactive editing, not just a chart; the results half is a read-only widget.)
- **Page**: variant config within the sequence builder / `SequenceResource` step editor; results on a per-variant stats widget on the sequence detail page.
- **Layout**: two-variant editor (subject + body per variant) on the email step; results widget with per-variant open/click/reply columns.
- **Key interactions**: add/edit variant A/B copy; enable A/B on an email step; view per-variant results to pick a winner.
- **States**: empty (A/B off → single variant) · loading (stats query) · error (invalid variant config rejected at save) · selected (variant row highlighted in results)
- **Gating**: `crm.sequences.manage` *(assumed)* to configure variants

## Data
- Owns / writes: `crm_sequence_steps` (variant templates on the email step), `crm_sequence_enrolments` (`variant_map` jsonb — per-enrolment assignment at enrolment time)
- Reads: per-step tracking (open/click/reply) from own tables + `EmailTracked` signals to attribute per variant
- Cross-domain writes: via events only ([[../../../../security/data-ownership]])

## Relations
- Consumes: `EmailTracked` / `EmailReplied` from [[../../email-integration/_module|crm.email]] → per-variant attribution
- Feeds: variant-aware `SequenceStepMail` (send) via [[step-advancement|step-advancement]]
- Shared entity: none (variant data owned here)

## Related

- [[../architecture]]
- [[step-advancement]]
- [[../data-model]]
