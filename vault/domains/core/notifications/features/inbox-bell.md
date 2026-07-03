---
domain: core
module: notifications
feature: inbox-bell
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Inbox Bell

Parent: [[../_module]] · See [[../architecture]]

The in-app notification bell in every panel.

- Rendered by Filament's built-in `->databaseNotifications()` with `->databaseNotificationsPolling('30s')` — **not** a custom Livewire `NotificationBell.php` (see [[../unknowns]]).
- Shows an unread badge and a slide-out list; supports mark-as-read, mark-all-read (`MarkAllReadAction`), and delete.
- Notifications are grouped by domain (HR / Finance / CRM) in the inbox.
- The ⌘K command palette is a separate component (`app/Livewire/Spotlight.php`, [[../spotlight/_module]]), not part of the bell.

## UI

- **Kind**: widget
- **Page**: topbar bell in every Filament panel — Filament's built-in `->databaseNotifications()` + `->databaseNotificationsPolling('30s')` (not a custom page/route).
- **Layout**: bell icon in the panel topbar with an unread-count badge; click opens a slide-out list grouped by domain (HR / Finance / CRM), each item showing title, body, timestamp, and optional action link.
- **Key interactions**: open the panel, click a notification (mark read + follow action_url), mark-as-read per item, mark-all-read (`MarkAllReadAction`), delete.
- **States**: empty = "You're all caught up" · loading = brief poll refresh · error = list load failure shows an inline retry · selected = an item marked read (badge decrements live via the Reverb broadcast + 30s poll).
- **Gating**: authentication only — every user manages their own inbox; no `view-any` permission (see [[../security]]).

## Data

- Owns / writes: `notifications` (read_at on mark-read; delete). No other tables.
- Reads: only its own `notifications` rows scoped to the authenticated `notifiable_id` + `company_id`.
- Cross-domain writes: none — inbound notifications are created by this module's own listeners/`FlowFlexNotification`, never by other domains writing here. See [[../../../../security/data-ownership]].

## Relations

- Consumes: `NotificationCreated` broadcast (internal) → live badge update; upstream domain events land via this module's listeners (see [[realtime-broadcast]]).
- Feeds: none (terminal UI surface).
- Shared entity: `notifications` table (Laravel-standard, extended with `company_id`) — owned here.

## Test Checklist

### Unit
- [ ] Unread-count query returns only rows with `read_at` null for the authenticated `notifiable_id`
- [ ] Domain grouping buckets notifications by their source domain

### Feature (Pest)
- [ ] `MarkAllReadAction` sets `read_at` on every unread row for the user and none for other users
- [ ] Company A user cannot mark-read / delete a company B notification (scoped to `notifiable_id` + `company_id`)
- [ ] Marking one item read decrements the unread count; delete removes it from the list

### Livewire
- [ ] Bell renders unread badge; opening the panel and clicking an item marks it read and follows `action_url`
- [ ] Empty state shows "You're all caught up" when no unread notifications
