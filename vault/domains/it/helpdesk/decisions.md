---
domain: it
module: helpdesk
type: decisions
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# IT Helpdesk — Decisions

---

## Auto-Close 3 Days After Resolve

Resolved tickets auto-close 3 days after `resolved_at` if the requester takes no further action. Handled by the scheduled `AutoCloseItTicketsCommand` — the sole automatic path to `closed`. This keeps the queue clean without forcing IT staff to manually close every ticket. The 3-day window is *(assumed)* and configurable — see [[unknowns|helpdesk.unknowns]].

---

## Internal-Only Reply Flag

Replies carry an `is_internal` boolean. Internal notes are IT-to-IT scratchpad: invisible to the requester and generating no notification. Public replies notify the requester through core.notifications. This avoids a separate "notes" table by folding IT notes into the same thread with a visibility flag.

---

## Simple Per-Priority SLA Hours (No Full SLA Module)

SLA targets are a simple per-priority hours config (e.g. urgent = 4h, high = 8h, normal = 24h, low = 72h) rather than reusing / building a full SLA engine. Internal helpdesk does not need the escalation matrices, business-hours calendars, or breach-workflow machinery of a customer-facing SLA module. Values are *(assumed)* — see [[unknowns|helpdesk.unknowns]].

---

## Sequential Ticket Numbers

Each ticket gets a human-readable `ticket_number` that is sequential per company (unique `(company_id, ticket_number)`). Sequential (not random/ULID-based) numbering is chosen so employees and IT can reference tickets verbally ("ticket 142"). The generation mechanism (counter table vs. max+1 in a transaction) is *(assumed)* — see [[unknowns|helpdesk.unknowns]].
