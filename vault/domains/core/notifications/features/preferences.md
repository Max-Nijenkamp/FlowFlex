---
domain: core
module: notifications
feature: preferences
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-07-03
---

# Feature: Preferences

Parent: [[../_module]] · See [[../architecture]]

Per-user, per-type channel preferences.

- `NotificationPreferencesPage` (`/app`, custom Filament page) presents a matrix: notification type × channel (in-app / email) toggles.
- Saved via `UpdateNotificationPreferencesData` — see [[../api]].
- `NotificationPreferenceService::channelsFor(User, type)` resolves the enabled channels; every domain Notification's `via()` calls it, so email=off suppresses the mail channel while keeping in-app (and vice versa).
- Defaults: `in_app_enabled` and `email_enabled` both `true`.

## UI

- **Kind**: custom-page
- **Page**: `NotificationPreferencesPage` at `/app` (custom Filament page).
- **Layout**: a matrix — notification types (rows, grouped by domain) × channels (in-app / email) as toggle columns; a save button at the bottom.
- **Key interactions**: user toggles per-type / per-channel switches and saves; save submits `UpdateNotificationPreferencesData`.
- **States**: empty = defaults shown (all on) for a fresh user · loading = form skeleton while preferences load · error = validation on an unknown `notification_type`, or save failure toast · selected = a toggled row pending save (dirty-state indicator).
- **Gating**: authentication only — each user edits their own preferences; no `view-any` permission.

## Data

- Owns / writes: `notification_preferences` (one row per user × notification_type; `in_app_enabled`, `email_enabled`).
- Reads: only its own preference rows for the authenticated user.
- Cross-domain writes: none. The preference is later read by every domain's `FlowFlexNotification::via()` through `NotificationPreferenceService` — a read, not a cross-domain write. See [[../../../../security/data-ownership]].

## Relations

- Consumes: none.
- Feeds: `NotificationPreferenceService::channelsFor()` is read by every domain's Notification `via()` — a preference toggle universally suppresses that channel. No event emitted.
- Shared entity: `notification_preferences` (owned here); the channel-resolution service is the shared read surface.

## Test Checklist

### Unit
- [ ] `channelsFor()` returns only enabled channels; email=off yields in-app-only, both-off yields no channels
- [ ] `UpdateNotificationPreferencesData` rejects an unknown `notification_type`
- [ ] Fresh user with no rows resolves to the defaults (`in_app_enabled` and `email_enabled` both true)

### Feature (Pest)
- [ ] Saving preferences upserts one row per user × type and never touches another user's rows
- [ ] After email=off, a domain notification delivers in-app only (mail channel suppressed via `channelsFor()`)

### Livewire
- [ ] Preferences page saves toggles and persists `UpdateNotificationPreferencesData`
- [ ] A user only sees/edits their own preference rows (no `view-any`)
