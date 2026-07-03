---
domain: support
module: support-analytics
feature: csat-survey
type: feature
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: CSAT Survey

Post-resolution satisfaction survey: mailed on resolve, answered on a public token page.

## Behaviour

- On `TicketResolved`, `SendCsatSurveyListener` (queued) creates a `sup_csat_responses` row (token, unanswered) and mails `CsatSurveyMail` with a `/csat/{token}` link.
- The customer opens the link (Vue + Inertia), rates 1–5, optional comment → `RecordCsatAction` stamps `responded_at`.
- One response per ticket (unique `ticket_id` + unique `token`); duplicate/replay rejected. Rate-limited.
- This module is the **v1 consumer** of `TicketResolved` until marketing's P3 CSAT.

## UI

- **Kind**: two surfaces — the send path is **background** (queued listener + mailable, no page); the response page is **public-vue**.
- **Page**: send = `SendCsatSurveyListener` + `CsatSurveyMail` (no UI); respond = `/csat/{token}` (`CsatController` + `resources/js/Pages/Csat/Respond.vue`), ui-strategy row #16.
- **Layout**: response page = star rating (1–5) + optional comment + submit; thank-you state after submit.
- **Key interactions**: pick rating → submit (optimistic) → thank-you; expired/answered token → friendly notice.
- **States**: empty (fresh survey) · loading (submit) · error (invalid/answered token, rate-limited) · selected (rating chosen, submitted).
- **Gating**: none (token-only guard, no panel session).

## Data

- Owns / writes: `sup_csat_responses` (the listener + the public action both write only this table).
- Reads: `sup_tickets` (for the survey context / agent attribution).
- Cross-domain writes: none — reacts to `TicketResolved`, writes its own table ([[../../../../security/data-ownership]]).

## Relations

- Consumes: `TicketResolved` from [[../../tickets/_module|support.tickets]].
- Feeds: CSAT scores into the [[./support-dashboard|Support Dashboard]] (CSAT widget, per-agent).
- Shared entity: `sup_tickets` (read for context).

## Test Checklist

### Unit
- [ ] Rating validation accepts 1–5 only; comment optional
- [ ] Survey token is unique and single-use

### Feature (Pest)
- [ ] `TicketResolved` creates exactly one `sup_csat_responses` row and queues `CsatSurveyMail` once (idempotent on redelivery)
- [ ] `RecordCsatAction` stamps `responded_at`; a second submit on the same token is rejected (locked `responded_at IS NULL` guard)
- [ ] Public submit is throttled by the named `csat` limiter; expired/answered token shows a friendly notice
- [ ] Listener writes only `sup_csat_responses` — no cross-domain write; `company_id` carried as a scalar

## Unknowns

- Send on `resolved` vs `closed`; comment PII treatment; marketing P3 handoff — [[../unknowns]].

## Related

- [[../_module|Support Analytics]] · [[./support-dashboard]] · [[../../../../architecture/event-bus]]
