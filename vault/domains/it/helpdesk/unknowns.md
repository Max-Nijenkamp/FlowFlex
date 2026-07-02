---
domain: it
module: helpdesk
type: unknowns
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# IT Helpdesk — Unknowns & Assumptions

All items below are unverified. They function as authoritative defaults at build time but are overridable via ADR. Design-affecting items should be resolved before implementation begins.

---

## Assumed Items (verbatim from spec, unverified)

> [!warning] UNVERIFIED — auto-close window
> `*(assumed)*` — resolved tickets auto-close **3 days** after `resolved_at`. The window length is not confirmed and should be a company setting. See [[decisions|helpdesk.decisions]].

> [!warning] UNVERIFIED — no full SLA module reuse
> `*(assumed: no full SLA module reuse)*` — SLA targets are a **simple per-priority hours config**, not a reused/standalone SLA engine. The actual per-priority hour values (urgent/high/normal/low) are placeholders.

> [!warning] UNVERIFIED — requester asset link scope
> `*(assumed)*` — a requester may only attach `asset_id` values that are **their own assigned assets**. Whether IT staff can attach any company asset, and how "assigned to" is resolved from `it.assets`, is unconfirmed.

> [!warning] UNVERIFIED — internal-note authorship
> `*(assumed)*` — only IT staff (holders of `it.helpdesk.respond`) may set `is_internal = true`. Requesters' replies are always public.

> [!warning] UNVERIFIED — reopen path
> `*(assumed)*` — a requester replying to a `resolved` ticket before auto-close reopens it (`resolved → in_progress`). The exact reopen trigger and whether it re-notifies the assignee is unconfirmed.

> [!warning] UNVERIFIED — auto-close schedule cadence
> `*(assumed)*` — `AutoCloseItTicketsCommand` runs **daily**. The schedule frequency is not specified.

> [!warning] UNVERIFIED — ticket-number generation
> `*(assumed)*` — `ticket_number` is sequential per company; the generation mechanism (counter table vs. `MAX+1` in a transaction) and format (bare integer vs. prefixed) are unconfirmed. See [[decisions|helpdesk.decisions]].

---

## Open Questions

1. **Auto-close window as a setting** — is 3 days global or a per-company `spatie/laravel-settings` value? Resolve before writing `AutoCloseItTicketsCommand`.
2. **SLA breach behaviour** — do SLA hours drive anything (highlighting, notification, escalation) or are they display-only targets? Affects whether a breach job/field is needed.
3. **Reopen semantics** — does a requester reply reopen a resolved ticket, and does that reset `resolved_at` / cancel the auto-close countdown?
