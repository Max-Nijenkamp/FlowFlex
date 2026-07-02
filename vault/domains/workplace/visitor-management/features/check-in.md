---
domain: workplace
module: visitor-management
feature: check-in
type: feature
build-status: planned
status: wip
color: "#4ADE80"
updated: 2026-06-20
---

# Check-in & Kiosk

Visitor signs in at a kiosk (or reception), gets a badge, the host is notified, and an optional NDA is captured.

## Behaviour

1. Visitor is looked up among today's expected (by name) or filled in as a walk-in.
2. If the NDA toggle is on, `declaration_accepted_at` must be stamped before check-in proceeds (hard gate).
3. `VisitorService::checkIn` assigns a `badge_number`, stamps `checked_in_at`, dispatches `GenerateVisitorBadgeJob` (PDF badge), and notifies the host in-app + `VisitorArrivedMail`.
4. Check-out later via `CheckOutAction` (`checked_out_at`).

## UI

- **Kind**: custom-page (kiosk) + optional public-vue tablet screen
- **Page**: `VisitorKioskPage` — "Visitor Kiosk" (`/workplace/kiosk`), kiosk-role device session; optional Vue+Inertia reception tablet behind the same scoped guard.
- **Layout**: full-screen kiosk — big search box (name), walk-in fallback form, NDA checkbox + text, "Check in" button; badge print/confirmation screen after.
- **Key interactions**: type name → match expected → confirm → badge assigned + host pinged; walk-in path fills fields inline. Lookup + check-in are **rate-limited** per device/IP.
- **States**: empty (no match → "not expected? sign in as a walk-in") · loading (checking in) · error (declaration required / rate-limited toast) · selected (matched visitor card shown).
- **Gating**: `workplace.visitors.kiosk`.

## Data

- Owns / writes: `wp_visitors` only (`checked_in_at`, `checked_out_at`, `badge_number`, `declaration_accepted_at`).
- Reads: today's expected `wp_visitors` (own module, decrypted in memory *(assumed)*); `hr.profiles` for the host.
- Cross-domain writes: none — host notification via `core.notifications`, mail via `foundation.email` ([[../../../../security/data-ownership]]).

> [!warning] UNVERIFIED
> Whether a true public-vue self-check-in page (scoped guest guard) is built for v1, or the kiosk-role device session suffices, is undecided — see [[../unknowns]]. Watchlist screening on check-in is a competitor gap ([[../_opportunities]]) not yet scoped.

## Relations

- Consumes: expected visitors from [[pre-registration]].
- Feeds: host arrival notification; a `VisitorArrived` cross-domain event is *(assumed)* / undecided ([[../unknowns]]).
- Shared entity: `hr_employees` (host) — owned by [[../../../hr/employee-profiles/_module|hr.profiles]], read-only.

## Test Checklist

### Unit
- [ ] Declaration gate: when the NDA toggle is on, check-in is blocked until `declaration_accepted_at` is stamped.
- [ ] Badge-number assignment is unique within the company/day.

### Feature (Pest)
- [ ] Check-in assigns a badge, stamps `checked_in_at`, dispatches `GenerateVisitorBadgeJob`, notifies the host (in-app + `VisitorArrivedMail`).
- [ ] Walk-in check-in without pre-registration works.
- [ ] **Concurrent check-in**: two kiosk submissions for the same expected visitor produce a single check-in / badge, not two (row guard, [[../architecture#Concurrency]]).
- [ ] Check-out stamps `checked_out_at`.

### Livewire
- [ ] `VisitorKioskPage` requires `workplace.visitors.kiosk`; lookup + check-in are rate-limited (device/IP + panel-action).
- [ ] No match shows the walk-in fallback; declaration-required / rate-limited surfaces the correct toast.

## Related

- [[../_module|Visitor Management]] · [[pre-registration]] · [[visitor-log]] · [[../security]]
