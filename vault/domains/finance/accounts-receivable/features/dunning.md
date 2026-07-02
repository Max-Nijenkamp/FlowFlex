---
domain: finance
module: accounts-receivable
feature: dunning
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Feature — Automated Dunning

Escalating reminder emails chase overdue customers per aging bucket.

- `DunningRuleResource` (#1 CRUD resource) configures the sequence: `aging_bucket`, `days_overdue` threshold, `email_template`, `escalation_level` (unique per company), `is_active`. Steps run friendly → firm → final notice.
- `ProcessDunningCommand` runs daily 07:00 on the notifications queue. Per overdue invoice it evaluates active rules in `escalation_level` order and fires the next level **once**, guarded by `fin_invoices.last_dunning_level` *(assumed column)*.
- Reminders go out via `DunningMail` (queued mailable, [[../../../../architecture/email]]).
- Payment stops the sequence: the `InvoicePaid` listener resets `last_dunning_level` and busts the aging cache.
- Permission: `finance.ar.manage-dunning` for rule editing.

## UI
- **Kind**: simple-resource (rule CRUD) + background (the send)
- **Page**: `DunningRuleResource` (`/finance/ar/dunning-rules`) for rule CRUD; `ProcessDunningCommand` runs headless daily 07:00 on the notifications queue
- **Layout**: standard resource table + form — `aging_bucket`, `days_overdue`, `email_template`, `escalation_level`, `is_active`; the send has no page
- **Key interactions**: create/edit escalation steps (friendly → firm → final); the scheduled command evaluates active rules per overdue invoice and sends the next level once
- **States**: empty (no rules configured — resource empty state) · loading (resource save spinner) · error (validation on unique `escalation_level` per company) · selected (rule row open in form)
- **Gating**: `finance.ar.manage-dunning`

## Data
- Owns / writes: `fin_ar_dunning_rules`; updates `fin_invoices.last_dunning_level` *(assumed column)* to guard one-send-per-level (amounts as integer minor units / cents via brick/money)
- Reads: `fin_invoices` (overdue selection) from finance.invoicing
- Cross-domain writes: none to other domains' tables — only its own rule table and the guard column via invoicing's flow ([[../../../../security/data-ownership]])

## Relations
- Consumes: `InvoicePaid` from finance.invoicing → resets `last_dunning_level` + busts the aging cache
- Feeds: `DunningMail` (queued mailable, email) → customer reminder
- In-domain: `ProcessDunningCommand` reads active rules in `escalation_level` order

See [[../api]], [[../architecture]].
