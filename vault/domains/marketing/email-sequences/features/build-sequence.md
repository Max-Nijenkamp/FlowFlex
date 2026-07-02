---
domain: marketing
module: email-sequences
feature: build-sequence
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Build Sequence

Author a multi-step drip: ordered emails with wait delays and a trigger.

## Behaviour

- Name the sequence, pick `trigger_type` + config, add ≥1 step (`order`, subject, body, wait_days).
- Steps are strictly ordered; body purified.
- Toggle `is_active` to pause/resume (pauses all enrolments).

## UI

- **Kind**: simple-resource
- **Page**: `SequenceResource` (`/marketing/sequences`) — Sequences nav group.
- **Layout**: table (name, trigger, active, enrolled count) + form (trigger picker + step **repeater** with per-step stats).
- **Key interactions**: add/reorder steps in the repeater; set trigger config; toggle active; view page shows per-step open/click.
- **States**: empty (no sequences → CTA) · loading (stats) · error (≥1 step required; invalid trigger config) · selected (step highlighted in repeater).
- **Gating**: `marketing.sequences.create` / `marketing.sequences.update`.

## Data

- Owns / writes: `mkt_sequences`, `mkt_sequence_steps` (own module).
- Reads: form list (for form trigger config), segment list — read-only.
- Cross-domain writes: none ([[../../../../security/data-ownership]]).

## Relations

- Reads: form definitions from [[../../forms/_module|marketing.forms]]; segments from [[../../../crm/customer-segments/_module|crm.segments]].
- Feeds: steps consumed by [[advancement-engine]]; trigger config consumed by [[enrolment-triggers]].
- Shared entity: none written.

## Unknowns

- Branching by open/click deferred *(assumed linear)*. See [[../unknowns]].

## Related

- [[../_module|Email Sequences]] · [[enrolment-triggers]] · [[advancement-engine]]
