---
type: gap
severity: medium
category: feature
status: open
color: "#F97316"
discovered: 2026-05-09
discovered_in: admin-panel-flowflex
last_updated: 2026-05-09
---

# Gap: PlatformAnnouncement "Send" Action Is a Stub

## Context

Discovered during Phase 0 audit. `PlatformAnnouncementResource` has a `send` table action that marks `sent_at = now()`, locking the announcement from further edits. However, the action dispatches no event, no queued job, and sends no notification.

## The Problem

Clicking "Send" in the admin panel marks the announcement as sent but nothing reaches any tenant. The `CompanyCreated` and `UserInvited` events exist in `Events/Foundation/`, demonstrating the event pattern is established — but no equivalent announcement dispatch exists.

**File:** `app/Filament/Admin/Resources/PlatformAnnouncementResource.php:109-114`

## Impact

- Admins believe announcements have been sent when they have not
- The only functional difference is the record is locked from editing

## Proposed Solution

On send:
1. Fire `PlatformAnnouncementSent` event
2. Queued job reads `target` (`all` or `company`) and dispatches in-app notifications to affected users via Laravel's notification system
3. Optionally: email notification for `severity: high` announcements

This requires the notification system (Core Platform, Phase 1). Mark as Phase 1 dependency.

## Links

- Source builder log: [[builder-log-admin-panel-flowflex]]
- Related: [[admin-panel-flowflex]], [[entity-company]]
