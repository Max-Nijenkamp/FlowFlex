---
type: domain-index
domain: Support & Help Desk
panel: support
color: "#4ADE80"
---

# Support & Help Desk

Customer ticket management, knowledge base, SLA tracking, live chat, and automation. **Panel:** `/support` (Orange) — Phase 2.

**Displaces**: Freshdesk, Zendesk (SMB tier), Intercom (support use case)

---

## Navigation Groups

- **Tickets** — Tickets, Ticket Inbox
- **Knowledge Base** — Articles, Categories
- **Live Chat** — Chat Queue, Transcripts
- **Analytics** — Support Dashboard
- **Settings** — SLA Policies, Canned Responses, Automations

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/support/tickets\|Tickets]] | `support.tickets` | planned | **P2 core** |
| [[domains/support/knowledge-base\|Knowledge Base]] | `support.kb` | planned | P2 |
| [[domains/support/sla\|SLA Management]] | `support.sla` | planned | P2 |
| [[domains/support/canned-responses\|Canned Responses]] | `support.canned` | planned | P2 |
| [[domains/support/automations\|Automations]] | `support.automations` | planned | P2 |
| [[domains/support/live-chat\|Live Chat]] | `support.chat` | planned | P3 |
| [[domains/support/support-analytics\|Support Analytics]] | `support.analytics` | planned | P3 |

---

## Key Patterns

- `spatie/laravel-model-states` — ticket status machine
- Custom pages — Ticket Inbox, Chat Queue, SLA Monitor, Support Dashboard
- `awcodes/filament-tiptap-editor` — KB articles
- `architecture/websockets` — live chat + SLA monitor real-time
- Cross-domain: `TicketResolved` → CSAT survey via Communications
