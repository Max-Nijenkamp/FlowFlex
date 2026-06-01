---
type: module
domain: Core Platform
panel: app
module-key: core.notifications
status: planned
color: "#4ADE80"
---

# Notifications

In-app notification inbox and email alert delivery for all FlowFlex domains. Every domain dispatches events; Notifications delivers them to the right users via the right channel.

---

## Core Features

- In-app notification inbox — bell icon in all panels, unread count badge
- Email notifications via Laravel's notification system (Mailpit in dev, configured SMTP in prod)
- Notification preferences per user: on/off per notification type, channel selection (in-app / email)
- Notification types delivered via events from other domains (leave approved, invoice paid, deal won, etc.)
- Mark as read, mark all as read, delete
- Notification grouping by domain — HR / Finance / CRM tabs in inbox
- Real-time badge update via Reverb WebSocket + Alpine.js

---

## Data Model

| Table | Key Columns |
|---|---|
| `notifications` | id (UUID), notifiable_type, notifiable_id, type, data (json), read_at, company_id, created_at |
| `notification_preferences` | company_id, user_id, notification_type, in_app_enabled, email_enabled |

---

## Filament

**All panels (global):**
- Bell icon widget in top navbar — shows unread count
- Slide-out notification panel — recent notifications grouped by domain

**`/app` panel:**
- `NotificationPreferencesPage` — user notification preferences form

---

## Related

- [[domains/core/_index]]
