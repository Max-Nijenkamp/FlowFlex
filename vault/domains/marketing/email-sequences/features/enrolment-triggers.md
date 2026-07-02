---
domain: marketing
module: email-sequences
feature: enrolment-triggers
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-06-20
---

# Feature: Enrolment Triggers

Decide who enters a sequence and when.

## Behaviour

- Trigger types: **form submitted** (consume `FormSubmissionReceived`), **added to segment** (nightly diff), **contact created** (observer hook), **manual** (from a contact).
- `EnrolFromFormListener` enrols when the event's `form_id` matches `trigger_config.form_id`.
- Guards: one active enrolment per `(sequence, contact)`; suppressed contacts rejected.

## UI

- **Kind**: background
- **Trigger**: event listener + scheduled diff — no dedicated page. Configured in the [[build-sequence]] form (trigger picker); enrolments viewed via `SequenceEnrolmentResource` (a simple-resource list with an unenrol action).
- **States**: n/a (background). Enrolment list: empty ("no one enrolled yet") · loading · error (duplicate-active silently skipped) · selected (enrolment row → unenrol).
- **Gating**: `marketing.sequences.enrol` for manual enrol / unenrol.

## Data

- Owns / writes: `mkt_sequence_enrolments` (own module).
- Reads: contact record (CRM), segment membership, incoming form event payload — read-only.
- Cross-domain writes: none — reacts to `FormSubmissionReceived`, writes only its own enrolment ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `FormSubmissionReceived` from [[../../forms/_module|marketing.forms]] → enrol.
- Reads: `SegmentService` membership from [[../../../crm/customer-segments/_module|crm.segments]]; contacts from [[../../../crm/contacts/_module|crm.contacts]].
- Shared entity: `mkt_unsubscribes` (suppression check, owned by campaigns).

## Unknowns

- Segment-exit detection + contact-created observer wiring unspecified. See [[../unknowns]].

## Related

- [[../_module|Email Sequences]] · [[advancement-engine]] · [[../api]]
