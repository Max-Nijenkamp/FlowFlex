---
type: gap
severity: high
category: bug
status: resolved
color: "#F97316"
discovered: 2026-05-10
discovered_in: phase0-phase1-audit
last_updated: 2026-05-10
---

# Gap: NotificationLog model used wrong table name

## Context

Found during Phase 0+1 full audit. Migration `010001_create_notification_preferences_table.php` creates the table as `notification_log` (singular). Laravel's default table naming convention pluralises the model name to `notification_logs`.

## The Problem

Any query through `NotificationLog::` would throw `QueryException: relation "notification_logs" does not exist`. The `NotificationRouter` service writes to `NotificationLog` after every dispatched notification — this would crash silently in production on every notification send.

## Resolution ✅

Added `protected $table = 'notification_log';` to the `NotificationLog` model.

## Links

- Source builder log: [[core-platform-phase1]]
