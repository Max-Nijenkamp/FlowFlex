---
domain: core
module: notifications
type: module
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Notifications

In-app notification inbox and email alert delivery for all FlowFlex domains. Every domain dispatches events; Notifications provides the infrastructure — a base notification class, per-user preferences, an in-app bell, and a realtime broadcast — that delivers them to the right users on the right channel. Always-free core module.

## Module-key

`core.notifications`

**Priority:** v1-core  
**Panel:** app (bell renders in every panel)  
**Permission prefix:** `core.notifications` (no permissions — per-user inbox, auth only)  
**Tables:** `notifications`, `notification_preferences`  
**Events:** fires none · consumes `ModuleActivated`, `CompanySubscriptionSuspended`, `DSARRequestSubmitted` · broadcasts `NotificationCreated` on `company.{id}.notifications`

## Sibling notes

- [[architecture]] — base class, preference service, listeners, broadcast + flow
- [[data-model]] — `notifications`, `notification_preferences` + ERD
- [[api]] — `UpdateNotificationPreferencesData`, consumed-events list
- [[security]] — per-user inbox, tenant channel isolation
- [[unknowns]] — UNVERIFIED Build-Manifest items
- Features: [[features/inbox-bell]] · [[features/preferences]] · [[features/realtime-broadcast]]

## Dependencies

| Type | Module | Why |
|---|---|---|
| Hard | foundation.panels | bell renders in every panel |
| Hard | foundation.email | email channel |
| Hard | foundation.queues | delivery on `notifications` queue |
| Soft | [[../billing-engine/_module]] | source of `ModuleActivated` / `CompanySubscriptionSuspended` |

## Core Features

- In-app notification bell in all panels via Filament's built-in `->databaseNotifications()` with `->databaseNotificationsPolling('30s')` — unread badge, slide-out list (**not** a custom `NotificationBell.php`; see [[unknowns]])
- Email notifications via Laravel's notification system (Mailpit in dev, configured SMTP in prod)
- Per-user preferences: on/off per notification type, channel selection (in-app / email)
- Notification types delivered via events from other domains — each domain ships its own Notification classes; this module provides the infrastructure + inbox
- Mark as read, mark all as read, delete
- Notification grouping by domain (HR / Finance / CRM) in the inbox
- Realtime badge update via Reverb (`NotificationCreated` broadcast on `company.{id}.notifications`) — see [[../../../architecture/websockets]]

## Test Checklist

- [ ] Tenant isolation: a notification for company A never lands on company B's channel/user
- [ ] Module gating: n/a (platform module, always active — always-free core)
- [ ] Preference email=off suppresses mail channel, keeps in-app (and vice versa)
- [ ] Unread count correct after mark-read / mark-all-read
- [ ] `NotificationCreated` broadcast emitted on `company.{id}.notifications` on create
- [ ] Consumed events (`ModuleActivated` etc.) generate owner notifications per contract
- [ ] All notifications queued on `notifications` queue

## Build Manifest (corrected to flat paths)

```
database/migrations/xxxx_create_notifications_table.php
database/migrations/xxxx_create_notification_preferences_table.php
app/Models/NotificationPreference.php
app/Support/Notifications/FlowFlexNotification.php
app/Services/NotificationPreferenceService.php
app/Actions/MarkAllReadAction.php
app/Data/UpdateNotificationPreferencesData.php
app/Listeners/{NotifyModuleActivatedListener,NotifySubscriptionSuspendedListener}.php
app/Events/NotificationCreated.php (ShouldBroadcast)
app/Filament/App/Pages/NotificationPreferencesPage.php
database/factories/NotificationPreferenceFactory.php
tests/Feature/Core/{NotificationDeliveryTest,NotificationPreferencesTest,NotificationBroadcastTest}.php
```

> [!note]
> Spec listed `app/Models/Core/...`, `app/Services/Core/...`, `app/Actions/Core/...`, `app/Data/Core/...`, `app/Listeners/Core/...`, `app/Events/Core/...`, and `database/factories/Core/...`; real layout is flat — corrected above. `tests/Feature/Core/...` kept as-is.
>
> **Two corrections vs the flat spec** (see [[unknowns]]): the spec's `app/Livewire/NotificationBell.php + blade + render hook` was **removed** — the bell is Filament's built-in `->databaseNotifications()` + `->databaseNotificationsPolling('30s')`, and the ⌘K palette is the separate `app/Livewire/Spotlight.php`. The spec's `NotifyDsarSubmittedListener` was **removed** — it was not built.

## Cross-Domain Edges

| Direction | Event | Other module | Effect |
|---|---|---|---|
| consumes | `ModuleActivated` | core.billing-engine | `NotifyModuleActivatedListener` notifies owner/admins |
| consumes | `CompanySubscriptionSuspended` | core.billing-engine | `NotifySubscriptionSuspendedListener` mails the owner (not panel-gated) |
| consumes | `DSARRequestSubmitted` | data-lifecycle | listener **NOT built** — consumed on paper only (see [[unknowns]]) |
| fires | none | — | only the internal `NotificationCreated` broadcast (client-side, not cross-domain) |

Data ownership: notifications owns and writes only `notifications` and `notification_preferences`; it reacts to other domains' events by writing **its own** tables (never theirs), reads user/company identity read-only, and effects other domains via no events ([[../../../security/data-ownership]]).

## Related

- [[../../../architecture/websockets]] — channel + broadcast pattern
- [[../../../architecture/event-bus]] — consumed event contracts
- [[../billing-engine/_module]] · [[../spotlight/_module]]
- [[../../../decisions/decision-2026-06-20-full-mapping-conventions]] · [[../../../security/data-ownership]]
- [[../../../infrastructure/mail]]
