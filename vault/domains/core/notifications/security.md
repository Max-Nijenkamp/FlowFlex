---
domain: core
module: notifications
type: security
build-status: planned
status: unverified
color: "#4ADE80"
updated: 2026-06-20
---

# Notifications — Security

Parent: [[_module]]

## Permissions

None beyond authentication — every user manages their own inbox and preferences. There is no `view-any` concept.

## Per-user scope

A user only ever sees their own notifications (`notifiable_id` = the authenticated user) and their own `notification_preferences` row per type.

## Tenant channel isolation

The realtime broadcast fires on `company.{id}.notifications`; the channel authorization callback must confirm the subscribing user belongs to that `company_id`, so a notification for company A never reaches a company B user. See [[../../../security/tenancy-isolation]] and [[../../../architecture/websockets]].

## Consumed-event delivery

`NotifySubscriptionSuspendedListener` must deliver by mail even when the company is suspended and can no longer reach the panel — suspension mail must not require panel access. See [[../../../architecture/event-bus]].
