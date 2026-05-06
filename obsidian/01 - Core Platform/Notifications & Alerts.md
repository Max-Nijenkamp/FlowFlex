---
tags: [flowflex, core, notifications, alerts, phase/1]
domain: Core Platform
panel: workspace
color: "#2199C8"
status: built
last_updated: 2026-05-06
---

# Notifications & Alerts

The central notification hub. Every module dispatches notifications here. Users control how and where they receive them.

**Who uses it:** All users
**Filament Panel:** `workspace` (settings), in-app bell in all panels
**Depends on:** [[Authentication & Identity]]
**Build complexity:** Medium — 1 resource, 1 page, 2 tables

## Notification Channels

| Channel | Technology | Notes |
|---|---|---|
| In-app bell | Pusher/Soketi WebSockets | Unread count badge |
| Email | Resend / Mailgun | Templated, branded per workspace |
| Slack push | Webhook-based | Workspace-level Slack connection |
| Microsoft Teams push | Webhook-based | — |
| SMS | Twilio | Urgent alerts only |
| Browser push | Web Push API | Opt-in per user |
| Webhook | Custom URL | POST to configured endpoint |

## User Notification Preferences

- Per-user notification preferences per notification type
- Digest options: immediate, hourly, daily, weekly
- Quiet hours (e.g. 22:00–08:00 no notifications)
- Channel priority per notification type (e.g. "Invoice overdue → email + Slack, not SMS")
- Notification mute per record (mute notifications for specific project, ticket, etc.)

## Escalation Rules

- Escalation chains (if not acknowledged in N minutes, escalate to manager)
- Priority levels on notifications: Low / Normal / High / Critical
- Critical notifications bypass quiet hours
- Workspace-level defaults (HR admin sets what's default for all new users)

## Notification Types (by module)

Every module dispatches to this hub. Key notification types:

**HR:**
- Leave request submitted / approved / rejected
- Onboarding task overdue
- Payslip generated
- Performance review due
- Certification expiring

**Finance:**
- Invoice overdue
- Expense approved / rejected
- Payment received
- Budget threshold reached

**Projects:**
- Task assigned
- Task overdue
- Approval requested

**CRM:**
- Ticket assigned
- Deal stale
- Contract expiring

**Operations:**
- Stock below reorder point
- Field job completed

## Database Tables (2)

1. `notifications` — ULID PK, `notifiable_type`/`notifiable_id` morphs, `type`, `data` text, `read_at` nullable, `created_at`
2. `notification_preferences` — `tenant_id`, `notification_type`, `channels` json, `is_enabled` bool; unique on `[tenant_id, notification_type]`

## Implementation

### Base Class

`app/Notifications/FlowFlexNotification.php` — abstract base all FlowFlex notifications extend.
- Abstract methods: `notificationType()`, `toDatabase()`, `toMail()`
- `via()` reads `NotificationPreference` for the notifiable tenant; defaults to `['database']` when no preference exists
- Channels: `database`, `mail` (Slack, SMS, webhook wired in when integrations active)

### Concrete Notifications (built so far)

| Class | Type string | Channels |
|---|---|---|
| `ModuleToggledNotification` | `module.toggled` | database, mail |

### Notification Preferences UI

`app/Filament/Workspace/Pages/Settings/ManageNotificationPreferences.php`
- Per-type enable toggle + mail channel checkbox
- Upserts `notification_preferences` records on save

### In-app Bell

`WorkspacePanelProvider` — `.databaseNotifications()` enables Filament's native bell widget (reads `notifications` table, marks as read).

### Adding New Notifications (pattern)

```php
class MyNotification extends FlowFlexNotification
{
    public function notificationType(): string { return 'my.module.event'; }
    
    public function toDatabase(object $notifiable): array
    {
        return ['title' => '...', 'body' => '...', 'icon' => 'heroicon-o-...', 'color' => 'primary'];
    }
    
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)->subject('...')->line('...');
    }
}
```

## Related

- [[Authentication & Identity]]
- [[API & Integrations Layer]]
- [[Tech Stack]]
