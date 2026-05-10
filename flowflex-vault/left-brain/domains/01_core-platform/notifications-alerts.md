---
type: module
domain: Core Platform
panel: app
cssclasses: domain-admin
phase: 1
status: in-progress
migration_range: 010001–019999
last_updated: 2026-05-10
right_brain_log: "[[builder-log-core-platform-phase1]]"
---

# Notifications & Alerts

Cross-domain notification infrastructure. Any domain module fires a `NotifiableEvent`; the `NotificationRouter` service resolves the user's channel preferences, respects quiet hours, and dispatches to the correct channels. Supports database, email, push, SMS, Slack, and webhook delivery.

**Panel:** `app` (user preferences UI)  
**Phase:** 1 — must exist before Phase 2 domain modules can send notifications

---

## Features

### NotifiableEvent Interface

All domain events that require user notification implement `App\Contracts\Core\NotifiableEvent`:

```php
interface NotifiableEvent
{
    public function eventType(): string;
    public function priority(): string;       // critical | high | normal | low
    public function toNotification(User $user): Notification;
}
```

### Priority Routing

| Priority | Behaviour |
|----------|-----------|
| `critical` | All available channels; bypasses quiet hours and user preferences |
| `high` | Email + database channel |
| `normal` | Database channel only (default) |
| `low` | Queued digest only |

### NotificationRouter Service

`app/Services/Core/NotificationRouter.php`

- Accepts any `NotifiableEvent` + target `User`
- Loads user's `NotificationPreference` for the event type; falls back to `normal` (database channel) if none set
- Checks `NotificationQuietHours` — suppresses non-critical notifications during quiet window
- Critical events bypass both preference lookup and quiet hours
- Dispatches the resolved channels using the standard Laravel notification system
- Logs every delivery attempt to `notification_log` (including company_id, channel, status)

### Channels

- `database` — stored in Laravel notifications table; rendered in app panel notification bell
- `email` — queued mailable via Horizon
- `push` — via web push / Firebase (future integration hook)
- `sms` — via Twilio (future integration hook)
- `slack` — via Slack webhook URL on the team (future integration hook)
- `webhook` — via `WebhookEndpoint` (see [[api-integrations-layer]])

### Quiet Hours

- Per-user `NotificationQuietHour` rows define `start_time` and `end_time` in the user's timezone
- Non-critical notifications generated during a quiet window are suppressed (not queued for later delivery)

### Notification Watches

- `notification_watches` allows a user or role to subscribe to notifications for a specific model instance (e.g. watch a specific project for all updates)
- Used by domain modules to implement "follow" / "watch" patterns without custom tables per domain

### Events

- `NotificationPreferenceUpdated` — fired when a user changes their channel settings
- `DigestReady` — fired by a scheduled job when a digest batch is ready to send
- `NotificationDeliveryFailed` — fired when a channel dispatch throws; triggers retry logic

---

## Data Model

```erDiagram
    notification_preferences {
        ulid id PK
        ulid user_id FK
        ulid company_id FK
        string event_type
        json channels
        timestamps created_at/updated_at
    }

    notification_quiet_hours {
        ulid id PK
        ulid user_id FK
        ulid company_id FK
        time start_time
        time end_time
        timestamps created_at/updated_at
    }

    notification_log {
        ulid id PK
        ulid user_id FK
        ulid company_id FK
        string event_type
        string channel
        string status
        json payload
        timestamp created_at
    }

    notification_watches {
        ulid id PK
        ulid user_id FK
        ulid company_id FK
        string watchable_type
        ulid watchable_id
        timestamps created_at/updated_at
    }
```

> Note: `notification_log` table name is singular — `NotificationLog` model must declare `protected $table = 'notification_log'` (Eloquent would default to `notification_logs`).

---

## Permissions

```
core.notifications.manage-own-preferences
core.notifications.manage-team-defaults
core.notifications.view-delivery-log
```

---

## Related

- [[MOC_CorePlatform]]
- [[audit-log]] — delivery failures are audit-logged
- [[api-integrations-layer]] — webhook channel uses WebhookEndpoint
- [[entity-user]]
