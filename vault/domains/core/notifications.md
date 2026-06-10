---
type: module
domain: Core Platform
domain-key: core
panel: app
module-key: core.notifications
status: planned
priority: v1-core
depends-on: [foundation.panels, foundation.email, foundation.queues]
soft-depends: []
fires-events: []
consumes-events: [ModuleActivated, CompanySubscriptionSuspended, DSARRequestSubmitted]
patterns: [websockets, email]
tables: [notifications, notification_preferences]
permission-prefix: core.notifications
encrypted-fields: []
last-reviewed: 2026-06-10
color: "#4ADE80"
---

# Notifications

In-app notification inbox and email alert delivery for all FlowFlex domains. Every domain dispatches events; Notifications delivers them to the right users via the right channel. Always-free core module.

---

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | [[domains/foundation/filament-panels\|foundation.panels]] | bell renders in every panel |
| Hard | [[domains/foundation/email-setup\|foundation.email]] | email channel |
| Hard | [[domains/foundation/queue-workers\|foundation.queues]] | delivery on `notifications` queue |

---

## Core Features

- In-app notification inbox — bell icon in all panels, unread count badge
- Email notifications via Laravel's notification system (Mailpit in dev, configured SMTP in prod)
- Notification preferences per user: on/off per notification type, channel selection (in-app / email)
- Notification types delivered via events from other domains (leave approved, invoice paid, deal won, etc.) — each domain ships its own Notification classes; this module provides infrastructure + inbox
- Mark as read, mark all as read, delete
- Notification grouping by domain — HR / Finance / CRM tabs in inbox
- Real-time badge update via Reverb (`company.{id}.notifications` channel, `NotificationCreated` broadcast — [[architecture/websockets]]; bell = the only always-on broadcast use case)

---

## Data Model

### notifications (Laravel standard, extended)

| Column | Type | Notes |
|---|---|---|
| id | uuid | PK (framework convention) |
| notifiable_type / notifiable_id | string / ulid | target user |
| type | string | notification class |
| data | jsonb | title, body, action_url, domain |
| read_at | timestamp nullable | |
| company_id | ulid, indexed | added column |
| created_at | timestamp | |

### notification_preferences

| Column | Type | Notes |
|---|---|---|
| id | ulid | PK |
| company_id | ulid, indexed | |
| user_id | ulid FK | |
| notification_type | string | class or type key |
| in_app_enabled | boolean default true | |
| email_enabled | boolean default true | |

**Indexes:** `(user_id, notification_type)` unique

---

## DTOs

### UpdateNotificationPreferencesData (input)
| Field | Type | Validation |
|---|---|---|
| preferences | array<{notification_type: string, in_app_enabled: bool, email_enabled: bool}> | types in known registry |

## Services & Actions

- `NotificationPreferenceService::channelsFor(User $user, string $type): array` — resolves enabled channels; every domain Notification's `via()` calls this
- `MarkAllReadAction::run(User $user): void`
- Base class `FlowFlexNotification` (abstract): enforces `company_id` in payload, broadcast on the company notifications channel, queued on `notifications`

## Events

### Consumes: ModuleActivated / CompanySubscriptionSuspended / DSARRequestSubmitted
Listeners notify owner/admins per [[architecture/event-bus]] contracts (suspension mail must not require panel access).

---

## Filament

**Nav group:** (global chrome + Settings)

| Artifact | Kind ([[architecture/ui-strategy]] row) | Notes |
|---|---|---|
| Bell + slide-out panel (all panels) | #10 render hook + Livewire | Reverb badge updates |
| `NotificationPreferencesPage` (`/app`) | #7 custom page (form) | matrix: type × channel toggles |

---

## Permissions

None beyond auth — every user manages own inbox/preferences. (No `view-any` concept.)

---

## Search & Realtime

Realtime: Reverb broadcast `notification.created` on `company.{id}.notifications` (ui-strategy row #10). No search index.

---

## Test Checklist

- [ ] Tenant isolation: notification for company A user never lands on company B channel/user
- [ ] Preference email=off suppresses mail channel, keeps in-app (and vice versa)
- [ ] Unread count correct after mark-read / mark-all-read
- [ ] Broadcast event emitted on channel `company.{id}.notifications` on create
- [ ] Consumed events (ModuleActivated etc.) generate owner notifications per contract
- [ ] All notifications queued on `notifications` queue

---

## Build Manifest

```
database/migrations/xxxx_create_notifications_table.php
database/migrations/xxxx_create_notification_preferences_table.php
app/Models/Core/NotificationPreference.php
app/Support/Notifications/FlowFlexNotification.php
app/Services/Core/NotificationPreferenceService.php
app/Actions/Core/MarkAllReadAction.php
app/Data/Core/UpdateNotificationPreferencesData.php
app/Listeners/Core/{NotifyModuleActivatedListener,NotifySubscriptionSuspendedListener,NotifyDsarSubmittedListener}.php
app/Events/Core/NotificationCreated.php (ShouldBroadcast)
app/Livewire/NotificationBell.php + blade + render hook registration
app/Filament/App/Pages/NotificationPreferencesPage.php
database/factories/Core/NotificationPreferenceFactory.php
tests/Feature/Core/{NotificationDeliveryTest,NotificationPreferencesTest,NotificationBroadcastTest}.php
```

---

## Related

- [[architecture/websockets]] — channel + broadcast pattern
- [[architecture/event-bus]] — consumed event contracts
- [[domains/foundation/email-setup]]
