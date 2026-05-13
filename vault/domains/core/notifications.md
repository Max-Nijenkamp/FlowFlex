---
type: module
domain: Core Platform
panel: app
module-key: core.notifications
status: planned
color: "#4ADE80"
---

# Notifications

> Per-user notification preferences, in-app notification centre, and multi-channel delivery — database, email, and webhook — for every event raised across all domains.

**Panel:** `app`
**Module key:** `core.notifications`

## What It Does

The Notifications module provides the delivery infrastructure for all platform events. Every domain raises events (EmployeeHired, InvoicePaid, DealWon) and the notification system determines who to notify, on which channels, using each user's personal preferences. Users configure their own notification preferences from a Filament settings page. The in-app notification centre shows unread and read notifications with links to the relevant record. All notification delivery attempts are logged so audit and support can trace whether a notification was received.

## Features

### Core
- In-app notification centre: unread badge on nav, dropdown with recent notifications, "mark all read" action
- Email delivery via Laravel `Notification` + `Mail` channels — respects user preference per notification type
- Database channel: all notifications persisted to `notifications` table for in-app display
- User preferences page: per-notification-type toggles for email, in-app, and webhook channels
- Platform-wide announcements from admin panel delivered to all companies or a specific company

### Advanced
- Notification log table: every delivery attempt recorded with channel, status (sent/failed/bounced), and timestamp
- Retry logic for failed email deliveries: 3 attempts with exponential backoff via queued jobs
- Notification digest: configurable daily or weekly digest email summarising low-priority notifications instead of real-time delivery
- Company-level default preferences: owner sets defaults applied to all new users
- Webhook channel: any notification type can be routed to the company's registered webhook endpoints

### AI-Powered
- Smart notification grouping: burst events (20 task status changes in 1 minute) collapsed into a single digest notification rather than 20 individual ones
- Relevance scoring: low-relevance notifications downgraded from email to in-app only without user having to configure it manually

## Data Model

```erDiagram
    notifications {
        uuid id PK
        ulid company_id FK
        string type
        string notifiable_type
        ulid notifiable_id FK
        json data
        timestamp read_at
        timestamps created_at/updated_at
    }

    notification_preferences {
        ulid id PK
        ulid user_id FK
        string notification_type
        boolean in_app
        boolean email
        boolean webhook
        timestamps created_at/updated_at
    }

    notification_log {
        ulid id PK
        ulid company_id FK
        ulid user_id FK
        string notification_type
        string channel
        string status
        string error_message
        timestamps created_at/updated_at
    }
```

| Table | Purpose |
|---|---|
| `notifications` | Laravel default notifications table — in-app display |
| `notification_preferences` | Per-user, per-type channel preferences |
| `notification_log` | Delivery attempt audit trail |

## Permissions

- `core.notifications.view-own`
- `core.notifications.manage-preferences`
- `core.notifications.view-log`
- `core.notifications.manage-company-defaults`
- `core.notifications.send-announcement`

## Filament

- **Resource:** None (preferences are a settings page, not a resource list)
- **Pages:** `NotificationPreferencesPage` — tabbed by category, toggles per type and channel
- **Custom pages:** None
- **Widgets:** Unread Notifications Widget (nav badge)
- **Nav group:** Account (app panel)

## Displaces

| Competitor | Feature Displaced |
|---|---|
| Intercom | In-app notification centre |
| SendGrid | Transactional email notification delivery |
| OneSignal | Multi-channel notification routing |
| Slack (for internal alerts) | In-app alert centre |

## Related

- [[audit-log]]
- [[webhooks]]
- [[company-settings]]
