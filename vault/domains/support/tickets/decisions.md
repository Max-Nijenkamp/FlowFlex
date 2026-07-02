---
domain: support
module: tickets
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Tickets — Local Decisions

Module-local design decisions. Vault-wide ADRs live in [[../../../decisions/INDEX]].

## Decided

- **State machine, not plain enum.** Ticket status uses `spatie/laravel-model-states` (unlike CRM `lifecycle_stage`) because transitions carry guards + side effects (SLA pause/resume, `TicketResolved`, stamps). See [[./architecture]].
- **No separate Requester model.** Requester is a soft link to `crm_contacts` (find-or-create via `ContactService`) with standalone `requester_email`/`requester_name` fallback fields — mirrors the CRM "no Lead model" stance and keeps Support usable before CRM is built.
- **CSAT lives in analytics, not here.** `TicketResolved` is fired here; the survey mail + `sup_csat_responses` are owned by [[../support-analytics/_module|support.analytics]] — keeps tickets thin.
- **Auto-assign delegated to automations.** Round-robin / by-category assignment is a `support.automations` action when active; tickets ship with manual + category-default assignment only.

## Assumed (overridable via ADR)

- Inbound email via Resend/Postmark inbound parse webhook *(assumed)*.
- Reopen window default 14 days *(assumed)*; auto-close `resolved` after 3 days *(assumed)*.

## Related

- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
- [[./unknowns]]
