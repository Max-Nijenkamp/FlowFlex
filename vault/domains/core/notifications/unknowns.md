---
domain: core
module: notifications
type: unknown
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Notifications — Unknowns / UNVERIFIED

Parent: [[_module]]

## In-app bell was NOT built as spec'd

> [!warning] UNVERIFIED — needs confirmation: spec's NotificationBell.php was NOT built; in-app bell uses Filament ->databaseNotifications() + ->databaseNotificationsPolling('30s'); ⌘K search is the separate app/Livewire/Spotlight.php (see [[../spotlight/_module]]).

The spec's Build Manifest listed `app/Livewire/NotificationBell.php + blade + render hook registration`. That component was **removed** from the manifest above — the bell is Filament's built-in database notifications with 30s polling, registered per panel.

## DSAR listener was NOT built

> [!warning] UNVERIFIED — needs confirmation
> `NotifyDsarSubmittedListener` was NOT built (spec-listed, not present). `DSARRequestSubmitted` is still declared as a consumed event in the spec frontmatter, but no listener exists — activating a real DSAR notification will require building it.

## `*(assumed)*` markers carried from spec

- Notification-type registry drives which `notification_type` values are valid in `UpdateNotificationPreferencesData` *(assumed)* — exact registry source not documented.
