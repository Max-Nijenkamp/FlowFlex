---
domain: support
module: live-chat
type: decisions
build-status: planned
status: planned
color: "#4ADE80"
updated: 2026-07-02
---

# Live Chat — Local Decisions

## Decided

- **Per-chat visitor token, not company-wide widget auth.** A visitor's signed token scopes Reverb + HTTP access to exactly one `chat.{chat_id}` — the core security boundary for a public widget (HIGH audit item).
- **Offline → ticket, not a dropped message.** No agents online → `missed` chat + a ticket via `TicketService`, so nothing is lost and it lands in the normal queue.
- **Built JS embed, not an iframe app.** The widget is a compiled script asset (`resources/js/chat-widget/`) authenticated by widget key — lighter than a full portal.
- **Ticket creation via TicketService.** Chat never writes `sup_tickets` directly ([[../../../security/data-ownership]]).

## Assumed (overridable via ADR)

- Least-active-chats assignment *(assumed)*.
- IP-geo deferred *(assumed)*.
- Canned inserts in chat land with this module or later *(soft-dep)*.

## Related

- [[./unknowns]] · [[../../../architecture/websockets]] · [[../../../decisions/decision-2026-06-20-full-mapping-conventions]]
