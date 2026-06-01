---
type: domain-index
domain: Communications
panel: comms
color: "#4ADE80"
---

# Communications

Shared omnichannel inbox (email, WhatsApp, SMS, Instagram/Facebook), internal messaging, broadcast, and automation. **Panel:** `/comms` (Blue) — Phase 2.

Merged from: **Omnichannel Inbox** + **Communications** (formerly two separate domains)

**Key differentiator**: Native WhatsApp Business API — the #1 HubSpot pain point for EU SMBs (see [[product/positioning]]).

---

## Navigation Groups

- **Inbox** — Shared Inbox
- **Broadcast** — Broadcasts
- **Messaging** — Internal Messaging
- **Analytics** — Comms Dashboard
- **Settings** — Channels (Email, SMS, WhatsApp), Automations

---

## Modules

| Module | Key | Status | Priority |
|---|---|---|---|
| [[domains/communications/shared-inbox\|Shared Inbox]] | `comms.inbox` | planned | **P2 core** |
| [[domains/communications/whatsapp\|WhatsApp]] | `comms.whatsapp` | planned | **P2 core** |
| [[domains/communications/email-channel\|Email Channel]] | `comms.email` | planned | P2 |
| [[domains/communications/broadcast\|Broadcast]] | `comms.broadcast` | planned | P2 |
| [[domains/communications/internal-messaging\|Internal Messaging]] | `comms.internal` | planned | P3 |
| [[domains/communications/sms-channel\|SMS Channel]] | `comms.sms` | planned | P3 |
| [[domains/communications/automations\|Automations]] | `comms.automations` | planned | P3 |
| [[domains/communications/comms-analytics\|Comms Analytics]] | `comms.analytics` | planned | P3 |

---

## Key Patterns

- `architecture/websockets` — real-time message arrival (heavy use)
- Custom pages — Shared Inbox (3-panel), Internal Messaging, dashboards
- Encrypted channel credentials (API keys, OAuth tokens) — see [[architecture/patterns/encryption]]
- `propaganistas/laravel-phone` — WhatsApp + SMS numbers in E.164
- Provider webhooks (WhatsApp, SMS) verified before processing — see [[architecture/security]]
- **ADR needed**: WhatsApp provider (360dialog / Twilio / Meta direct)
