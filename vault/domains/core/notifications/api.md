---
domain: core
module: notifications
type: api
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Notifications — API (DTOs, Events)

Parent: [[_module]] · See also [[architecture]]

Fires no cross-domain events (only the internal `NotificationCreated` broadcast). Provides one DTO and consumes three events.

## DTOs

### UpdateNotificationPreferencesData (input)

| Field | Type | Validation |
|---|---|---|
| preferences | array<{notification_type: string, in_app_enabled: bool, email_enabled: bool}> | each `notification_type` in the known registry |

## Consumes

| Event | Source | Listener |
|---|---|---|
| `ModuleActivated` | [[../billing-engine/_module]] | `NotifyModuleActivatedListener` |
| `CompanySubscriptionSuspended` | [[../billing-engine/_module]] | `NotifySubscriptionSuspendedListener` |
| `DSARRequestSubmitted` | data-lifecycle | *(listener NOT built — see [[unknowns]])* |

Contracts: [[../../../architecture/event-bus]].

## Broadcast

`NotificationCreated` (`ShouldBroadcast`) on channel `company.{id}.notifications`. Internal realtime signal for the in-app bell, not a cross-domain event. See [[../../../architecture/websockets]].
