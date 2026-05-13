---
type: domain-index
domain: Communications
panel: comms
panel-path: /comms
panel-color: Blue
color: "#4ADE80"
---

# Communications

One panel for company announcements, direct and group messaging, team channels, video conferencing, email broadcasts, and a unified notification centre — replacing Slack, Microsoft Teams for internal comms, and Mailchimp for bulk email broadcasts.

**Panel:** `comms` — `/comms`
**Filament color:** Blue

---

## Modules

| Module | Key | Description |
|---|---|---|
| [[announcements]] | comms.announcements | Company-wide announcements: author, target audience, schedule, send, and track reads |
| [[messaging]] | comms.messaging | Direct and group messaging between users with threads and file sharing |
| [[video-conferencing]] | comms.video | Meeting scheduling with video link generation, calendar integration, and recording storage |
| [[email-broadcasts]] | comms.broadcasts | Bulk email to company contacts and customers with template builder, send, and analytics |
| [[team-channels]] | comms.channels | Persistent topic-based channels with posts, reactions, and threads |
| [[notification-center]] | comms.notification-center | Unified notification inbox aggregating all cross-domain alerts in one place |
| [[ai-voice]] | comms.ai-voice | AI phone receptionist: 24/7 inbound call handling, intent routing, knowledge base answers, appointment booking, voicemail transcription, outbound campaigns |
| [[live-chat-widget]] | comms.live-chat | Embeddable chat widget for the company's website: AI first response, real-time messaging, visitor identification, offline ticket creation |

---

## Nav Groups

- **Internal** — team-channels, messaging, announcements, notification-center
- **Broadcast** — email-broadcasts, video-conferencing
- **Settings** — notification preferences, channel defaults, email sending domain

---

## Displaces

| Tool | Replaced By |
|---|---|
| Slack | team-channels, messaging |
| Microsoft Teams | team-channels, messaging, video-conferencing |
| Mailchimp (internal broadcasts) | email-broadcasts |
| Zoom (scheduling) | video-conferencing |
| Loom (async video) | video-conferencing (recording storage) |

---

## Related

- [[../hr/INDEX]] — employee records define the user base for internal comms
- [[../marketing/email-marketing]] — customer-facing email marketing handled in marketing domain
- [[../analytics/INDEX]] — communication metrics (email open rates, announcement reads) fed to BI
