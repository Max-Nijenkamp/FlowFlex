---
type: module
domain: Core Platform
panel: core
cssclasses: domain-core
phase: 1
status: planned
migration_range: 000000–099999
last_updated: 2026-05-09
---

# Notification Preferences

Per-user, per-channel, per-event-type notification control. Digest scheduling, quiet hours, and team-level defaults. Ensures users receive relevant alerts without notification fatigue. Foundation used by all 19 domains.

**Panel:** `core`  
**Phase:** 1 — must exist before any domain emits notifications

---

## Why Phase 1

Every domain sends notifications. If preferences don't exist in Phase 1, every domain builds its own ad-hoc preference system → 19 inconsistent implementations. Centralise once.

---

## Features

### Notification Channels
- **Email** — transactional (immediate) or digest
- **In-app** — notification bell in Filament panel + Vue portals
- **Push** — browser push (PWA) + mobile push (iOS/Android via FCN/APNs)
- **SMS** — Twilio / Vonage, high-priority alerts only
- **Slack / MS Teams** — webhook per workspace
- **Webhook** — custom URL (for integrations and automation)

### Per-Event-Type Preferences
Each notification event has a category and priority:

| Priority | Default | Can Disable? |
|---|---|---|
| Critical | All channels | No (security, legal compliance, payroll failures) |
| High | Email + In-app | Email only, not in-app |
| Normal | In-app only | Yes |
| Low | Digest only | Yes, including fully off |

User sees categorised list:
- Finance (invoice paid, overdue, payrun complete)
- HR (leave approved, payslip ready, onboarding task)
- Projects (task assigned, deadline approaching, comment mention)
- CRM (lead assigned, deal won, SLA breach)
- Operations (stock alert, maintenance due, field job assigned)
- IT (ticket assigned, access request, security alert)
- Legal (contract expiry, DSAR deadline, policy acknowledgement due)
- etc. per active domain

### Digest Mode
- User sets: real-time, hourly digest, daily digest, weekly summary
- Digest aggregates all "Normal" and "Low" events into single email
- Smart digest: skip digest if zero events since last send
- Digest time: user sets preferred send time (e.g. 09:00 in their timezone)
- Daily digest includes: summary table by domain, click-through links to each item

### Quiet Hours
- User sets quiet hours window (e.g. 22:00–07:00)
- Push and SMS suppressed during quiet hours
- In-app notifications still created (visible when user opens app)
- Email still sends unless digest mode is on (digest respects next-morning window)
- Override: Critical priority ignores quiet hours

### Team / Role Defaults
- Admin sets org-wide defaults per role (e.g. "Finance Managers receive all Finance events via email")
- New user inherits defaults on first login
- User can override any non-critical default

### Notification History
- Last 90 days of all sent notifications (per channel)
- Status: Sent / Delivered / Read / Failed
- Re-send option for failed notifications
- Clear all / mark all read for in-app bell

### Mention & Watch
- @mention in comments → direct notification to mentioned user (ignores quiet hours, respects channel prefs)
- Watch a record (task, deal, project) → get notifications for any activity on that record
- Unwatch at any time

---

## Data Model

```erDiagram
    notification_preferences {
        ulid id PK
        ulid user_id FK
        ulid company_id FK
        string event_type
        string channel
        boolean enabled
        string delivery_mode
        time digest_time
        string timezone
    }

    notification_quiet_hours {
        ulid id PK
        ulid user_id FK
        time start_time
        time end_time
        string timezone
        json days_of_week
    }

    notification_log {
        ulid id PK
        ulid company_id FK
        ulid user_id FK
        string event_type
        string channel
        string status
        json payload
        timestamp sent_at
        timestamp read_at
    }

    notification_watches {
        ulid id PK
        ulid user_id FK
        string watchable_type
        ulid watchable_id
        timestamp created_at
    }
```

---

## Implementation Notes

```php
// NotificationRouter — dispatched by all domain events
class NotificationRouter
{
    public function route(Notification $notification, User $user): void
    {
        $prefs = $this->prefsFor($user, $notification->eventType());

        foreach ($prefs->enabledChannels() as $channel) {
            if ($this->isQuietHours($user) && !$notification->isCritical()) {
                $this->queueForAfterQuietHours($notification, $user, $channel);
                continue;
            }

            if ($prefs->isDigest($channel)) {
                DigestQueue::push($notification, $user, $channel);
                continue;
            }

            $channel->send($notification, $user);
        }
    }
}
```

All domain events must implement `NotifiableEvent` interface — enforces `eventType()`, `priority()`, and `toNotification(User $user): Notification`.

---

## Events

| Event | When | Consumed By |
|---|---|---|
| `NotificationPreferenceUpdated` | User changes prefs | Notification system (invalidate cache) |
| `DigestReady` | Scheduled job compiles digest | Notification system (send email) |
| `NotificationDeliveryFailed` | Channel send error | IT (alert for SMS/email service issues) |

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
- [[entity-user]] — preferences keyed per user
- All domain MOCs — every domain's events feed into this system
